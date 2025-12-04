<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Scan Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->user->name }}</td>
                <td>{{ $record->user->email }}</td>
                <td>{{ ucfirst($record->status) }}</td>
                <td>{{ $record->scanned_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
