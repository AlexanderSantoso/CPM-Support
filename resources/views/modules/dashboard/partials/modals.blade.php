    <div
        class="modal fade"
        id="financialReportDetailModal"
        tabindex="-1"
        aria-labelledby="financialReportDetailModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog mw-100 m-2">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header position-relative justify-content-center p-4">
                    <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="financialReportDetailModalLabel">
                        Financial Report Detail
                    </h2>
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-body fs-5">
                    <div class="row g-3">
                        <div class="col-md-2">Work Package</div>
                        <div class="col-md-3" data-selected-package></div>
                        <div class="col-md-2">Company</div>
                        <div class="col-md-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($activeDashboardSection === 'dashboard')
    <div
        class="modal fade"
        id="budgetPlanDetailModal"
        data-budget-plan-data-url="{{ route('dashboard.current-status.budget-plans') }}"
        tabindex="-1"
        aria-labelledby="budgetPlanDetailModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog mw-100 m-2">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header position-relative justify-content-center p-4">
                    <div class="text-center">
                        <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="budgetPlanDetailModalLabel">
                            Budget Plan Details
                        </h2>
                    </div>
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 overflow-x-auto">
                    <table class="table w-100 mb-0 align-middle text-nowrap dashboard-budget-modal-table" data-budget-plan-table>
                        <thead>
                            <tr>
                                <th scope="col">Issued Date</th>
                                <th scope="col">Work Package</th>
                                <th scope="col">Budget Estimate</th>
                                <th scope="col">Status</th>
                                <th scope="col">Assigned To</th>
                            </tr>
                        </thead>
                            <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="estimatedContractDetailModal" tabindex="-1"
    data-owner-estimate-data-url="{{ route('dashboard.owner-estimates') }}"
    aria-labelledby="estimatedContractDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog mw-100 m-2">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header position-relative justify-content-center p-4">
                <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="estimatedContractDetailModalLabel">
                    Estimated Contract Details
                </h2>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 overflow-x-auto">
                <table class="table w-100 mb-0 align-middle text-nowrap dashboard-budget-modal-table" data-owner-estimate-table>
                    <thead>
                        <tr>
                            <th scope="col">Issued Date</th>
                            <th scope="col">Work Package</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Estimated Contract</th>
                            <th scope="col">Status</th>
                            <th scope="col">Assigned To</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    @endif

    <div
    class="modal fade"
    id="contractDetailModal"
    data-contract-data-url="{{ route('dashboard.current-status.contracts') }}"
    tabindex="-1"
    aria-labelledby="contractDetailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog mw-100 m-2">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header position-relative justify-content-center p-4">
                <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="contractDetailModalLabel">
                    Financial Report Detail
                </h2>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 overflow-x-auto">
                <table class="table w-100 mb-0 align-middle text-nowrap dashboard-budget-modal-table" data-contract-table>
                    <thead>
                        <tr>
                            <th>Contract Number</th>
                            <th>Issued Date</th>
                            <th>Vendor</th>
                            <th>Address</th>
                            <th>Work Package</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Contract Amount Excl VAT</th>
                            <th>Contract Amount Incl VAT</th>
                            <th>Down Payment</th>
                            <th>Retention</th>
                            <th>Term of Payment</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="estimatedVariationDetailModal"
    data-change-order-data-url="{{ route('dashboard.current-status.change-orders') }}"
    tabindex="-1"
    aria-labelledby="estimatedVariationDetailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog mw-100 m-2">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header position-relative justify-content-center p-4">
                <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="estimatedVariationDetailModalLabel">
                    Financial Report Detail
                </h2>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 overflow-x-auto">
                <table class="table w-100 mb-0 align-middle text-nowrap dashboard-budget-modal-table" data-change-order-table>
                    <thead>
                        <tr>
                            <th rowspan="2">Date Raised</th>
                            <th rowspan="2">Input By</th>
                            <th rowspan="2">Change Order Number</th>
                            <th rowspan="2">Contract Number</th>
                            <th rowspan="2">Vendor</th>
                            <th rowspan="2">Work Package</th>
                            <th rowspan="2">Building/Phase</th>
                            <th rowspan="2">Type of Change</th>
                            <th rowspan="2">Originator of Change</th>
                            <th rowspan="2">Priority</th>
                            <th rowspan="2">Description</th>
                            <th rowspan="2">Estimated Variation</th>
                            <th rowspan="2">Potential Impact on Project</th>
                            <th colspan="2">Cost Assessment</th>
                            <th colspan="4">Comments</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Assigned To</th>
                        </tr>
                        <tr>
                            <th>Cost Estimation</th>
                            <th>Time Estimation (Days)</th>
                            <th>Project Manager</th>
                            <th>Consultant Design/Specialist</th>
                            <th>Quantity Surveyor</th>
                            <th>Client</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approvedVariationDetailModal" tabindex="-1"
    data-variation-order-data-url="{{ route('dashboard.approved-variations') }}"
    aria-labelledby="approvedVariationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog mw-100 m-2">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header position-relative justify-content-center p-4">
                <h2 class="modal-title fs-3 fw-normal text-center text-body px-4" id="approvedVariationDetailModalLabel">
                    Approved Variation Details
                </h2>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 overflow-x-auto">
                <table class="table w-100 mb-0 align-middle text-nowrap dashboard-budget-modal-table" data-variation-order-table>
                    <thead>
                        <tr>
                            <th>Variation Order Number</th>
                            <th>Vendor</th>
                            <th>Work Package</th>
                            <th>Site Instruction Number</th>
                            <th>Change Order Number</th>
                            <th>Approved Variation</th>
                            <th>Status</th>
                            <th>Assign to</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
