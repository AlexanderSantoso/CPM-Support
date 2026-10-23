@props(['label', 'cards'])

<details open>
    <summary class="fs-6 user-select-none">{{ $label }}</summary>
    <div class="row g-4 mt-0">
        @foreach ($cards as $card)
            <div class="col-6 col-xl-4">
                <x-dashboard.donut-card
                    :label="$card['title']"
                    :total="$card['count']"
                    :statuses="$card['statuses']"
                    :show-chart="$card['show_chart']"
                    :has-data-source="$card['has_data_source'] ?? true"/>
            </div>
        @endforeach
    </div>
</details>
