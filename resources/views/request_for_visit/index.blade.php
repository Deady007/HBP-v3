@extends('layouts.app')

@section('content')
<div class="content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Requested Medical Visits</h1>
                @if(Auth::check())
                <span class="badge badge-primary slide-in text-lg">Logged in as : {{ Auth::user()->name }}</span>
                @endif
            </div>
        </div>
    </section>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideIn 2s;
        }

        .emergency {
            background-color: rgba(146, 193, 150, 0.2)!important;
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <div class="container mx-auto">
            <div class="flex justify-center">
                <div class="w-full">
                    <div class="bg-white shadow-lg rounded-lg"> <!-- Tailwind classes for card -->
                        <div class="bg-teal-500 text-white p-4 rounded-t-lg flex justify-between items-center"> <!-- Tailwind classes for header -->
                            <h3 class="text-lg font-semibold">Medical Visits List</h3>
                            <div id="customSearchContainer"></div>
                        </div>
                        <div class="p-4">
                            @if($medicalVisits)
                            <table id="medicalVisitsTable" class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Patient Unique ID</th>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Patient Name</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Preferred Visit Date</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Preferred Time Slot</th>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Approval Status</th>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $pendingVisits = $medicalVisits->filter(function($visit) {
                                    return $visit->is_approved == 'pending';
                                    });
                                    $emergencyVisits = $medicalVisits->filter(function($visit) {
                                    return $visit->is_emergency == true;
                                    });
                                    @endphp
                                    @foreach($pendingVisits as $visit)
                                    @if($visit->patient)
                                    <tr class="border-b {{ $visit->is_emergency ? 'emergency' : '' }}">
                                        <td class="py-2 px-4">{{ $visit->patient->pat_unique_id }}</td>
                                        <td class="py-2 px-4">{{ $visit->patient->full_name }} {{ $visit->patient->middle_name}} {{ $visit->patient->last_name}}</td>
                                        <td class="py-2 px-1">{{ $visit->preferred_visit_date }}</td>
                                        <td class="py-2 px-1">{{ $visit->preferred_time_slot }}</td>
                                        <td class="py-2 px-4">{{ $visit->is_approved ? 'Pending' : 'Approved' }}</td>
                                        <td class="py-2 px-4"><button class="btn btn-primary" data-toggle="modal" data-target="#approveModal-{{ $visit->id }}">Approve</button></td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p>No medical visits available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@foreach($pendingVisits as $visit)
<div class="modal fade" id="approveModal-{{ $visit->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel-{{ $visit->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel-{{ $visit->id }}">Approve Medical Visit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('medical_visit.approve', $visit->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="visit_date">Visit Date</label>
                        <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="time_slot">Time Slot</label>
                        <input type="time" name="time_slot" id="time_slot" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_id">Doctor</label>
                        <select name="doctor_id" id="doctor_id-{{ $visit->id }}" class="form-control" required onchange="updateDoctorName(this, 'doctor_name-{{ $visit->id }}')">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nurse_id">Nurse</label>
                        <select name="nurse_id" id="nurse_id-{{ $visit->id }}" class="form-control" required onchange="updateNurseName(this, 'nurse_name-{{ $visit->id }}')">
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($pendingVisits as $visit)
        fetchUsersWithRole('doctor', 'doctor_id-{{ $visit->id }}');
        fetchUsersWithRole('nurse', 'nurse_id-{{ $visit->id }}');
        @endforeach

        function fetchUsersWithRole(role, id) {
            fetch(`/api/users-with-role/${role}`)
                .then(response => response.json())
                .then(data => {
                    const userSelect = document.getElementById(id);
                    userSelect.innerHTML = '';
                    data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        userSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching users with role:', error));
        }

        function updateDoctorName(select, inputId) {
            const selectedOption = select.options[select.selectedIndex];
            document.getElementById(inputId).value = selectedOption.textContent;
        }

        function updateNurseName(select, inputId) {
            const selectedOption = select.options[select.selectedIndex];
            document.getElementById(inputId).value = selectedOption.textContent;
        }

        // New function to delete non-approved visits when a patient is deleted
        function deleteNonApprovedVisits(patientId) {
            fetch(`/api/patients/${patientId}/delete-non-approved-visits`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    console.error('Error deleting non-approved visits:', data.message);
                }
            })
            .catch(error => console.error('Error deleting non-approved visits:', error));
        }

        // Initialize DataTable with additional configurations
        $('#medicalVisitsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "dom": '<"top"lfB>rt<"bottom"ip><"clear">', // Add export buttons to "top"
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "renderer": "semanticUI"
        }).buttons().container().appendTo('#medicalVisitsTable_wrapper .col-md-6:eq(0)');

        // Add column search functionality
        $('#medicalVisitsTable thead tr:eq(1) th').each(function (i) {
            var title = $(this).text();
            $(this).find('input').on('keyup change', function () {
                if ($('#medicalVisitsTable').DataTable().column(i).search() !== this.value) {
                    $('#medicalVisitsTable').DataTable()
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        $('#medicalVisitsTable_filter').detach().appendTo('#customSearchContainer');

        // Remove the default "Search:" label
        $('#medicalVisitsTable_filter label').contents().filter(function() {
            return this.nodeType === 3; // Select text nodes
        }).remove();

        // Style the search input field
        $('#medicalVisitsTable_filter input')
            .attr('placeholder', 'Search Medical Visits...')
            .css({
                'color': 'black', // Change font color to black
                'font-weight': 'bold' // Make text bold (optional)
            });
    });
</script>
@endsection