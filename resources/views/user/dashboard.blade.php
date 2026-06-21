@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4" style="color: #4e73df; font-weight: bold;">User Dashboard</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg transition-card">
                <div class="card-body" style="background-color: #f8f9fa; border-left: 5px solid #4e73df;">
                    <h5 class="card-title" style="color: #4e73df; font-weight: bold;">Total Patients</h5>
                    <p class="card-text" style="font-size: 1.2em;">{{ $totalPatients }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg transition-card">
                <div class="card-body" style="background-color: #f8f9fa; border-left: 5px solid #1cc88a;">
                    <h5 class="card-title" style="color: #1cc88a; font-weight: bold;">Total Medical Visits</h5>
                    <p class="card-text" style="font-size: 1.2em;">{{ $totalMedicalVisits }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg transition-card">
                <div class="card-body" style="background-color: #f8f9fa; border-left: 5px solid #36b9cc;">
                    <h5 class="card-title" style="color: #36b9cc; font-weight: bold;">Select Patient and Date Range</h5>
                    <form method="GET" action="{{ route('user.dashboard') }}">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <select name="patient_id" class="form-control" onchange="this.form.submit()" style="font-size: 1.1em;">
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ $selectedPatientId == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <select name="date_range" class="form-control" onchange="this.form.submit()" style="font-size: 1.1em;">
                                    <option value="day" {{ $dateRange == 'day' ? 'selected' : '' }}>Day</option>
                                    <option value="week" {{ $dateRange == 'week' ? 'selected' : '' }}>Week</option>
                                    <option value="month" {{ $dateRange == 'month' ? 'selected' : '' }}>Month</option>
                                    <option value="year" {{ $dateRange == 'year' ? 'selected' : '' }}>Year</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($selectedPatientId && !empty($vitalsData))
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg transition-card">
                <div class="card-body">
                    <h5 class="card-title" style="color: #f6c23e; font-weight: bold;">Vitals Graph for Patient: {{ $patients->find($selectedPatientId)->full_name }}</h5>
                    <canvas id="vitalsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @if($currentHealth)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg transition-card">
                <div class="card-body" style="background-color: #f8f9fa; border-left: 5px solid #e74a3b;">
                    <h5 class="card-title" style="color: #e74a3b; font-weight: bold;">Current Health Status</h5>
                    <ul style="font-size: 1.1em;">
                        <li><strong>Sugar Level:</strong> {{ $currentHealth['sugar_level'] ?? 'N/A' }}</li>
                        <li><strong>Heart Rate:</strong> {{ $currentHealth['heart_rate'] ?? 'N/A' }}</li>
                        <li><strong>Temperature:</strong> {{ $currentHealth['temperature'] ?? 'N/A' }}</li>
                        <li><strong>Oxygen Level:</strong> {{ $currentHealth['oxygen_level'] ?? 'N/A' }}</li>
                    </ul>
                    <p style="font-size: 1.2em; font-weight: bold; color: #4e73df;">
                        Health Status: 
                        <span style="color: 
                            {{ $healthStatus == 'Healthy' ? '#1cc88a' : 
                               ($healthStatus == 'Mild Concern' ? '#f6c23e' : 
                               ($healthStatus == 'Moderate Concern' ? '#fd7e14' : 
                               ($healthStatus == 'Severe Concern' ? '#e74a3b' : 
                               ($healthStatus == 'Critical' ? '#d9534f' : 
                               ($healthStatus == 'Too Sick' ? '#8b0000' : '#6c757d'))))) }};">
                            {{ $healthStatus }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>

<style>
    .transition-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .transition-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($selectedPatientId && !empty($vitalsData))
    var ctx = document.getElementById('vitalsChart').getContext('2d');
    var vitalsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {{ Js::from($vitalsData['periods']) }},
            datasets: [
                {
                    label: 'Sugar Level',
                    data: {{ Js::from($vitalsData['SugarLevel']) }},
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Heart Rate',
                    data: {{ Js::from($vitalsData['heartRate']) }},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Temperature',
                    data: {{ Js::from($vitalsData['temperature']) }},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Oxygen Level',
                    data: {{ Js::from($vitalsData['oxygen']) }},
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '{{ ucfirst($dateRange) }} View'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.2)'
                    }
                }
            }
        }
    });
    @endif
</script>
@endsection
