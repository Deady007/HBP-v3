@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4 text-primary font-weight-bold">Create Medical Visit</h1>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-lg transition-card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('medical_visit.store') }}" method="POST" onsubmit="visitSubmitBtn.disabled=true;visitSubmitBtn.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span> Submitting...';">
                        @csrf
                        <div class="patient-details mb-4">
                            <h3 class="text-primary font-weight-bold">Patient Information</h3>
                            @php
                                $patients = auth()->user()->hasRole('Admin') ? \App\Models\Patient::all() : \App\Models\Patient::where('user_unique_id', auth()->user()->id)->get();
                            @endphp
                            <div class="form-group">
                                <label for="patient_id">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-control custom-select">
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" data-unique-id="{{ $patient->unique_id }}">{{ $patient->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="unique_id" id="unique_id" value="">
                        </div>

                        <div class="visit-details mb-4">
                            <h3 class="text-primary font-weight-bold">Visit Details</h3>
                            <div class="form-group">
                                <label for="preferred_visit_date">Preferred Visit Date</label>
                                <input type="date" name="preferred_visit_date" id="preferred_visit_date" class="form-control" value="{{ old('preferred_visit_date') }}" min="{{ \Carbon\Carbon::tomorrow()->toDateString() }}" required>
                            </div>
                            <div class="form-group">
                                <label for="preferred_time_slot">Preferred Time Slot</label>
                                <input type="time" name="preferred_time_slot" id="preferred_time_slot" class="form-control" value="{{ old('preferred_time_slot') }}" required>

                            </div>
                            <div class="form-group">
                                <label for="appointment_type">Appointment Type</label>
                                <select name="appointment_type" id="appointment_type" class="form-control" required>
                                    <option value="Routine Checkup" {{ old('appointment_type') == 'Routine Checkup' ? 'selected' : '' }}>Routine Checkup</option>
                                    <option value="Follow-up Visit" {{ old('appointment_type') == 'Follow-up Visit' ? 'selected' : '' }}>Follow-up Visit</option>
                                    <option value="Emergency Visit" {{ old('appointment_type') == 'Emergency Visit' ? 'selected' : '' }}>Emergency Visit</option>
                                    <option value="Other" {{ old('appointment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <input type="hidden" name="is_emergency" id="is_emergency" value="0">
                        </div>

                        <div class="medical-information mb-4">
                            <h3 class="text-primary font-weight-bold">Medical Information</h3>
                            <div class="form-group">
                                <label for="primary_complaint">Reason for Appointment (Primary Complaint)</label>
                                <textarea name="primary_complaint" id="primary_complaint" class="form-control" required>{{ old('primary_complaint') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="existing_conditions">Existing Medical Conditions (if any)</label>
                                <div>
                                    <label><input type="checkbox" name="existing_conditions[]" value="Diabetes" {{ is_array(old('existing_conditions')) && in_array('Diabetes', old('existing_conditions')) ? 'checked' : '' }}> Diabetes</label>
                                    <label><input type="checkbox" name="existing_conditions[]" value="Hypertension" {{ is_array(old('existing_conditions')) && in_array('Hypertension', old('existing_conditions')) ? 'checked' : '' }}> Hypertension</label>
                                    <label><input type="checkbox" name="existing_conditions[]" value="Heart Disease" {{ is_array(old('existing_conditions')) && in_array('Heart Disease', old('existing_conditions')) ? 'checked' : '' }}> Heart Disease</label>
                                    <label><input type="checkbox" name="existing_conditions[]" value="Asthma" {{ is_array(old('existing_conditions')) && in_array('Asthma', old('existing_conditions')) ? 'checked' : '' }}> Asthma</label>
                                    <label><input type="checkbox" name="existing_conditions[]" value="Other" {{ is_array(old('existing_conditions')) && in_array('Other', old('existing_conditions')) ? 'checked' : '' }}> Other</label>
                                </div>
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
                            <div class="form-group">
                                <label for="preferred_provider">Preferred Doctor/Nurse (if any)</label>
                                <input type="text" name="preferred_provider" id="preferred_provider" class="form-control" value="{{ old('preferred_provider') }}">
                            </div>
                        </div>

                        <div class="confirmation mb-4">
                            <h3 class="text-primary font-weight-bold">Appointment Confirmation</h3>
                            <div class="form-group">
                                <label for="terms_conditions">Do you agree to the terms and conditions of the visit?</label>
                                <div>
                                    <label><input type="radio" name="terms_conditions" value="Yes" {{ old('terms_conditions') == 'Yes' ? 'checked' : '' }}> Yes</label>
                                    <label><input type="radio" name="terms_conditions" value="No" {{ old('terms_conditions') == 'No' ? 'checked' : '' }}> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="patient_signature">Patient’s Signature</label>
                                <input type="text" name="patient_signature" id="patient_signature" class="form-control" value="{{ old('patient_signature') }}">
                            </div>
                            <div class="form-group">
                                <label for="signature_date">Date</label>
                                <input type="date" name="signature_date" id="signature_date" class="form-control" value="{{ old('signature_date', \Carbon\Carbon::today()->toDateString()) }}">
                            </div>
                        </div>

                        <button type="submit" id="visitSubmitBtn" class="btn btn-primary">Submit</button>
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
    .hover-effect:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }
</style>

<script>
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
    fetchUsersWithRole('doctor', 'doctor_id');
    fetchUsersWithRole('nurse', 'nurse_id');

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

    flatpickr('.datetimepicker', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i'
    });
});
</script>
@endsection
