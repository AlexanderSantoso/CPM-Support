<section class="card mt-2 border rounded-3 overflow-hidden bg-body text-body shadow-none">
    <div class="card-body d-flex flex-column gap-4 p-3 p-md-4">
        <p class="text-danger" data-overview-error role="alert" hidden></p>
        <x-dashboard.status-category
            label="COST AND CONTRACT"
            :cards="$initialDashboardData['data'] ?? []"
        />
        <x-dashboard.status-category
            label="ADMINISTRATION"
            :cards="$initialDashboardData['administration'] ?? []"
        />
        <x-dashboard.status-category
            label="COMPLIANCE"
            :cards="$initialDashboardData['compliance'] ?? []"
        />
        <x-dashboard.status-category
            label="DRAWING"
            :cards="$initialDashboardData['drawing'] ?? []"
        />
        <x-dashboard.status-category
            label="MATERIAL"
            :cards="$initialDashboardData['material'] ?? []"
        />
    </div>
</section>
<section class="card mt-3 border rounded-3 bg-body">
    <div class="card-body">
        <div class="row g-2 align-items-end mb-2">
            <div class="col-lg-4">
                <div class="row align-items-center">
                    <label for="contractorFilter" class="col-5 form-label mb-0">
                        Contractor
                    </label>
                    <div class="col-7">
                        <select
                            id="contractorFilter"
                            class="form-select border-0 border-bottom rounded-0 shadow-none"
                        >
                            <option value="">Select Contractor</option>
                            @foreach ($contractors as $contractor)
                                <option
                                    value="{{ $contractor['id'] }}"
                                    @selected($contractor['id'] === ($dashboardDefaults['contractor']['id'] ?? null))
                                >
                                    {{ $contractor['text'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row align-items-center">
                    <label for="workPackageFilter" class="col-5 form-label mb-0">
                        Work Package
                    </label>
                    <div class="col-7">
                        <select
                            id="workPackageFilter"
                            class="form-select border-0 border-bottom rounded-0 shadow-none"
                        >
                            <option value="">Select Work Package</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-start">
            <div class="col-lg-9">
                <div id="monthly-signups-chart"></div>
            </div>
            <div class="col-lg-3">
                <h5 class="text-primary">Duration</h5>
                <p>Project Start: <span id="sCurveStartDate">-</span></p>
                <p>Project End: <span id="sCurveEndDate">-</span></p>
                <p>
                    Contract:
                    <span id="sCurveDurationDays">-</span>
                    <span id="sCurveDurationUnit">Days</span>
                </p>
            </div>
        </div>
    </div>
</section>
