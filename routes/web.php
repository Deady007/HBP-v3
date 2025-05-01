<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController, RoleController, UserController, ProductController, MedicalVisitController, RequestForVisitController, AdminDashboardController, UserDashboardController, DoctorDashboardController, NurseDashboardController};
use App\Http\Controllers\Admin\PatientController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\DoctorReportController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Artisan;

Route::view('/', 'Homepage.welcome')->name('welcome');
Route::view('/about-us', 'Homepage.about')->name('about-us');
Route::view('/services', 'Homepage.services')->name('services');

// Services Routes
foreach (range(1, 5) as $num) {
    Route::view("/services/service{$num}", "Services.Service{$num}")->name("services.service{$num}");
}

Route::get('/about-details', function () {
    return view('about-details');
})->name('about.details');

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function() {
    Route::resources([
        'roles' => RoleController::class,
        'users' => UserController::class,
        'medical_visit' => MedicalVisitController::class,
        'request_for_visit' => RequestForVisitController::class,
    ]);
    
    Route::patch('/medical_visit/{id}/approve', [MedicalVisitController::class, 'approve'])->name('medical_visit.approve');
    Route::patch('/medical_visit/{id}/reject', [MedicalVisitController::class, 'reject'])->name('medical_visit.reject');
    Route::patch('/medical_visit/{id}/update_status', [MedicalVisitController::class, 'updateStatus'])->name('medical_visit.update_status');
    Route::patch('/medical_visit/{id}/reschedule', [MedicalVisitController::class, 'reschedule'])->name('medical_visit.reschedule');
    Route::get('/calendar', [MedicalVisitController::class, 'calendar'])->name('calendar');
    Route::get('/medical-visit/details/{id}', [MedicalVisitController::class, 'getVisitDetails'])->name('medical_visit.details');
    Route::get('/medical_visits/data', [MedicalVisitController::class, 'getData'])->name('medical_visits.data'); // Corrected route definition

    // Dashboard Routes
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    Route::get('/nurse/dashboard', [NurseDashboardController::class, 'index'])->name('nurse.dashboard');

    Route::get('/doctor/report', [DoctorReportController::class, 'generateLoggedInDoctorReport'])->name('doctor.report.loggedin');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function() {
    Route::resource('patient', PatientController::class);
    Route::post('patient/{id}/approve', [PatientController::class, 'approve'])->name('patient.approve');
    Route::put('patient/{id}/storePatientData', [PatientController::class, 'storePatientData'])->name('patient.storePatientData');
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/report', [AdminReportController::class, 'generateReport'])->name('report');
    Route::get('patients/data', [PatientController::class, 'getData'])->name('patient.getData');
});

// Patient Routes
Route::prefix('patient')->name('patient.')->middleware(['auth'])->group(function() {
    Route::get('dashboard', [HomeController::class, 'patientIndex'])->name('dashboard');
    Route::get('profile', [PatientController::class, 'profile'])->name('profile');
    Route::post('profile', [PatientController::class, 'updateProfile'])->name('profile.update');
    Route::get('data', [PatientController::class, 'getData'])->name('patient.data'); // Ensure this line is correct
});

// API Route
Route::get('/api/users-with-role/{role}', [UserController::class, 'getUsersWithRole']);
Route::get('/api/users-with-role/{role}', [RequestForVisitController::class, 'fetchUsersWithRole']);

// Report Routes
Route::get('/doctor/report/{doctorId}', [DoctorReportController::class, 'generateReport'])->name('doctor.report');
Route::get('/user/report', [UserReportController::class, 'generateReport'])->name('reports.user');
Route::get('/reports/admin', [AdminReportController::class, 'generateReport'])->name('reports.admin');
Route::get('/reports/export', [UserReportController::class, 'exportReport'])->name('reports.export');
Route::get('/reports/export/csv', [UserReportController::class, 'exportReportCsv'])->name('reports.export.csv');
Route::get('/doctor-report/export/{doctorId}', [DoctorReportController::class, 'exportReport'])->name('doctor.report.export');
Route::get('/doctor-report/export', [DoctorReportController::class, 'exportLoggedInDoctorReport'])->name('doctor.report.export.loggedin');
Route::get('/admin-report/export', [AdminReportController::class, 'exportAdminReport'])->name('admin.report.export');
Route::get('/admin-report/export/csv', [AdminReportController::class, 'exportAdminReportCsv'])->name('admin.report.export.csv');
Route::get('/doctor-report/export/csv/{doctorId}', [DoctorReportController::class, 'exportReportCsv'])->name('doctor.report.export.csv');
Route::get('/doctor-report/export/csv', [DoctorReportController::class, 'exportLoggedInDoctorReportCsv'])->name('doctor.report.export.loggedin.csv');
Route::get('/admin-report/export/pdf', [AdminReportController::class, 'exportAdminReportPdf'])->name('admin.report.export.pdf');
Route::get('/doctor-report/export/pdf/{doctorId}', [DoctorReportController::class, 'exportReportPdf'])->name('doctor.report.export.pdf');
Route::get('/user-report/export/pdf', [UserReportController::class, 'exportReportPdf'])->name('reports.export.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::get('/messages/{receiverId}', [MessageController::class, 'fetchMessages']);
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
    Route::post('/schedule-visit', [App\Http\Controllers\VisitController::class, 'scheduleVisit'])->name('schedule.visit');
});

Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'));
});

Route::get('/service-worker.js', function () {
    return response()->file(public_path('service-worker.js'));
})->name('service-worker');

Route::get('/test-email', [App\Http\Controllers\TestEmailController::class, 'sendTestEmail']);
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/migrate-now', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return '✅ Migration ran successfully!';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});


