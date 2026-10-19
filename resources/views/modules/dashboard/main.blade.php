@extends('layouts.main')

@section('content')
    @php
        $formatAmount = fn ($amount) => $amount === null
            ? ''
            : number_format($amount, 0, ',', '.');
    @endphp

    <style>
        .dashboard-cost-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 12px;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            box-shadow: var(--bs-box-shadow-sm);
        }

        .dashboard-cost-body {
            padding: 30px 30px 50px;
        }

        .dashboard-cost-table {
            width: 100%;
            min-width: 1050px;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 0;
            color: var(--bs-body-color);
            font-family: inherit;
            font-size: 15px;
        }

        .dashboard-cost-table th,
        .dashboard-cost-table td {
            padding: 10px 12px;
            border: 0;
            vertical-align: middle;
        }

        .dashboard-cost-table thead th {
            border-top: 1px solid var(--bs-border-color);
            border-bottom: 1px solid var(--bs-border-color);
            color: var(--bs-secondary-color);
            font-weight: 600;
            letter-spacing: 0;
            line-height: 1.5;
            text-align: center;
        }

        .dashboard-cost-table tbody td {
            border-bottom: 1px solid var(--bs-border-color);
        }

        .dashboard-cost-table .budget-column,
        .dashboard-cost-table .contract-column,
        .dashboard-cost-table .variation-column,
        .dashboard-cost-table .final-column {
            background: transparent;
        }

        .dashboard-cost-table .package-column { color: var(--bs-body-color); }
        .dashboard-cost-table .amount { text-align: right; white-space: nowrap; }

        #dashboard-tabs .nav-link {
            color: var(--bs-secondary-color);
        }

        #dashboard-tabs .nav-link.active {
            color: var(--bs-body-color);
            font-weight: 600;
        }

        .dashboard-package-link {
            padding: 0;
            border: 0;
            background: transparent;
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }

        .dashboard-package-link:hover,
        .dashboard-package-link:focus-visible {
            text-decoration: underline;
        }

        .dashboard-budget-link {
            display: block;
            width: 100%;
            text-align: right;
        }

        .dashboard-detail-modal-dialog {
            width: calc(100% - 16px);
            max-width: none;
            margin: 8px auto;
        }

        .dashboard-detail-modal-content {
            min-height: 205px;
            border: 0;
            border-radius: 18px;
            box-shadow: var(--bs-box-shadow);
        }

        .dashboard-detail-modal-header {
            min-height: 88px;
            justify-content: center;
            padding: 18px 60px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .dashboard-detail-modal-title {
            color: var(--bs-body-color);
            font-size: 30px;
            font-weight: 400;
            text-align: center;
        }

        .dashboard-detail-modal-header .btn-close {
            position: absolute;
            top: 28px;
            right: 28px;
        }

        .dashboard-detail-modal-body {
            min-height: 116px;
            padding: 24px 36px;
            color: var(--bs-body-color);
            font-size: 18px;
        }

        .dashboard-budget-modal-body {
            padding: 24px;
            overflow-x: auto;
        }

        .dashboard-budget-modal-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            color: var(--bs-body-color);
            font-size: 15px;
        }

        .dashboard-budget-modal-table th,
        .dashboard-budget-modal-table td {
            padding: 12px;
            border-bottom: 1px solid var(--bs-border-color);
            text-align: left;
            white-space: nowrap;
        }

        .dashboard-budget-modal-table th {
            font-weight: 600;
        }

        .dashboard-cost-table tfoot td {
            height: 52px;
            border-top: 1px solid var(--bs-border-color);
            border-bottom: 1px solid var(--bs-border-color);
            background: transparent;
            color: var(--bs-body-color);
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .dashboard-cost-body { padding: 32px 14px; }
            .dashboard-cost-table { font-size: 16px; }
        }
    </style>

    <div class="my-3">
        <ul class="nav nav-tabs" id="dashboard-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active"
                    id="dashboard-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#dashboard-panel"
                    type="button"
                    role="tab"
                    aria-controls="dashboard-panel"
                    aria-selected="true"
                >
                    Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="current-status-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#current-status-panel"
                    type="button"
                    role="tab"
                    aria-controls="current-status-panel"
                    aria-selected="false"
                >
                    Current Status
                </button>
            </li>
        </ul>

        <div class="tab-content" id="dashboard-tabs-content">
            <div
                class="tab-pane fade show active"
                id="dashboard-panel"
                role="tabpanel"
                aria-labelledby="dashboard-tab"
                tabindex="0"
            >
                <div class="card dashboard-cost-card overflow-hidden">
                    <div class="card-body dashboard-cost-body">
                        <div class="table-responsive">
                            <table class="dashboard-cost-table">
                                <colgroup>
                                    <col style="width: 15.3%;">
                                    <col style="width: 12.9%;">
                                    <col style="width: 14.6%;">
                                    <col style="width: 14.5%;">
                                    <col style="width: 12.8%;">
                                    <col style="width: 13.7%;">
                                    <col style="width: 16.2%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" class="package-column">Package Name</th>
                                        <th scope="col" class="budget-column">Budget Plan</th>
                                        <th scope="col" class="contract-column">Estimated<br>Contract</th>
                                        <th scope="col" class="contract-column">Contract Amount<br>Excl VAT</th>
                                        <th scope="col" class="variation-column">Estimated<br>Variation</th>
                                        <th scope="col" class="variation-column">Approved<br>Variation</th>
                                        <th scope="col" class="final-column">Estimated Final<br>Account</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costRows as $row)
                                        <tr>
                                            <td class="package-column">
                                                <button
                                                    class="dashboard-package-link"
                                                    type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#financialReportDetailModal"
                                                    data-package-name="{{ $row['package'] }}"
                                                >{{ $row['package'] }}</button>
                                            </td>
                                            <td class="budget-column amount">
                                                @if ($row['budget_plan'] !== null)
                                                    <button
                                                        class="dashboard-package-link dashboard-budget-link"
                                                        data-budget-plan-details="{{ json_encode($row['budget_plan_details']) }}"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#budgetPlanDetailModal"
                                                        data-package-name="{{ $row['package'] }}"
                                                    >{{ $formatAmount($row['budget_plan']) }}</button>
                                                @endif
                                            </td>
                                            <td class="contract-column amount">{{ $formatAmount($row['estimated_contract']) }}</td>
                                            <td class="contract-column amount">
                                                @if (!empty($row['contract_details']))
                                                    <button
                                                        class="dashboard-package-link dashboard-budget-link"
                                                        data-contract-details="{{ json_encode($row['contract_details']) }}"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#contractDetailModal"
                                                    >{{ $formatAmount($row['contract_amount_excl_vat']) }}</button>
                                                @else
                                                    {{ $formatAmount($row['contract_amount_excl_vat']) }}
                                                @endif
                                            </td>
                                            <td class="variation-column amount">
                                                @if (!empty($row['change_order_details']))
                                                    <button
                                                        class="dashboard-package-link dashboard-budget-link"
                                                        data-change-order-details="{{ json_encode($row['change_order_details']) }}"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#estimatedVariationDetailModal"
                                                    >{{ $formatAmount($row['estimated_variation']) }}</button>
                                                @else
                                                    {{ $formatAmount($row['estimated_variation']) }}
                                                @endif
                                            </td>
                                            <td class="variation-column amount">{{ $formatAmount($row['approved_variation']) }}</td>
                                            <td class="final-column amount">{{ $formatAmount($row['final_account']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>Total</td>
                                        <td class="amount">{{ $formatAmount($totals['budget_plan']) }}</td>
                                        <td class="amount">{{ $formatAmount($totals['estimated_contract']) }}</td>
                                        <td class="amount">{{ $formatAmount($totals['contract_amount_excl_vat']) }}</td>
                                        <td class="amount">{{ $formatAmount($totals['estimated_variation']) }}</td>
                                        <td class="amount">{{ $formatAmount($totals['approved_variation']) }}</td>
                                        <td class="amount">{{ $formatAmount($totals['final_account']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="tab-pane fade"
                id="current-status-panel"
                role="tabpanel"
                aria-labelledby="current-status-tab"
                tabindex="0"
            ></div>
        </div>
    </div>

    <div
        class="modal fade"
        id="financialReportDetailModal"
        tabindex="-1"
        aria-labelledby="financialReportDetailModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog dashboard-detail-modal-dialog">
            <div class="modal-content dashboard-detail-modal-content">
                <div class="modal-header dashboard-detail-modal-header">
                    <h2 class="modal-title dashboard-detail-modal-title" id="financialReportDetailModalLabel">
                        Financial Report Detail
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body dashboard-detail-modal-body">
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

    <div
        class="modal fade"
        id="budgetPlanDetailModal"
        tabindex="-1"
        aria-labelledby="budgetPlanDetailModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog dashboard-detail-modal-dialog">
            <div class="modal-content dashboard-detail-modal-content">
                <div class="modal-header dashboard-detail-modal-header">
                    <div class="text-center">
                        <h2 class="modal-title dashboard-detail-modal-title" id="budgetPlanDetailModalLabel">
                            Financial Report Detail
                        </h2>
                        <!-- <div class="small text-body-secondary" data-budget-package></div> -->
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body dashboard-budget-modal-body">
                    <table class="dashboard-budget-modal-table">
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
    <div
    class="modal fade"
    id="contractDetailModal"
    tabindex="-1"
    aria-labelledby="contractDetailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog dashboard-detail-modal-dialog">
        <div class="modal-content dashboard-detail-modal-content">
            <div class="modal-header dashboard-detail-modal-header">
                <h2 class="modal-title dashboard-detail-modal-title" id="contractDetailModalLabel">
                    Financial Report Detail
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body dashboard-budget-modal-body">
                <table class="dashboard-budget-modal-table">
                    <thead>
                        <tr>
                            <th>Contract No</th>
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
    tabindex="-1"
    aria-labelledby="estimatedVariationDetailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog dashboard-detail-modal-dialog">
        <div class="modal-content dashboard-detail-modal-content">
            <div class="modal-header dashboard-detail-modal-header">
                <h2 class="modal-title dashboard-detail-modal-title" id="estimatedVariationDetailModalLabel">
                    Financial Report Detail
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body dashboard-budget-modal-body">
                <table class="dashboard-budget-modal-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Date Raised</th>
                            <th rowspan="2">Input By</th>
                            <th rowspan="2">CR No</th>
                            <th rowspan="2">Contract No</th>
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

    <script>
        const financialReportDetailModal = document.getElementById('financialReportDetailModal');

        financialReportDetailModal.addEventListener('show.bs.modal', (event) => {
            const selectedPackage = event.relatedTarget.getAttribute('data-package-name');
            financialReportDetailModal.querySelector('[data-selected-package]').textContent = selectedPackage;
        });

        const budgetPlanDetailModal = document.getElementById('budgetPlanDetailModal');

        budgetPlanDetailModal.addEventListener('show.bs.modal', (event) => {
            const details = JSON.parse(
                event.relatedTarget.dataset.budgetPlanDetails || '[]'
            );
            const tableBody = budgetPlanDetailModal.querySelector('tbody');

            tableBody.replaceChildren();

            details.forEach((detail) => {
                const row = document.createElement('tr');
                const values = [
                    detail.issued_date,
                    detail.work_package,
                    detail.budget_estimate,
                    detail.status,
                    detail.assigned_to,
                ];

                values.forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = value ?? '';
                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });
        });

        const contractDetailModal = document.getElementById('contractDetailModal');

        contractDetailModal.addEventListener('show.bs.modal', (event) => {
            const details = JSON.parse(
                event.relatedTarget.dataset.contractDetails || '[]'
            );
            const tableBody = contractDetailModal.querySelector('tbody');

            tableBody.replaceChildren();

            details.forEach((detail) => {
                const row = document.createElement('tr');
                const values = [
                    detail.contract_no,
                    detail.issued_date,
                    detail.vendor,
                    detail.address,
                    detail.work_package,
                    detail.start_date,
                    detail.end_date,
                    detail.contract_amount_excl_vat,
                    detail.contract_amount_incl_vat,
                    detail.down_payment,
                    detail.retention,
                    '',
                ];

                values.forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = value ?? '';
                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });
        });

        const estimatedVariationDetailModal = document.getElementById('estimatedVariationDetailModal');

        estimatedVariationDetailModal.addEventListener('show.bs.modal', (event) => {
            const details = JSON.parse(
                event.relatedTarget.dataset.changeOrderDetails || '[]'
            );
            const tableBody = estimatedVariationDetailModal.querySelector('tbody');

            tableBody.replaceChildren();

            details.forEach((detail) => {
                const row = document.createElement('tr');
                const values = [
                    detail.date_raised,
                    detail.input_by,
                    detail.cr_no,
                    detail.contract_no,
                    detail.vendor,
                    detail.work_package,
                    detail.building_phase,
                    detail.type_of_change,
                    detail.originator,
                    detail.priority,
                    detail.description,
                    detail.estimated_variation,
                    detail.potential_impact,
                    detail.cost_estimation,
                    detail.time_estimation,
                    detail.pm_comment,
                    detail.consultant_comment,
                    detail.qs_comment,
                    detail.client_comment,
                    detail.status,
                    detail.assigned_to,
                ];

                values.forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = value === null || value === undefined || value === '' ? '-' : value;
                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });
        });
    </script>
@endsection
