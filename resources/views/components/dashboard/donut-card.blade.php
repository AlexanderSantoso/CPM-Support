@props([
    'label',
    'total' => null,
    'statuses' => [],
    'showChart' => true,
    'hasDataSource' => true,
    'href' => null,
])

<div class="card h-100 border shadow-sm rounded-4"
     data-chart-size="145"
     @if ($showChart) data-donut-component @endif>
    <div class="card-body d-flex flex-column p-3 p-md-4">
        <h5 class="card-title text-primary text-center text-break fs-6" title="{{ $label }}">
            {{ $label }}
        </h5>

        <p class="text-body-secondary text-center mb-3">
            Total Data: {{ $total ?? '-' }}
        </p>

        @if ($showChart)
            <div class="d-flex flex-column flex-grow-1 gap-3">
                <div class="d-flex justify-content-center w-100"
                     aria-label="Status {{ $label }}"
                     data-donut-chart>
                </div>

                <ul class="list-unstyled mb-0 small">
                    @foreach ($statuses as $status)
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <span
                                class="rounded-circle flex-shrink-0"
                                style="width: 8px; height: 8px;"
                                data-donut-swatch>
                            </span>
                            <span class="text-break">{{ $status->status }}</span>
                            <span class="ms-auto fw-semibold flex-shrink-0">
                                {{ $hasDataSource ? ($status->total ?? 0) : '-' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="d-flex justify-content-center py-4">
                <span
                    class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-3"
                    role="img"
                    aria-label="{{ $label }}">
                    <svg width="42" height="42" viewBox="0 0 16 16"
                        fill="currentColor" aria-hidden="true">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.154.896l-.3-.17c-1.226-.706-2.62.69-1.913 1.915l.17.3a1.464 1.464 0 0 1-.896 2.154l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .896 2.154l-.17.3c-.706 1.226.69 2.62 1.915 1.913l.3-.17a1.464 1.464 0 0 1 2.154.896l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.154-.896l.3.17c1.226.706 2.62-.69 1.913-1.915l-.17-.3a1.464 1.464 0 0 1 .896-2.154l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.896-2.154l.17-.3c.706-1.226-.69-2.62-1.915-1.913l-.3.17a1.464 1.464 0 0 1-2.154-.896zM8 10.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5"/>
                    </svg>
                </span>
            </div>
        @endif

        {{ $slot }}

    </div>

    <script type="application/json" data-donut-statuses>
        @json($statuses)
    </script>
</div>

@once
    <script src="{{ asset('dist/external_plugins/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const theme = getComputedStyle(document.documentElement);

            document.querySelectorAll('[data-donut-component]').forEach((component) => {
                const statuses = JSON.parse(
                    component.querySelector('[data-donut-statuses]').textContent
                );

                const colors = statuses.map((status) => {
                    const variant = String(status.bg_color ?? '').replace(/^light-/, '');
                    return theme.getPropertyValue(`--bs-${variant}`).trim()
                        || theme.getPropertyValue('--bs-secondary').trim();
                });

                component.querySelectorAll('[data-donut-swatch]').forEach((swatch, index) => {
                    swatch.style.backgroundColor = colors[index];
                });

                const hasData = statuses.some((status) => (status.total ?? 0) > 0);
                const chartElement = component.querySelector('[data-donut-chart]');
                const chartSize = Math.min(Number(component.dataset.chartSize), chartElement.clientWidth);
                const chart = new ApexCharts(chartElement, {
                    chart: {
                        type: 'donut', width: chartSize, height: chartSize + 5,
                        animations: { enabled: false }, toolbar: { show: false },
                    },
                    series: hasData ? statuses.map((status) => Number(status.total ?? 0)) : [1],
                    labels: hasData ? statuses.map((status) => status.status) : ['No data'],
                    colors: hasData ? colors : ['#e5e7eb'],
                    dataLabels: { enabled: false },
                    legend: { show: false },
                    stroke: { width: 0 },
                    plotOptions: { pie: { expandOnClick: false, donut: { size: '58%' } } },
                    tooltip: { enabled: hasData, y: { formatter: (value) => `${value}` } },
                });

                chart.render();
            });
        });
    </script>
@endonce
