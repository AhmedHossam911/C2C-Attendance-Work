<table>
    <thead>
        <tr>
            <th>Committee</th>
            <th>Member Name</th>
            <th>Total Scans</th>
            <th>Present</th>
            <th>Late</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($committees as $committee)
            @foreach ($committee->users as $user)
                @php
                    $totalRecords = $user->attendanceRecords->count();
                    $present = $user->attendanceRecords->where('status', 'present')->count();
                    $late = $user->attendanceRecords->where('status', 'late')->count();
                @endphp
                <tr>
                    <td>{{ $committee->name }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $totalRecords }}</td>
                    <td>{{ $present }}</td>
                    <td>{{ $late }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
