<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalVisit;
use App\Models\AuditLog; // Add this import
use App\Models\User; // Add this import
use Illuminate\Support\Facades\Auth; // Add this import
use Carbon\Carbon; // Add this import
use Yajra\DataTables\DataTables; // Add this import
use App\Models\Role; // Add this import

class RequestForVisitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:req-list|req-create|req-approve', ['only' => ['index','show']]);
        $this->middleware('permission:req-create', ['only' => ['create','store']]);
        $this->middleware('permission:req-approve', ['only' => ['approve']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MedicalVisit::with('patient')->where('is_approved', 'Pending')->get();
            return DataTables::of($data)
                ->addColumn('action', function($row){
                    $btn = '<button class="btn btn-primary" data-toggle="modal" data-target="#approveModal-' . $row->id . '">Approve</button>';
                    return $btn;
                })
                ->make(true);
        }

        $visits = MedicalVisit::with('patient')->get(); // Fetch visits for the view
        $doctors = User::whereHas('roles', function($query) {
            $query->where('name', 'doctor');
        })->get(['id', 'name']); // Fetch doctors

        $nurses = User::whereHas('roles', function($query) {
            $query->where('name', 'nurse');
        })->get(['id', 'name']); // Fetch nurses

        return view('request_for_visit.index', compact('visits', 'doctors', 'nurses')); // Pass $visits, $doctors, and $nurses to the view
    }

    public function create()
    {
        return view('request_for_visit.create');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            
            'visit_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_emergency' => 'boolean',
        ]);

        // Create a new medical visit
        $medicalVisit = MedicalVisit::create($validatedData);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => 'Scheduled a new appointment for patient: ' . $medicalVisit->patient_name,
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('request_for_visit.index')->with('success', 'Medical visit created successfully.');
    }

    public function approve(Request $request, $id)
    {
        $medicalVisit = MedicalVisit::findOrFail($id);
        $medicalVisit->is_approved = 'Approved';
        $medicalVisit->time_slot = $request->input('time_slot'); // Set the time slot
        $medicalVisit->doctor_id = $request->input('doctor_id');
        $medicalVisit->nurse_id = $request->input('nurse_id');
        $medicalVisit->visit_date = Carbon::parse($request->input('visit_date'))->format('Y-m-d H:i:s');
        if ($medicalVisit->is_emergency) {
            $medicalVisit->is_approved = 'Emergency Approved';
        }

        $medicalVisit->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve',
            'description' => 'Approved medical visit for patient: ' . $medicalVisit->patient->full_name,
        ]);

        return redirect()->route('request_for_visit.index')->with('success', 'Medical visit approved successfully.');
    }

    public function fetchUsersWithRole($role)
    {
        $users = User::whereHas('roles', function($query) use ($role) {
            $query->where('name', $role);
        })->get(['id', 'name']);

        return response()->json($users);
    }
}
