@props(['label', 'cards'])

@if ($label === 'COST AND CONTRACT')
    <div class="d-flex align-items-start gap-3">
@endif

<details open @class(['flex-grow-1' => $label === 'COST AND CONTRACT'])>
    <summary class="fs-6 user-select-none">{{ $label }}</summary>
    <div class="current-status-content">
        <div class="row g-4 mt-0">
            @foreach ($cards as $card)
                <div class="col-6 col-md-4 col-xl-3">
                    <x-dashboard.donut-card
                        :label="$card['title']"
                        :total="$card['count']"
                        :statuses="$card['statuses']"
                        :show-chart="$card['show_chart']"
                        :has-data-source="$card['has_data_source'] ?? true"
                        :icon="$card['icon'] ?? null"
                    />
                </div>
            @endforeach
        </div>
    </div>
</details>

@if ($label === 'COST AND CONTRACT')
    <button
        type="button"
        id="toggleAllCurrentStatus"
        class="btn btn-link text-body text-decoration-none flex-shrink-0 p-0 fs-6"
    >
        <i id="toggleAllCurrentStatusIcon" class="ti ti-arrows-diagonal-2"></i>
        <span id="toggleAllCurrentStatusText">Collapse</span>
    </button>
    </div>
@endif
