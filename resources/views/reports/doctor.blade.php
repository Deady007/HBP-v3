@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <style>
        /* Add smooth transitions for hover effects */
        .hover-effect {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
    </style>

    <h1 class="text-center mb-6 text-blue-600 font-bold text-2xl">Detailed Medical Visit Report</h1>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-blue-600 font-bold text-xl mb-4">Doctor Information</h3>
        <div class="p-4 bg-gradient-to-r from-blue-100 to-blue-300 rounded-lg shadow-md flex justify-between items-center">
            <p class="text-gray-800 font-semibold"><strong>Doctor Name:</strong> <span class="text-blue-700">{{ $doctor->name ?? 'N/A' }}</span></p>
            <p class="text-gray-800 font-semibold"><strong>Doctor ID:</strong> <span class="text-blue-700">{{ $doctor->id ?? 'N/A' }}</span></p>
            <p class="text-gray-800 font-semibold"><strong>Specialization:</strong> <span class="text-blue-700">{{ $doctor->specialty ?? 'N/A' }}</span></p>
        </div>

        @php
            $startDate = $startDate ?? '';
            $endDate = $endDate ?? '';
        @endphp

        <form method="GET" action="{{ route('doctor.report', ['doctorId' => $doctor->id]) }}" class="mt-4">
            <div class="mb-4">
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
            <input type="date" name="start_date" id="start_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $startDate }}">
            </div>
            <div class="mb-4">
            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $endDate }}">
            </div>
            
            <button type="button" id="filterButton" class="btn btn-primary bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-800 transition duration-300">Filter</button>

            
            <button type="button" id="clearFilterButton" class="btn btn-secondary bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-800 transition duration-300">Clear Filter</button>


            <div class="mt-6 relative inline-block text-left">
            <button type="button" class="btn bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-800 transition duration-300" id="exportDropdownButton">
                Export Report
            </button>
            <div class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-300 rounded-md shadow-lg z-10" id="exportDropdownMenu">
                <a href="javascript:void(0);" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" id="exportExcel">Export to Excel</a>
                <a href="javascript:void(0);" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" id="exportCsv">Export to CSV</a>
                <a href="javascript:void(0);" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" id="exportPdf">Export to PDF</a>
            </div>


            </div>
        </form>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h3 class="text-blue-600 font-bold text-xl mb-4">Summary Statistics</h3>
        <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="py-3 px-5 border-b text-left">Statistic</th>
                    <th class="py-3 px-5 border-b text-left">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Total Patients Seen</td>
                    <td class="py-3 px-5 border-b">{{ $summary->total_patients ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Emergency Cases Handled</td>
                    <td class="py-3 px-5 border-b">{{ $summary->emergency_cases ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Pending Visit Approvals</td>
                    <td class="py-3 px-5 border-b">{{ $summary->pending_approvals ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Completed Visits</td>
                    <td class="py-3 px-5 border-b">{{ $summary->completed_visits ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bg-white shadow-md rounded-md p-4 mt-4">
        <h3 class="text-blue-600 font-bold text-lg mb-3">Patient Visit Details</h3>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-md shadow-md">
                <thead>
                    <tr class="bg-blue-600 text-white text-sm">
                        <th class="py-2 px-3 border-b">Date</th>
                        <th class="py-2 px-3 border-b">Name</th>
                        <th class="py-2 px-3 border-b">Age</th>
                        <th class="py-2 px-3 border-b">Gender</th>
                        <th class="py-2 px-3 border-b">Complaint</th>
                        <th class="py-2 px-3 border-b">Diagnosis</th>
                        <th class="py-2 px-3 border-b">Meds</th>
                        <th class="py-2 px-3 border-b">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctorVisits as $visit)
                    <tr class="hover:bg-blue-100 hover:scale-100 hover-effect text-sm {{ $loop->even ? 'bg-blue-50' : 'bg-white' }}">
                        <td class="py-2 px-3 border-b">{{ $visit->visit_date ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->patient->full_name ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->patient->age_category ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->patient->gender ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->primary_complaint ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->diagnosis ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->medications_prescribed ?? 'N/A' }}</td>
                        <td class="py-2 px-3 border-b">{{ $visit->medical_status ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h3 class="text-blue-600 font-bold text-xl mb-4">Vital Stats Summary</h3>
        <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="py-3 px-5 border-b text-left">Vital Stat</th>
                    <th class="py-3 px-5 border-b text-left">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect">
                    <td class="py-3 px-5 border-b">Average Heart Rate</td>
                    <td class="py-3 px-5 border-b">{{ $vitalStats->avg_heart_rate ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Average Sugar Level</td>
                    <td class="py-3 px-5 border-b">{{ $vitalStats->avg_sugar_level ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Average Temperature</td>
                    <td class="py-3 px-5 border-b">{{ $vitalStats->avg_temperature ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Average Oxygen Level</td>
                    <td class="py-3 px-5 border-b">{{ $vitalStats->avg_oxygen_level ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h3 class="text-blue-600 font-bold text-xl mb-4">Treatments & Procedures Done</h3>
        <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="py-3 px-5 border-b text-left">Category</th>
                    <th class="py-3 px-5 border-b text-left">Details</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Common Diagnoses Treated</td>
                    <td class="py-3 px-5 border-b">{{ $treatments->common_diagnoses ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Most Prescribed Medications</td>
                    <td class="py-3 px-5 border-b">{{ $treatments->most_prescribed_medications ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Procedures Performed</td>
                    <td class="py-3 px-5 border-b">{{ $treatments->procedures_performed ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h3 class="text-blue-600 font-bold text-xl mb-4">Pending Follow-Ups & Recommendations</h3>
        <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="py-3 px-5 border-b text-left">Category</th>
                    <th class="py-3 px-5 border-b text-left">Details</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                    <td class="py-3 px-5 border-b">Upcoming Follow-Up Appointments</td>
                    <td class="py-3 px-5 border-b">{{ $followUps->upcoming_appointments ?? 'N/A' }}</td>
                </tr>
                <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                    <td class="py-3 px-5 border-b">Recommendations for Patients</td>
                    <td class="py-3 px-5 border-b">{{ $followUps->recommendations ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButton = document.getElementById('filterButton');
        const clearFilterButton = document.getElementById('clearFilterButton');
        const exportDropdownButton = document.getElementById('exportDropdownButton');
        const exportDropdownMenu = document.getElementById('exportDropdownMenu');
        const exportExcel = document.getElementById('exportExcel');
        const exportCsv = document.getElementById('exportCsv');
        const exportPdf = document.getElementById('exportPdf');

        filterButton.addEventListener('click', function () {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch("{{ route('doctor.report', ['doctorId' => $doctor->id]) }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ start_date: startDate, end_date: endDate })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        clearFilterButton.addEventListener('click', function () {
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';

            fetch("{{ route('doctor.report', ['doctorId' => $doctor->id]) }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        exportDropdownButton.addEventListener('click', function () {
            exportDropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (!exportDropdownButton.contains(event.target) && !exportDropdownMenu.contains(event.target)) {
                exportDropdownMenu.classList.add('hidden');
            }
        });

        function exportReport(format) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`{{ route('doctor.report.export', ['doctorId' => $doctor->id]) }}?format=${format}&start_date=${startDate}&end_date=${endDate}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `report.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error exporting report:', error);
            });
        }

        exportExcel.addEventListener('click', () => exportReport('xlsx'));
        exportCsv.addEventListener('click', () => exportReport('csv'));
        exportPdf.addEventListener('click', () => exportReport('pdf'));
    });
</script>
@endsection
