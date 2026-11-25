@php
    $cardsByTitle = collect($initialDashboardData['administration'] ?? [])
        ->merge($initialDashboardData['compliance'] ?? [])
        ->merge($initialDashboardData['drawing'] ?? [])
        ->merge($initialDashboardData['material'] ?? [])
        ->keyBy('title');

    $sidebarCardTitles = [
        'Project Activity Tracker',
        'Non-Conformance Report (NCR)',
        'Quality Inspection',
        'Conceptual Design',
        'Schematic Design',
        'Design Development',
        'For Tender',
        'For Construction',
        'Pre Construction Drawing',
        'Shop Drawing',
        'As Built Drawing',
        'Material Approval',
    ];

    $cards = collect($sidebarCardTitles)
        ->map(fn ($title) => $cardsByTitle->get($title))
        ->filter()
        ->values();
@endphp

<div class="d-flex flex-column gap-3">
    @foreach ($cards as $card)
        <x-dashboard.donut-card
            :label="$card['title']"
            :total="$card['count']"
            :statuses="$card['statuses']"
            :show-chart="$card['show_chart']"
            :has-data-source="$card['has_data_source'] ?? true"
            :icon="$card['icon'] ?? null"
        />
    @endforeach
</div>
