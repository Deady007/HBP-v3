<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController; // Add this import
use App\Models\User;
use App\Models\Patient; // Change to Patient model
use App\Models\AuditLog; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Add this import
use Illuminate\Support\Facades\Log; // Add this import
use Illuminate\Support\Facades\Auth; // Add this import
use Illuminate\Support\Facades\Mail; // Add this import
use App\DataTables\PatientDataTable; // Add this import
use Yajra\DataTables\Facades\DataTables; // Add this import


class PatientController extends Controller
{         
    function __construct()
    {
        $this->middleware('permission:patient-list|patient-create|patient-edit|patient-delete', ['only' => ['index','show']]);
        $this->middleware('permission:patient-create', ['only' => ['create','store']]);
        $this->middleware('permission:patient-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:patient-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('patient.index');
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $patients = Patient::where('full_name', 'like', "%$search%")->paginate(10);
        return view('patient.list', compact('patients'));
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patient.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patient.edit', compact('patient'));
    }

    public function create()
    {
        return view('patient.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['full_name'] = $request->input('Full_name');
        $user = Auth::user();

        if ($user) {
            $data['user_unique_id'] = $user->id;

            // Generate a unique ID for the patient
            $currentYear = date('Y');
            $latestPatient = Patient::whereYear('created_at', $currentYear)->orderBy('id', 'desc')->first();
            $latestId = $latestPatient ? intval(substr($latestPatient->pat_unique_id, -4)) : 0;
            $newId = str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);
            $data['pat_unique_id'] = "PAT-{$currentYear}-{$newId}";
        } else {
            return redirect()->back()->withErrors(['error' => 'User not authenticated']);
        }

        $patient = Patient::create($data);

        // Log the creation in the audit log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Created a new patient: ' . $patient->full_name,
        ]);
        
        Log::info('Audit log created for patient creation', ['user_id' => auth()->id(), 'action' => 'create']);

        // Send email to the patient
        Mail::to($patient->email)->send(new \App\Mail\PatientCreatedMail($patient, $user));

        // Send email to the creator
        Mail::to($user->email)->send(new \App\Mail\PatientCreatedMail($patient, $user));

        return redirect()->route('admin.patient.index')->with('success', 'Patient created successfully.');
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $data = $request->all();
        $data['full_name'] = $request->input('Full_name');
        $user = User::findOrFail($request->input('user_id'));
        $data['user_unique_id'] = $user->id; // Fetch the id field from the User table
        $data['pat_unique_id'] = $patient->pat_unique_id;
        $patient->update($data);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Updated patient: ' . $patient->full_name . ' with fields: ' . implode(', ', array_keys($data)),
        ]);
        Log::info('Audit log created for patient update', ['user_id' => auth()->id(), 'action' => 'update']);
        return redirect()->route('admin.patient.show', $id)->with('success', 'Patient updated successfully.');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patientName = $patient->full_name;
        $patient->delete();
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Deleted patient: ' . $patientName,
        ]);
        Log::info('Audit log created for patient deletion', ['user_id' => auth()->id(), 'action' => 'delete']);
        return redirect()->route('admin.patient.index')->with('success', 'Patient deleted successfully.');
    }

    public function profile()
    {
        $patient = Patient::findOrFail(auth()->guard()->user()->id);
        return view('patient.profile', compact('patient'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'date_of_birth' => 'required|date',
            'phone_number' => 'required|string|max:15',
            'email' => 'nullable|string|email|max:255,email,' . auth()->guard()->user()->id,
            'full_address' => 'required|string',
            'religion' => 'required|string',
            'economic_status' => 'required|string',
            'bpl_card_number' => 'nullable|string|max:255',
            'ayushman_card' => 'required|boolean',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:15',
            'emergency_contact_relationship' => 'required|string',
            'age_category' => 'required|string',
        ]);

        $patient = Patient::findOrFail(auth()->guard()->user()->id);
        $patient->update($request->all());

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully.');
    }

   

    public function getData(Request $request)
    {
        if(Auth::user()->hasRole('Admin')){
            $patients = Patient::query();
        } else {
            $patients = Patient::where('user_unique_id', auth()->id());
        }
        return DataTables::of($patients)
            ->addColumn('actions', function ($patient) {
                return view('patient.partials.actions', compact('patient'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
