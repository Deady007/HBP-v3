<?php

namespace App\Http\Controllers;

use App\Models\MedicalVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NurseDashboardController extends Controller
{
    public function index(): View
    {
        $totalMedicalVisits = MedicalVisit::where('nurse_id', auth()->id())->count();

        return view('nurse.dashboard', compact('totalMedicalVisits'));
    }
}
