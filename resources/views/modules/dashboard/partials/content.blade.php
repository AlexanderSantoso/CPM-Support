<div class="my-3">
    <ul class="nav nav-tabs" id="dashboard-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active"
            id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-pane" type="button" role="tab" aria-controls="dashboard-pane" aria-selected="true">
            Dashboard
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="current-status-tab" data-bs-toggle="tab" data-bs-target="#current-status-pane" type="button" role="tab" aria-controls="current-status-pane" aria-selected="false">
            Current Status
        </button>
    </li>
</ul>

    <div class="tab-content pt-3" id="dashboard-tab-content">
    <div class="tab-pane fade show active" id="dashboard-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
        @include('modules.dashboard.partials.financial-table')
    </div>
    <div class="tab-pane fade" id="current-status-pane" role="tabpanel" aria-labelledby="current-status-tab" tabindex="0">
        @isset($initialCurrentStatusData)
            @include('modules.dashboard.partials.current-status', [
                'initialDashboardData' => $initialCurrentStatusData,
            ])
        @endisset
    </div>
</div>
</div>
