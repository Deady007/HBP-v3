@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4 text-primary font-weight-bold" style="font-size: 2.5rem;">Edit Medical Visit</h1>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-lg transition-card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li style="font-size: 1.1rem;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('medical_visit.update', $visit->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="patient-details mb-4">
                            <h3 class="text-primary font-weight-bold" style="font-size: 1.8rem;">Patient Information</h3>
                            <div class="form-group">
                                <label for="patient_id" style="font-size: 1.1rem;">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-control" disabled style="font-size: 1.1rem;">
                                        <option value="{{ $visit->patient->id }}" data-unique-id="{{ $visit->patient->unique_id }}" @if($visit->patient->id == $visit->patient_id) selected @endif>{{ $visit->patient->full_name }} </option>

                                </select>
                            </div>
                            <input type="hidden" name="unique_id" id="unique_id" value="{{ $visit->unique_id }}">
                        </div>

                        <div class="visit-details mb-4">
                            <h3 class="text-primary font-weight-bold" style="font-size: 1.8rem;">Visit Details</h3>
                            <div class="form-group">
                                <label for="visit_date" style="font-size: 1.1rem;">Visit Date</label>
                                <input type="text" name="visit_date" id="visit_date" class="form-control datetimepicker" disabled value="{{ $visit->visit_date }}" style="font-size: 1.1rem;">
                            </div>
                            <div class="form-group">
                                <label for="time_slot" style="font-size: 1.1rem;">Time Slot</label>
                                <select name="time_slot" id="time_slot" class="form-control" disabled style="font-size: 1.1rem;">
                                    <option value="{{ $visit->time_slot }}">{{ $visit->time_slot }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferred_visit_date" style="font-size: 1.1rem;">Preferred Visit Date</label>
                                <input type="text" name="preferred_visit_date" id="preferred_visit_date" class="form-control datepicker" value="{{ $visit->preferred_visit_date }}" style="font-size: 1.1rem;">
                            </div>
                            <div class="form-group">
                                <label for="preferred_time_slot" style="font-size: 1.1rem;">Preferred Time Slot</label>
                                <select name="preferred_time_slot" id="preferred_time_slot" class="form-control" disabled style="font-size: 1.1rem;">
                                    <option value="{{ $visit->preferred_time_slot }}">{{ $visit->preferred_time_slot }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="doctor_name" style="font-size: 1.1rem;">Doctor</label>
                                <select name="doctor_name" id="doctor_name" class="form-control" style="font-size: 1.1rem;" readonly>
                                    <option value="{{ $visit->doctor->name }}" selected>{{ $visit->doctor->name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nurse_name" style="font-size: 1.1rem;">Nurse</label>
                                <select name="nurse_name" id="nurse_name" class="form-control" style="font-size: 1.1rem;" readonly>
                                    <option value="{{ $visit->nurse->name }}" selected>{{ $visit->nurse->name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="appointment_type" style="font-size: 1.1rem;">Appointment Type</label>
                                <select name="appointment_type" id="appointment_type" class="form-control" style="font-size: 1.1rem;">
                                    <option value="Routine Checkup" {{ $visit->appointment_type == 'Routine Checkup' ? 'selected' : '' }}>Routine Checkup</option>
                                    <option value="Follow-up Visit" {{ $visit->appointment_type == 'Follow-up Visit' ? 'selected' : '' }}>Follow-up Visit</option>
                                    <option value="Emergency Visit" {{ $visit->appointment_type == 'Emergency Visit' ? 'selected' : '' }}>Emergency Visit</option>
                                    <option value="Other" {{ $visit->appointment_type == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="primary_complaint" style="font-size: 1.1rem;">Primary Complaint</label>
                                <textarea name="primary_complaint" id="primary_complaint" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->primary_complaint }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="symptoms">Symptoms (if applicable)</label>
                                <div>
                                    <label><input type="checkbox" name="symptoms[]" value="Fever" {{ is_array(old('symptoms')) && in_array('Fever', old('symptoms')) ? 'checked' : '' }}> Fever</label>
                                    <label><input type="checkbox" name="symptoms[]" value="Cough" {{ is_array(old('symptoms')) && in_array('Cough', old('symptoms')) ? 'checked' : '' }}> Cough</label>
                                    <label><input type="checkbox" name="symptoms[]" value="Breathing Issues" {{ is_array(old('symptoms')) && in_array('Breathing Issues', old('symptoms')) ? 'checked' : '' }}> Breathing Issues</label>
                                    <label><input type="checkbox" name="symptoms[]" value="Body Pain" {{ is_array(old('symptoms')) && in_array('Body Pain', old('symptoms')) ? 'checked' : '' }}> Body Pain</label>
                                    <label><input type="checkbox" name="symptoms[]" value="Weakness" {{ is_array(old('symptoms')) && in_array('Weakness', old('symptoms')) ? 'checked' : '' }}> Weakness</label>
                                    <label><input type="checkbox" name="symptoms[]" value="Other" {{ is_array(old('symptoms')) && in_array('Other', old('symptoms')) ? 'checked' : '' }}> Other</label>
                                </div>
                            </div>
                        </div>

                        <div class="treatment-details mb-4">
                            <h3 class="text-primary font-weight-bold" style="font-size: 1.8rem;">Treatment Information</h3>
                            <div class="form-group">
                                <label for="sugar_level" style="font-size: 1.1rem;"> sugar level</label>
                                <input type="range" name="sugar_level" id="sugar_level" class="form-control" min="80" max="180" value="{{ $visit->sugar_level }}" style="font-size: 1.1rem;">
                                <span id="sugar_level_value">{{ $visit->sugar_level }}</span>
                            </div>
                            <div class="form-group">
                                <label for="heart_rate" style="font-size: 1.1rem;">Heart Rate</label>
                                <input type="range" name="heart_rate" id="heart_rate" class="form-control" min="40" max="180" value="{{ $visit->heart_rate }}" style="font-size: 1.1rem;">
                                <span id="heart_rate_value">{{ $visit->heart_rate }}</span>
                            </div>
                            <div class="form-group">
                                <label for="temperature" style="font-size: 1.1rem;">Temperature</label>
                                <input type="range" name="temperature" id="temperature" class="form-control" min="95" max="105" step="0.1" value="{{ $visit->temperature }}" style="font-size: 1.1rem;">
                                <span id="temperature_value">{{ $visit->temperature }}</span>
                            </div>
                            <div class="form-group">
                                <label for="oxygen_level" style="font-size: 1.1rem;">Oxygen Level</label>
                                <input type="range" name="oxygen_level" id="oxygen_level" class="form-control" min="70" max="100" value="{{ $visit->oxygen_level }}" style="font-size: 1.1rem;">
                                <span id="oxygen_level_value">{{ $visit->oxygen_level }}</span>
                            </div>
                            
                            <div class="form-group">
                                <label for="diagnosis" style="font-size: 1.1rem;">Diagnosis</label>
                                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->diagnosis }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="simplified_diagnosis" style="font-size: 1.1rem;">Simplified Diagnosis</label>
                                <textarea name="simplified_diagnosis" id="simplified_diagnosis" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->simplified_diagnosis }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="medications_prescribed" style="font-size: 1.1rem;">Prescribed Medications</label>
                                <textarea name="medications_prescribed" id="medications_prescribed" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->medications_prescribed }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="ongoing_treatments" style="font-size: 1.1rem;">Treatment Plan</label>
                                <textarea name="ongoing_treatments" id="ongoing_treatments" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->ongoing_treatments }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="procedures" style="font-size: 1.1rem;">Next Steps</label>
                                <textarea name="procedures" id="procedures" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->procedures }}</textarea>
                            </div>
                        </div>

                        <div class="notes mb-4">
                            <h3 class="text-primary font-weight-bold" style="font-size: 1.8rem;">Additional Notes</h3>
                            <div class="form-group">
                                <label for="doctor_notes" style="font-size: 1.1rem;">Doctor's Notes</label>
                                <textarea name="doctor_notes" id="doctor_notes" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->doctor_notes }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nurse_observations" style="font-size: 1.1rem;">Nurse's Observations</label>
                                <textarea name="nurse_observations" id="nurse_observations" class="form-control" rows="3" style="font-size: 1.1rem;">{{ $visit->nurse_observations }}</textarea>
                            </div>
                        </div>

                        <input type="hidden" name="is_emergency" id="is_emergency" value="{{ $visit->is_emergency ? 1 : 0 }}">

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('medical_visit.index') }}" class="btn btn-secondary">Back to List</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .transition-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .text-primary {
        color: #4e73df !important;
    }
    .font-weight-bold {
        font-weight: bold !important;
    }
</style>

<script>
    document.getElementById('sugar_level').addEventListener('input', function() {
    document.getElementById('sugar_level_value').textContent = this.value;
});
document.getElementById('heart_rate').addEventListener('input', function() {
    document.getElementById('heart_rate_value').textContent = this.value;
});
document.getElementById('temperature').addEventListener('input', function() {
    document.getElementById('temperature_value').textContent = this.value;
});
document.getElementById('oxygen_level').addEventListener('input', function() {
    document.getElementById('oxygen_level_value').textContent = this.value;
});
document.getElementById('patient_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var uniqueId = selectedOption.getAttribute('data-unique-id');
    document.getElementById('unique_id').value = uniqueId;
});

document.getElementById('appointment_type').addEventListener('change', function() {
    var isEmergency = this.value === 'Emergency Visit';
    document.getElementById('is_emergency').value = isEmergency ? 1 : 0;
});

document.addEventListener('DOMContentLoaded', function() {
    flatpickr('.datepicker', {
        enableTime: true,
        dateFormat: 'Y-m-d'
    });
});
</script>
@endsection
