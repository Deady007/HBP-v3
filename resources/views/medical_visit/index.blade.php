@extends('layouts.app')

@section('content')
<div class="content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Medical Visits Management</h1>
                <a class="btn btn-success mb-2" href="{{ route('medical_visit.create') }}"><i class="fa fa-plus"></i> Create New Visit</a>
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
            background-color: rgba(255, 0, 0, 0.1);
        }

        #medical-visits-table_filter input {
            width: 600px !important; /* Adjust width as needed */
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

    </style>

    <!-- Main content -->
    <section class="content">
        <div class="container mx-auto">
            <div class="flex justify-center">
                <div class="w-full">
                    <div class="bg-white shadow-lg rounded-lg"> <!-- Tailwind classes for card -->
                    <div class="bg-teal-500 text-white p-4 rounded-t-lg flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Medical Visits List</h3>
                        <div id="customSearchContainer"></div>
                    </div>

                        <div class="p-4">
                            @session('success')
                            <div class="alert alert-success" role="alert">
                                {{ $value }}
                            </div>
                            @endsession

                            @if($data)
                            <table id='medical-visits-table'  class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Patient Unique ID</th>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700">Patient Name</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Visit Date</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Doctor</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Nurse</th>
                                        <th class="py-2 px-1 text-left text-sm font-medium text-gray-700">Appointment Status</th>
                                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-700" width="280px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $visit)
                                    <tr class="border-b {{ $visit->is_emergency ? 'emergency' : '' }}" id="visit-row-{{ $visit->id }}">
                                        <td class="py-2 px-4">{{ $visit->patient->pat_unique_id }}</td>
                                        <td class="py-2 px-4">{{ $visit->patient->full_name }}</td>
                                        <td class="py-2 px-1">{{ $visit->visit_date ?? 'N/A'  }}</td>
                                        <td class="py-2 px-1">{{ $visit->doctor->name ?? 'N/A' }}</td>
                                        <td class="py-2 px-1">{{ $visit->nurse->name ?? 'N/A' }}</td>
                                        <td class="py-2 px-1">{{ $visit->is_approved }}</td>
                                        <td class="py-2 px-4">
                                            @can('medical-visit-create', $visit)
                                            <a class="btn btn-info btn-sm" href="{{ route('medical_visit.show',$visit->id) }}"><i class="fas fa-list"></i> Show</a>
                                            @endcan
                                            @can('medical-visit-edit', $visit)
                                            <a class="btn btn-primary btn-sm" href="{{ route('medical_visit.edit',$visit->id) }}"><i class="	fas fa-pencil-alt"></i> Edit</a>
                                            @endcan
                                            
                                            @can('medical-visit-delete', $visit)
                                            <form action="{{ route('medical_visit.destroy', $visit->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                            <button class="btn btn-danger btn-sm delete-visit" data-id="{{ $visit->id }}"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                            @endcan
                                            @can('medical-visit-reschedule', $visit)
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#rescheduleModal-{{ $visit->id }}"><i class="fas fa-calendar-alt"></i> Reschedule</button>
                                            @endcan
                                        </td>
                                    </tr>
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

@foreach ($data as $key => $visit)
<div class="modal fade" id="rescheduleModal-{{ $visit->id }}" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel-{{ $visit->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel-{{ $visit->id }}">Reschedule Medical Visit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('medical_visit.reschedule', $visit->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="visit_date">Visit Date</label>
                        <input type="date" name="visit_date" id="visit_date" class="form-control" value="{{ $visit->visit_date }}" required>
                    </div>
                    <div class="form-group">
                        <label for="time_slot">Time Slot</label>
                        <input type="time" name="time_slot" id="time_slot" class="form-control" value="{{ $visit->time_slot }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reschedule</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#medical-visits-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "dom": '<"top"f>rt<"bottom"lp><"clear">' // Move search bar to "top"
        });


        $('#medical-visits-table_filter').detach().appendTo('#customSearchContainer');

        $('#medical-visits-table_filter').detach().appendTo('#customSearchContainer');

// Remove the default "Search:" label
$('#medical-visits-table_filter label').contents().filter(function() {
    return this.nodeType === 3; // Select text nodes
}).remove();

// Style the search input field
$('#medical-visits-table_filter input')
    .attr('placeholder', 'Search Medical Visits...')
    .css({
        'color': 'black', // Change font color to black
        'font-weight': 'bold' // Make text bold (optional)
    });


        document.querySelectorAll('.delete-visit').forEach(button => {
            button.addEventListener('click', function() {
                const visitId = this.getAttribute('data-id');
                fetch(`/medical_visits/${visitId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`visit-row-${visitId}`).remove();
                        } else {
                            alert('Error deleting visit');
                        }
                    });
            });
        });

        // Handle reschedule modal
        var rescheduleButtons = document.querySelectorAll('[data-toggle="modal"]');
        rescheduleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-target');
                var modal = document.querySelector(target);
                $(modal).modal('show');
            });
        });
    });
</script>
@endsection