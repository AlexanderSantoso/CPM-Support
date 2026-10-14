@php
    $name = fn ($u) => $u ? trim($u->first_name . ' ' . $u->last_name) : '';
@endphp
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Issued Date</th>
            <th>Work Package</th>
            <th>Budget Estimate</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Last Updated By</th>
            <th>Last Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($plans as $plan)
            @php
                $latest = $plan->latestActivity;
                $updatedBy = $latest?->creator ?? $plan->creator;
                $updatedAt = $latest?->created_at ?? $plan->created_at;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $plan->issued_date?->format('d M Y') }}</td>
                <td>{{ $plan->workPackage?->work_package }}</td>
                <td>{{ $plan->budget_estimate }}</td>
                <td>{{ $plan->logStatus?->name }}</td>
                <td>{{ $name($latest?->assignee) }}</td>
                <td>{{ $name($updatedBy) }}</td>
                <td>{{ $updatedAt?->format('d M Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
