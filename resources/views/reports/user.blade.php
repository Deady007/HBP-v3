@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
<style>
        .flex {
            display: flex;
            margin-right: -190px;
            margin-left: -190px;
        }
        .form-control {
                                background-color: #f9fafb;
                                border: 1px solid #d1d5db;
                                padding: 0.5rem 1rem;
                                font-size: 1rem;
                                color: #374151;
                                transition: border-color 0.2s, box-shadow 0.2s;
                            }
                            .form-control:focus {
                                border-color: #2563eb;
                                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
                                outline: none;
                            }
                            .form-control option {
                                color: #374151;
                            }
    </style>
    <h1 class="text-center mb-4 text-blue-600 font-bold text-2xl">Personal Medical Summary</h1>
    <div class="flex justify-center">
        <div class="w-full lg:w-3/4">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-blue-600 font-bold text-xl mb-4">Select Patient</h3>
                <form method="GET" action="{{ route('reports.user') }}">
                    <div class="mb-4">
                        <select name="patient_id" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $selectedPatientId == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="start_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $startDate }}">
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="end_date" class="form-control block w-full mt-1 rounded-md border-gray-300 shadow-sm" value="{{ $endDate }}">
                    </div>
                    <button type="button" class="btn btn-primary bg-blue-600 text-white py-2 px-4 rounded-md" id="filter-button">Filter</button>

                    
                    <div class="relative inline-block text-left">
                        <button type="button" class="btn btn-primary bg-blue-600 text-white py-2 px-4 rounded-md" id="export-button">
                            Export Options
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg hidden" id="export-menu">
                            <ul class="py-1">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" data-export="excel">Export to Excel</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" data-export="csv">Export to CSV</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" data-export="pdf">Export to PDF</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <script>
                        const exportButton = document.getElementById('export-button');
                        const exportMenu = document.getElementById('export-menu');

                        exportButton.addEventListener('click', () => {
                            exportMenu.classList.toggle('hidden');
                        });

                        document.querySelectorAll('#export-menu a').forEach(item => {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                const selectedOption = this.getAttribute('data-export');
                                const patientId = document.querySelector('select[name="patient_id"]').value;
                                const startDate = document.querySelector('input[name="start_date"]').value;
                                const endDate = document.querySelector('input[name="end_date"]').value;

                                fetch(`{{ url('reports/export') }}/${selectedOption}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        patient_id: patientId,
                                        start_date: startDate,
                                        end_date: endDate,
                                    }),
                                })
                                .then(response => {
                                    if (response.ok) {
                                        return response.blob();
                                    }
                                    throw new Error('Failed to export file');
                                })
                                .then(blob => {
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = `report.${selectedOption}`;
                                    document.body.appendChild(a);
                                    a.click();
                                    a.remove();
                                })
                                .catch(error => console.error('Error:', error));
                            });
                        });
                    </script>


                </form>
                </form>
                
                @if($selectedPatientId && !empty($userVisits))
                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Patient Profile</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <tbody>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Name:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->full_name }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Patient ID:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->pat_unique_id }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Gender:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->gender }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Date of Birth:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->date_of_birth }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Contact Number:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->phone_number }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b text-gray-700 font-medium">Email:</td>
                                <td class="py-3 px-5 border-b font-bold text-gray-800">{{ $patients->find($selectedPatientId)->email ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 text-gray-700 font-medium">Emergency Contact:</td>
                                <td class="py-3 px-5 font-bold text-gray-800">
                                    {{ $patients->find($selectedPatientId)->emergency_contact_name ?? 'N/A' }} – 
                                    {{ $patients->find($selectedPatientId)->emergency_contact_relationship ?? 'N/A' }} 
                                    ({{ $patients->find($selectedPatientId)->emergency_contact_phone ?? 'N/A' }})
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                

                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Recent Medical Visits</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="py-3 px-5 border-b text-left">Visit Date</th>
                                <th class="py-3 px-5 border-b text-left">Doctor</th>
                                <th class="py-3 px-5 border-b text-left">Complaint</th>
                                <th class="py-3 px-5 border-b text-left">Diagnosis</th>
                                <th class="py-3 px-5 border-b text-left">Medications</th>
                                <th class="py-3 px-5 border-b text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userVisits as $visit)
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">{{ \Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y') }}</td>
                                <td class="py-3 px-5 border-b">{{ $visit->doctor->name ?? 'N/A' }}</td>
                                <td class="py-3 px-5 border-b">{{ $visit->primary_complaint ?? 'N/A' }}</td>
                                <td class="py-3 px-5 border-b">{{ $visit->diagnosis ?? 'N/A' }}</td>
                                <td class="py-3 px-5 border-b">{{ $visit->medications_prescribed ?? 'N/A' }}</td>
                                <td class="py-3 px-5 border-b">{{ $visit->is_approved ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php $lastVisit = $userVisits->last(); @endphp
                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Health Vitals Summary</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="py-3 px-5 border-b text-left">Vital</th>
                                <th class="py-3 px-5 border-b text-left">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Latest Heart Rate</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->heart_rate : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Latest Sugar Level</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->sugar_level : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Latest Temperature</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->temperature : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Oxygen Level</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->oxygen_level : 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Current Treatment Plan</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="py-3 px-5 border-b text-left">Treatment Aspect</th>
                                <th class="py-3 px-5 border-b text-left">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Medications</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->medications_prescribed : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Ongoing Treatments</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->ongoing_treatments : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Procedures Done</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->procedures : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Doctor’s Recommendations</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->doctor_notes : 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Next Steps & Follow-up</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="py-3 px-5 border-b text-left">Detail</th>
                                <th class="py-3 px-5 border-b text-left">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Next Visit Date</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->preferred_visit_date : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Preferred Time Slot</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? $lastVisit->preferred_time_slot : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Is Emergency Case?</td>
                                <td class="py-3 px-5 border-b">{{ $lastVisit ? ($lastVisit->is_emergency ? 'Yes' : 'No') : 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">Emergency Contact for Queries</td>
                                <td class="py-3 px-5 border-b">
                                    {{ $patients->find($selectedPatientId)->emergency_contact_name ?? 'N/A' }}, 
                                    {{ $patients->find($selectedPatientId)->emergency_contact_phone ?? 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-blue-600 font-bold text-xl mb-4">Report Summary for Period</h3>
                    <table class="min-w-full bg-gradient-to-r from-blue-50 to-blue-100 border border-gray-300 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="py-3 px-5 border-b text-left">Statistic</th>
                                <th class="py-3 px-5 border-b text-left">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Total Visits</td>
                                <td class="py-3 px-5 border-b">{{ $userVisits->count() }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-blue-50">
                                <td class="py-3 px-5 border-b">New Diagnoses</td>
                                <td class="py-3 px-5 border-b">{{ $userVisits->pluck('diagnosis')->unique()->implode(', ') ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-blue-100 hover:scale-100 hover-effect bg-white">
                                <td class="py-3 px-5 border-b">Medications Prescribed</td>
                                <td class="py-3 px-5 border-b">{{ $userVisits->pluck('medications_prescribed')->unique()->implode(', ') ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('filter-button').addEventListener('click', function() {
        const patientId = document.querySelector('select[name="patient_id"]').value;
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;

        fetch("{{ route('reports.user') }}", {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                patient_id: patientId,
                start_date: startDate,
                end_date: endDate,
            }),
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response data (e.g., update the DOM with the filtered results)
            console.log(data);
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('export-options').addEventListener('change', function() {
        const selectedOption = this.value;
        const patientId = document.querySelector('select[name="patient_id"]').value;
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;

        fetch(`{{ url('reports/export') }}/${selectedOption}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                patient_id: patientId,
                start_date: startDate,
                end_date: endDate,
            }),
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Failed to export file');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report.${selectedOption}`;
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endsection
