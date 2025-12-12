<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Scan Time</th>
            <th>Notes</th>
            <th>Scanned By</th>
            <th>Updated By</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->user->name }}</td>
                <td>{{ $record->user->email }}</td>
                <td>{{ ucfirst($record->status) }}</td>
                <td>{{ $record->scanned_at->format('h:i:s A') }}</td>
                <td>{{ $record->notes }}</td>
                <td>{{ $record->scanner ? $record->scanner->name : '-' }}</td>
                <td>{{ $record->updater ? $record->updater->name : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
