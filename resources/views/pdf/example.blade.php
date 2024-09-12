<h1>Timesheets</h1>
<table>
    <thead>
        <th>Calendar</th>
        <th>Type</th>
        <th>Check-in</th>
        <th>Check-out</th>
    </thead>
    <tbody>
        @foreach ($timesheets as $timesheet)
            <tr>
                <td>{{ $timesheet->calendar->name }}</td>
                <td>{{ $timesheet->type }}</td>
                <td>{{ $timesheet->day_in }}</td>
                <td>{{ $timesheet->day_out }}</td>
            </tr>
        @endforeach
    </tbody>
</table>