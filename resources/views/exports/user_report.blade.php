<table>
    <thead>
        <tr>
            <th>Visit Date</th>
            <th>Doctor</th>
            <th>Complaint</th>
            <th>Diagnosis</th>
            <th>Medications</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($userVisits as $visit)
        <tr>
            <td>{{ $visit['Visit Date'] }}</td>
            <td>{{ $visit['Doctor Name'] }}</td>
            <td>{{ $visit['Symptoms Reported'] }}</td>
            <td>{{ $visit['Diagnosis'] }}</td>
            <td>{{ $visit['Medications Prescribed'] }}</td>
            <td>{{ $visit['Ongoing Treatments'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
