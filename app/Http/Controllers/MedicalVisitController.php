<?php

namespace App\Http\Controllers;

use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        $query = MedicalVisit::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('pat_unique_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            })
            ->orWhereHas('doctor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('nurse', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('visit_date', 'like', "%{$search}%")
            ->orWhere('is_approved', 'like', "%{$search}%");
        }

        $medicalVisits = $query->paginate(10);

        return view('medical_visit.index', compact('medicalVisits'));
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
            'preferred_visit_date' => 'required|date',
            'preferred_time_slot' => 'required|string',
        ]);
        $symptoms = $request->input('symptoms', []);
        $symptomsString = implode(', ', $symptoms);
        $medicalVisit = new MedicalVisit();
        $medicalVisit->patient_id = $request->patient_id;
        // Removed visit_date assignment
        $medicalVisit->appointment_type = $request->appointment_type;
        $medicalVisit->primary_complaint = $request->primary_complaint;
        $medicalVisit->symptoms = $symptomsString;  
        $medicalVisit->is_emergency = $request->is_emergency ?? false;
        $medicalVisit->created_by = Auth::id();
        $medicalVisit->preferred_visit_date = $request->preferred_visit_date;
        $medicalVisit->preferred_time_slot = $request->preferred_time_slot;
        $medicalVisit->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Created a new medical visit for patient ID: ' . $request->patient_id,
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
        $visit->is_emergency = $request->is_emergency ?? false;
        $visit->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Updated medical visit for patient: ' . $visit->patient->full_name,
        ]);

        return redirect()->route('medical_visit.show', $visit->id)->with('success', 'Medical visit updated successfully.');
    }

    public function approve(Request $request, $id)
    {
        $visit = MedicalVisit::findOrFail($id);
        $visit->is_approved = 'Approved';
        $visit->time_slot = $request->input('time_slot');
        $visit->doctor_id = $request->input('doctor_id');
        $visit->nurse_id = $request->input('nurse_id');
        $visit->visit_date = Carbon::parse($request->input('visit_date'))->format('Y-m-d H:i:s');
        $visit->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve',
            'description' => 'Approved medical visit for patient: ' . $visit->patient->full_name,
        ]);

        return redirect()->route('medical_visit.index')->with('success', 'Medical visit approved successfully.');
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
            'user_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Deleted medical visit for patient: ' . $visit->patient->full_name,
        ]);

        return redirect()->route('medical_visit.index')->with('success', 'Medical visit deleted successfully.');
    }

    public function calendar()
    {
        $userId = Auth::id();
        $medicalVisits = MedicalVisit::where('created_by', $userId)
            ->orWhere('doctor_id', $userId)
            ->orWhere('nurse_id', $userId)
            ->with('patient')
            ->get();

        $events = $medicalVisits->map(function ($visit) {
            return [
                'title' => $visit->patient->full_name . ' - ' . $visit->patient->full_address,
                'start' => $visit->visit_date,
                'status' => $visit->is_approved,
                'backgroundColor' => $visit->is_approved === 'Approved' ? 'green' : 'yellow',
                'borderColor' => $visit->is_approved === 'Approved' ? 'green' : 'yellow'
            ];
        });

        return view('calendar', compact('events'));
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'visit_date' => 'required|date',
            'time_slot' => 'required',
        ]);

        $visit = MedicalVisit::findOrFail($id);
        $visit->visit_date = $request->input('visit_date');
        $visit->time_slot = $request->input('time_slot');
        $visit->save();

        return redirect()->route('medical_visit.index')->with('success', 'Visit rescheduled successfully.');
    }
}
