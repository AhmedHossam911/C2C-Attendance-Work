<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Total Scans</th>
            <th>Last Scan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($members as $member)
            @php
                $lastScan = $member->attendanceRecords->sortByDesc('created_at')->first();
            @endphp
            <tr>
                <td>{{ $member->id }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ $member->role }}</td>
                <td>{{ $member->attendanceRecords->count() }}</td>
                <td>{{ $lastScan ? $lastScan->created_at->format('Y-m-d h:i A') : 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
