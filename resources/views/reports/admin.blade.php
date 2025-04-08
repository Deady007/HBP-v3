@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <style>
        .flex {
            display: flex;
            margin-right: -190px;
            margin-left: -190px;
        }
        /* Add smooth transitions for hover effects */
        .hover-effect {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
    </style>
    <h1 class="text-center mb-6 text-blue-600 font-bold text-2xl">Hospital Activity Report</h1>
   
    <div class="flex justify-center">
        <div class="w-full lg:w-3/4">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-blue-600 font-bold text-xl mb-4">Filter by Date Range</h3>
                <form method="GET" action="{{ route('reports.admin') }}">
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="start_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $startDate }}">
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="end_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $endDate }}">
                    </div>
                    <button type="button" id="filterButton" class="btn btn-primary bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-800 transition duration-300">Filter</button>
                    <button type="button" id="clearFilterButton" class="btn btn-secondary bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-800 transition duration-300">Clear Filter</button>
                    <div class="relative inline-block text-left mb-4">
                    <button type="button" class="btn btn-secondary bg-blue-600 text-white py-2 px-4 rounded-md inline-flex items-center" id="exportDropdownButton">
                        Export Report
                        <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg" id="exportDropdownMenu">
                        <a href="{{ route('admin.report.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export to Excel</a>
                        <a href="{{ route('admin.report.export.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export to CSV</a>
                        <a href="{{ route('admin.report.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export to PDF</a>
                    </div>
                </div>
                </form>

                

                

                <br><h3 class="text-blue-600 font-bold text-xl mt-6">Summary Statistics</h3><br>
                <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                    <tbody>
                        <tr class="hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b text-gray-700 font-medium">Total Patients Registered:</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $totalPatients ?? 'N/A' }}</td>
                        </tr>
                        @php $rowIndex = 1; @endphp
                        <tr class="{{ $rowIndex++ % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b text-gray-700 font-medium">Total Visits This Month:</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $totalVisits ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ $rowIndex++ % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b text-gray-700 font-medium">Approved Visits:</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $approvedVisits ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ $rowIndex++ % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b text-gray-700 font-medium">Pending Visits:</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $pendingVisits ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ $rowIndex++ % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 text-gray-700 font-medium">Emergency Visits:</td>
                            <td class="py-3 px-5 font-bold text-gray-800">{{ $emergencyCases ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <br><h3 class="text-blue-600 font-bold text-xl mt-6">Doctor-wise Visit Breakdown</h3><br>
                <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="py-3 px-5 border-b text-left">Doctor Name</th>
                            <th class="py-3 px-5 border-b text-left">Patients Seen</th>
                            <th class="py-3 px-5 border-b text-left">Pending Visits</th>
                            <th class="py-3 px-5 border-b text-left">Completed Visits</th>
                            <th class="py-3 px-5 border-b text-left">Emergency Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctorPerformance as $doctor)
                        @php $rowIndex = $rowIndex ?? 0; $rowIndex++; @endphp
                        <tr class="{{ $rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">{{ $doctor->name ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $doctor->patients_seen ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $doctor->pending_visits ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $doctor->completed_visits ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $doctor->emergency_visits ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <br><h3 class="text-blue-600 font-bold text-xl mt-6">Patient Demographics</h3><br>
                <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="py-3 px-5 border-b text-left">Category</th>
                            <th class="py-3 px-5 border-b text-left">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowIndex = $rowIndex ?? 0; @endphp
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Male Patients</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $malePatients ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Female Patients</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $femalePatients ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Other Gender</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $otherGenderPatients ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Children (0-12)</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $childrenCount ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Teenagers (13-19)</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $teenagersCount ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Adults (20-59)</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $adultsCount ?? 'N/A' }}</td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Seniors (60+)</td>
                            <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $seniorsCount ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <br><h3 class="text-blue-600 font-bold text-xl mt-6">Common Diagnoses & Treatments</h3><br>
                <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="py-3 px-5 border-b text-left">Category</th>
                            <th class="py-3 px-5 border-b text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowIndex = $rowIndex ?? 0; @endphp
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Top 5 Most Diagnosed Conditions</td>
                            <td class="py-3 px-5 border-b">
                                <ul class="list-disc list-inside text-gray-700">
                                    @foreach($topDiagnoses as $diagnosis)
                                    <li class="hover:text-green-600">{{ $diagnosis->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Top 5 Most Prescribed Medications</td>
                            <td class="py-3 px-5 border-b">
                                <ul class="list-disc list-inside text-gray-700">
                                    @foreach($topMedications as $medication)
                                    <li class="hover:text-green-600">{{ $medication->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b font-medium text-gray-700">Common Procedures Done</td>
                            <td class="py-3 px-5 border-b">
                                <ul class="list-disc list-inside text-gray-700">
                                    @foreach($commonProcedures as $procedure)
                                    <li class="hover:text-green-600">{{ $procedure->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <br>
                <h3 class="text-blue-600 font-bold text-xl mt-6">Appointment Summary</h3><br>
                <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="py-3 px-5 border-b text-left">Visit Date</th>
                            <th class="py-3 px-5 border-b text-left">Patient Name</th>
                            <th class="py-3 px-5 border-b text-left">Doctor Assigned</th>
                            <th class="py-3 px-5 border-b text-left">Appointment Type</th>
                            <th class="py-3 px-5 border-b text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowIndex = $rowIndex ?? 0; @endphp
                        @foreach($appointments as $appointment)
                        <tr class="{{ ++$rowIndex % 2 === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 hover:scale-100 hover-effect">
                            <td class="py-3 px-5 border-b">{{ $appointment->visit_date ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b">{{ $appointment->patient->full_name ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b">{{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b">{{ $appointment->appointment_type ?? 'N/A' }}</td>
                            <td class="py-3 px-5 border-b">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                    {{ $appointment->is_approved ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $appointment->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('filterButton').addEventListener('click', function () {
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;

        fetch("{{ route('reports.admin') }}", {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
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

    document.getElementById('clearFilterButton').addEventListener('click', function () {
        document.querySelector('input[name="start_date"]').value = '';
        document.querySelector('input[name="end_date"]').value = '';

        fetch("{{ route('reports.admin') }}", {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ start_date: '', end_date: '' })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Filters cleared:', data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    document.getElementById('exportDropdownButton').addEventListener('click', function () {
        const menu = document.getElementById('exportDropdownMenu');
        menu.classList.toggle('hidden');
    });

    document.addEventListener('click', function (event) {
        const button = document.getElementById('exportDropdownButton');
        const menu = document.getElementById('exportDropdownMenu');
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
</script>
@endsection
