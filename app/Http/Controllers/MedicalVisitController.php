<?php

namespace App\Http\Controllers;

use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\VisitScheduleMail;
use Illuminate\Support\Facades\Mail;
use App\DataTables\MedicalVisitDataTable;
use Yajra\DataTables\Facades\DataTables;

class MedicalVisitController extends Controller
{
    
    function __construct()
    {
        $this->middleware('permission:medical-visit-list|medical-visit-create|medical-visit-edit|medical-visit-delete', ['only' => ['index','show']]);
        $this->middleware('permission:medical-visit-create', ['only' => ['create','store']]);
        $this->middleware('permission:medical-visit-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:medical-visit-delete', ['only' => ['destroy']]);
        $this->middleware('permission:medical-visit-reschedule', ['only' => ['reschedule']]);
        $this->middleware('permission:medical-visit-update-status', ['only' => ['updateStatus']]);
    }

    // Display a listing of the medical visits
    public function index(MedicalVisitDataTable $dataTable)
    {
        $doctors = User::role('Doctor')->get(); // Assuming you have a role 'Doctor'
        return $dataTable->render('medical_visit.index', compact('doctors'));
    }

    public function create()
    {
        $patients = Patient::all();
        return view('medical_visit.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_type' => 'required|string',
            'primary_complaint' => 'required|string',
            'symptoms' => 'nullable|array',
            'is_emergency' => 'boolean',
            'preferred_visit_date' => 'required|date|after:today',
            'preferred_time_slot' => 'required|string',
        ]);
        $patient = Patient::findOrFail($request->patient_id);
        $symptoms = $request->input('symptoms', []);
        $symptomsString = implode(', ', $symptoms);
        $medicalVisit = new MedicalVisit();
        $medicalVisit->patient_id = $request->patient_id;
        
        $medicalVisit->appointment_type = $request->appointment_type;
        $medicalVisit->primary_complaint = $request->primary_complaint;
        $medicalVisit->symptoms = $symptomsString;  
        $medicalVisit->is_emergency = $request->appointment_type === 'Emergency Visit';
        $medicalVisit->created_by = Auth::id();
        $medicalVisit->preferred_visit_date = $request->preferred_visit_date;
        $medicalVisit->preferred_time_slot = $request->preferred_time_slot;
        $medicalVisit->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'request',
            'description' => 'Requested a new medical visit for patient: ' . $patient->full_name . ' (ID: ' . $patient->id . ') on ' . $medicalVisit->preferred_visit_date . ' at ' . $medicalVisit->preferred_time_slot,
        ]);

        return redirect()->route('medical_visit.index')->with('success', 'Medical visit scheduled successfully.');
    }

    public function show($id)
    {
        $visit = MedicalVisit::with(['patient', 'doctor', 'nurse'])->findOrFail($id);
        return view('medical_visit.show', compact('visit'));
    }

    public function edit($id)
    {
        $visit = MedicalVisit::findOrFail($id);
        $patients = Patient::all();
        return view('medical_visit.edit', compact('visit', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'diagnosis' => 'nullable|string',
            'simplified_diagnosis' => 'nullable|string',
            'sugar_level' => 'nullable|string',
            'heart_rate' => 'nullable|string',
            'temperature' => 'nullable|string',
            'oxygen_level' => 'nullable|string',
            'ongoing_treatments' => 'nullable|string',
            'medications_prescribed' => 'nullable|string',
            'procedures' => 'nullable|string',
            'doctor_notes' => 'nullable|string',
            'nurse_observations' => 'nullable|string',
            'is_emergency' => 'boolean',
            'doctor_name' => 'required|string',
            'nurse_name' => 'required|string',
        ]);
        $symptoms = $request->input('symptoms', []);
        $symptomsString = implode(', ', $symptoms);
        $visit = MedicalVisit::findOrFail($id);
        $visit->update($request->all());
        $visit->symptoms = $symptomsString;
        $visit->is_emergency = $request->appointment_type === 'Emergency Visit';
        $visit->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => 'Updated medical visit (ID: ' . $visit->id . ') for patient: ' . $visit->patient->full_name . ' (ID: ' . $visit->patient->id . ')',
        ]);

        return redirect()->route('medical_visit.show', $visit->id)->with('success', 'Medical visit updated successfully.');
    }


    public function approve(Request $request, $id)
{
    $validatedData = $request->validate([
        'visit_date' => 'required|date',
        'time_slot' => 'required',
        'doctor_id' => 'required|exists:users,id',
        'nurse_id' => 'required|exists:users,id',
    ]);

    $visitDate = Carbon::parse($request->input('visit_date'))->format('Y-m-d');
    $timeSlot = $request->input('time_slot');
    $doctorId = $request->input('doctor_id');
    $nurseId = $request->input('nurse_id');

    // Check if a record exists where both the same doctor and nurse are scheduled for the same date & time
    $conflict = MedicalVisit::where('visit_date', $visitDate)
        ->where('time_slot', $timeSlot)
        ->where('doctor_id', $doctorId)
        ->where('nurse_id', $nurseId)
        ->exists();

    if ($conflict) {
        return redirect()->back()->with('error', 'The selected doctor and nurse are already assigned together for a visit at this time slot on this date.');
    }

    // If no conflicts, approve the visit
    $medicalVisit = MedicalVisit::findOrFail($id);
    $medicalVisit->is_approved = $medicalVisit->is_emergency ? 'Emergency Approved' : 'Approved';
    $medicalVisit->visit_date = $visitDate;
    $medicalVisit->time_slot = $timeSlot;
    $medicalVisit->doctor_id = $doctorId;
    $medicalVisit->nurse_id = $nurseId;
    $medicalVisit->save();

    Mail::to($medicalVisit->patient->email)->send(new VisitScheduleMail($medicalVisit));


    // Log the approval action
    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'approve',
        'description' => 'Approved medical visit for patient: ' . $medicalVisit->patient->full_name,
    ]);

    return redirect()->route('request_for_visit.index')->with('success', 'Medical visit approved successfully.');
}

    public function updateStatus(Request $request, $id)
    {
        $visit = MedicalVisit::findOrFail($id);
        $visit->medical_status = $request->input('medical_status');
        $visit->save();

        return redirect()->route('medical_visit.index')->with('success', 'Medical status updated successfully.');
    }

    public function destroy($id)
    {
        $visit = MedicalVisit::findOrFail($id);
        $visit->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => 'Deleted medical visit (ID: ' . $visit->id . ') for patient: ' . $visit->patient->full_name . ' (ID: ' . $visit->patient->id . ')',
        ]);

        return redirect()->route('medical_visit.index')->with('success', 'Medical visit deleted successfully.');
    }

    public function calendar()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) {
            $medicalVisits = MedicalVisit::with('patient')->get();
        } else {
            $userId = $user->id;
            $medicalVisits = MedicalVisit::where('created_by', $userId)
                ->orWhere('doctor_id', $userId)
                ->orWhere('nurse_id', $userId)
                ->with('patient')
                ->get();
        }

        $events = $medicalVisits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'title' => $visit->patient->full_name,
                // 'title' => $visit->patient->full_name . ' - ' . $visit->patient->full_address
                'start' => $visit->visit_date ?? $visit->preferred_visit_date,
                'status' => $visit->is_approved,
                'backgroundColor' => $visit->is_approved === 'Approved' ? 'green' : ($visit->is_approved === 'pending' ? 'orange' : 'yellow'),
                'borderColor' => $visit->is_approved === 'Approved' ? 'green' : ($visit->is_approved === 'pending' ? 'orange' : 'yellow')
            ];
        });

        return view('calendar', compact('events'));
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'visit_date' => 'required|date|after:today', // Ensure the date is after today
            'time_slot' => 'required',
        ]);

        $visit = MedicalVisit::findOrFail($id);
        $visit->visit_date = $request->input('visit_date');
        $visit->time_slot = $request->input('time_slot');
        $visit->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'reschedule',
            'description' => 'Rescheduled medical visit (ID: ' . $visit->id . ') for patient: ' . $visit->patient->full_name . ' (ID: ' . $visit->patient->id . ') to ' . $visit->visit_date . ' at ' . $visit->time_slot,
        ]);

         // Check if the request is AJAX
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Visit rescheduled successfully.',
            'visit_date' => $visit->visit_date,
            'time_slot' => $visit->time_slot,
            'redirect' => route('medical_visit.index'),
        ]);
    }

    // If normal form submission
    return redirect()->route('medical_visit.index')->with('success', 'Medical visit rescheduled successfully.');
    }

    public function getVisitDetails($id)
    {
        $visit = MedicalVisit::with(['patient', 'doctor', 'nurse'])->findOrFail($id);
        return response()->json($visit);
    }

    public function getData(Request $request)
    {
        $query = MedicalVisit::with(['patient', 'doctor', 'nurse'])
            ->select('medical_visits.*'); // Ensure the table name is correct
        return DataTables::eloquent($query)
            ->addColumn('action', function ($visit) {
                return view('medical_visit.action', compact('visit'))->render();
            })
            ->editColumn('is_approved', function ($visit) {
                return $visit->is_approved === 'Approved' ? 'Approved' : 'pending'; // Correctly map the value
            })
            ->toJson();
    }
}
