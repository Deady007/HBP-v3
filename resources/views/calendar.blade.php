@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-primary font-weight-bold">Medical Visits Calendar</h1>

    <!-- Filter and Search -->
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <label for="statusFilter" class="form-label">Filter by Status:</label>
            <select id="statusFilter" class="form-control">
                <option value="">All</option>
                <option value="Approved">Approved</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div>
            <label for="searchPatient" class="form-label">Search by Patient:</label>
            <input type="text" id="searchPatient" class="form-control" placeholder="Enter patient name">
        </div>
    </div>

    <div id="calendar" class="bg-white rounded-lg shadow p-4"></div>
</div>

<!-- Modal for adding/editing/rescheduling visits -->
<div class="modal fade" id="visitModal" tabindex="-1" role="dialog" aria-labelledby="visitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visitModalLabel">Visit Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="visitForm">
                    @csrf
                    <input type="hidden" name="visit_id" id="visit_id">
                    <input type="hidden" name="event_id" id="event_id"> <!-- New hidden input field -->
                    @php
                    $patients = auth()->user()->hasRole('Admin') ? \App\Models\Patient::all() : \App\Models\Patient::where('user_unique_id', auth()->user()->id)->get();
                    @endphp
                    <div class="form-group">
                        <label for="patient_id">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-control">
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="preferred_visit_date">Preferred Visit Date</label>
                        <input type="date" name="preferred_visit_date" id="preferred_visit_date" class="form-control" value="{{ old('preferred_visit_date') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="preferred_time_slot">Preferred Time Slot</label>
                        <input type="time" name="preferred_time_slot" id="preferred_time_slot" class="form-control" value="{{ old('preferred_time_slot') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="appointment_type">Appointment Type</label>
                        <select name="appointment_type" id="appointment_type" class="form-control" required>
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Follow-up Visit">Follow-up Visit</option>
                            <option value="Emergency Visit">Emergency Visit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="primary_complaint">Primary Complaint</label>
                        <textarea name="primary_complaint" id="primary_complaint" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying visit details -->
<div class="modal fade" id="visitDetailsModal" tabindex="-1" role="dialog" aria-labelledby="visitDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visitDetailsModalLabel">Visit Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Patient:</strong> <span id="details_patient"></span></p>
                <p><strong>Doctor:</strong> <span id="details_doctor"></span></p>
                <p><strong>Nurse:</strong> <span id="details_nurse"></span></p>
                <p><strong>Date:</strong> <span id="details_date"></span></p>
                <p><strong>Time Slot:</strong> <span id="details_time_slot"></span></p>
                <p><strong>Appointment Type:</strong> <span id="details_appointment_type"></span></p>
                <p><strong>Primary Complaint:</strong> <span id="details_primary_complaint"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure jQuery is loaded
        if (typeof jQuery === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js';
            document.head.appendChild(script);
        }

        // Ensure Bootstrap is loaded
        if (typeof $.fn.modal === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js';
            document.head.appendChild(script);
        }

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            editable: true,
            events: @json($events),
            dateClick: function(info) {
                $('#visitModal').modal('show');
                $('#visitForm')[0].reset();
                $('#visit_id').val('');
                $('#event_id').val(''); // Reset event ID
                $('#preferred_visit_date').val(info.dateStr);
            },
            eventClick: function(info) {
                var visitId = info.event.id;
                $('#event_id').val(visitId); // Set event ID
                fetch(`/medical-visit/details/${visitId}`)
                    .then(response => response.json())
                    .then(data => {
                        $('#details_patient').text(data.patient ? data.patient.full_name : 'N/A');
                        $('#details_doctor').text(data.doctor ? data.doctor.name : 'N/A');
                        $('#details_nurse').text(data.nurse ? data.nurse.name : 'N/A');
                        $('#details_date').text(data.visit_date);
                        $('#details_time_slot').text(data.time_slot);
                        $('#details_appointment_type').text(data.appointment_type);
                        $('#details_primary_complaint').text(data.primary_complaint);
                        $('#visitDetailsModal').modal('show');
                    });
            },
            eventDrop: function(info) {
                var event = info.event;
                var formData = {
                    _token: '{{ csrf_token() }}',
                    visit_date: new Date(event.start.getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
                    time_slot: event.start.toISOString().slice(11, 16),
                    event_id: event.id // Include event ID
                };

                // Validate the new date is after today
                var today = new Date();
                var newDate = new Date(formData.visit_date);
                if (newDate <= today) {
                    alert('The visit date must be after today.');
                    info.revert(); // Revert to the original position
                    return;
                }

                $.ajax({
                    url: '/medical_visit/' + event.id + '/reschedule',
                    method: 'PATCH',
                    data: formData,
                    success: function(response) {
                        // Update the event immediately after a successful response
                        info.event.setStart(response.new_date + 'T' + response.new_time);
                        calendar.refetchEvents(); // Fetch updated events without page refresh
                        
                    },
                    error: function() {
                        info.revert(); // Revert to the original position if AJAX fails
                    }
                });
            },

            eventResize: function(info) {
                var event = info.event;
                var formData = {
                    _token: '{{ csrf_token() }}',
                    visit_date: new Date(event.start.getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
                    time_slot: event.start.toISOString().slice(11, 16),
                    event_id: event.id // Include event ID
                };

                $.ajax({
                    url: '/medical_visit/' + event.id + '/reschedule',
                    method: 'PATCH',
                    data: formData,
                    success: function(response) {
                        // Update the event immediately
                        info.event.setStart(response.new_date + 'T' + response.new_time);
                        calendar.refetchEvents(); // Fetch updated events without page refresh
                    },
                    error: function() {
                        info.revert(); // Revert if the request fails
                    }
                });
            },

            // eventRender: function(info) {
            //     var event = info.event;
            //     var element = info.el;
            //     element.classList.add('p-2', 'rounded-lg', 'shadow-md', 'hover:shadow-lg', 'transition-shadow', 'duration-300');
            //     if (event.extendedProps.status === 'Approved') {
            //         element.style.backgroundColor = 'green';
            //         element.style.color = 'white';
            //     } else if (event.extendedProps.status === 'Pending') {
            //         element.style.backgroundColor = 'green';
            //         element.style.color = 'black'; // Ensure text is black
            //     } else {
            //         element.style.backgroundColor = 'gray';
            //         element.style.color = 'white';
            //     }
            //     // Add custom content to the event element
            //     var content = `
            //     <div class="d-flex align-items-center">
            //         <div class="flex-grow-1">
            //             <h5 class="mb-0">${event.title}</h5>
            //             <small>${event.extendedProps.status}</small>
            //         </div>
            //     </div>
            // `;
            //     element.innerHTML = content;
            // }
            eventContent: function(arg) {
                let customHtml = document.createElement('div');
                customHtml.classList.add('p-2', 'rounded-lg', 'shadow-md', 'hover:shadow-lg', 'transition-shadow', 'duration-300');

                if (arg.event.extendedProps.status === 'Approved') {
                    customHtml.classList.add('bg-success', 'text-white');
                } else if (arg.event.extendedProps.status === 'pending') {
                    customHtml.classList.add('bg-warning', 'text-white'); // Ensuring black text color
                } else {
                    customHtml.classList.add('bg-secondary', 'text-white');
                }

                customHtml.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="mb-0">${arg.event.title}</h5>
                <small>${arg.event.extendedProps.status}</small>
            </div> 
        </div>
    `;
                return {
                    domNodes: [customHtml]
                };
            }

        });
        calendar.render();

        // Filter events by status
        document.getElementById('statusFilter').addEventListener('change', function() {
            var selectedStatus = this.value;

            calendar.getEvents().forEach(function(event) {
                if (!selectedStatus || event.extendedProps.status === selectedStatus) {
                    event.setProp('display', ''); // Show event
                } else {
                    event.setProp('display', 'none'); // Hide event
                }
            });
        });

        // Search events by patient name
        document.getElementById('searchPatient').addEventListener('input', function() {
            var searchQuery = this.value.toLowerCase();

            calendar.getEvents().forEach(function(event) {
                var patientName = event.title.toLowerCase();
                if (!searchQuery || patientName.includes(searchQuery)) {
                    event.setProp('display', ''); // Show event
                } else {
                    event.setProp('display', 'none'); // Hide event
                }
            });
        });

        $('#visitForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $('#visit_id').val() ? '/medical_visit/' + $('#visit_id').val() : '/medical_visit',
                method: $('#visit_id').val() ? 'PATCH' : 'POST',
                data: formData,
                success: function(response) {
                    $('#visitModal').modal('hide');
                    calendar.refetchEvents(); // Refetch events to update the calendar
                },
                error: function(response) {
                    if (response.status === 422) {
                        var errors = response.responseJSON.errors;
                        var errorMessage = '';
                        for (var field in errors) {
                            errorMessage += errors[field].join(', ') + '\n';
                        }
                        alert('Validation error:\n' + errorMessage);
                    } else {
                        alert('An error occurred while saving the visit.');
                    }
                }
            });
        });
    });
</script>
@endsection