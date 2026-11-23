@php
    $cards = [
        $initialDashboardData['administration'][5],
        $initialDashboardData['compliance'][2],   
        $initialDashboardData['compliance'][4],   
        $initialDashboardData['drawing'][1],   
        $initialDashboardData['drawing'][2],   
        $initialDashboardData['drawing'][3],   
        $initialDashboardData['drawing'][4],   
        $initialDashboardData['drawing'][5],   
        $initialDashboardData['drawing'][6],   
        $initialDashboardData['drawing'][7],   
        $initialDashboardData['drawing'][8],   
        $initialDashboardData['material'][0],     
    ];
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