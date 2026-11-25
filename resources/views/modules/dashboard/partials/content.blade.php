<div class="my-3">
    <ul class="nav nav-tabs" id="dashboard-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link active text-body"
                id="dashboard-tab"
                data-bs-toggle="tab"
                data-bs-target="#dashboard-pane"
                type="button"
                role="tab"
                aria-controls="dashboard-pane"
                aria-selected="true"
            >
                Dashboard
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link text-body"
                id="current-status-tab"
                data-bs-toggle="tab"
                data-bs-target="#current-status-pane"
                type="button"
                role="tab"
                aria-controls="current-status-pane"
                aria-selected="false"
            >
                Current Status
            </button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="dashboard-tab-content">
        <div
            class="tab-pane fade show active"
            id="dashboard-pane"
            role="tabpanel"
            aria-labelledby="dashboard-tab"
            tabindex="0"
        >
            <div class="row g-3 align-items-start">
                <div class="col-lg-9">
                    @include('modules.dashboard.partials.financial-table')

                    <div class="card border rounded-3 shadow-sm mt-3">
                        <div class="card-body">
                            <p
                                class="small text-body-secondary"
                                id="dashboard-gantt-message"
                                role="status"
                            >
                                Loading project schedule…
                            </p>
                            <div
                                id="project-gantt"
                                data-dashboard-gantt
                                data-readonly="true"
                                data-source-url="{{ route('gantt.data', [], false) }}"
                                aria-label="Project schedule (read-only)"
                                aria-describedby="dashboard-gantt-message"
                            ></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card border rounded-3 shadow-sm" style="min-height: 400px;">
                        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                            @isset($initialCurrentStatusData)
                                @include('modules.dashboard.partials.current-status-sidebar', [
                                    'initialDashboardData' => $initialCurrentStatusData,
                                ])
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="tab-pane fade"
            id="current-status-pane"
            role="tabpanel"
            aria-labelledby="current-status-tab"
            tabindex="0"
        >
            @isset($initialCurrentStatusData)
                @include('modules.dashboard.partials.current-status', [
                    'initialDashboardData' => $initialCurrentStatusData,
                ])
            @endisset
        </div>
    </div>
</div>
