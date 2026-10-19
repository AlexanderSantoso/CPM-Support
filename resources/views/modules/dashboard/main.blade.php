@extends('layouts.main')

@section('content')
    @php
        $formatAmount = fn ($amount) => $amount === null
            ? ''
            : number_format($amount, 0, '.', ',');
    @endphp

    <style>
        .dashboard-cost-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 17px;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            box-shadow: var(--bs-box-shadow-sm);
        }

        .dashboard-cost-header {
            min-height: 10px;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 15px;
            background: linear-gradient(110deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary) 60%, var(--bs-dark)));
            color: #fff;
            font-family: inherit;
            font-size: 23px;
            font-weight: 700;
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
            border-bottom: 2px solid var(--bs-border-color);
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1.5;
            text-align: center;
        }

        .dashboard-cost-table tbody tr:nth-child(1) { height: 40px; }
        .dashboard-cost-table tbody tr:nth-child(2) { height: 40px; }
        .dashboard-cost-table tbody tr:nth-child(3) { height: 60px; }

        .dashboard-cost-table .budget-column { background: var(--bs-secondary-bg); }
        .dashboard-cost-table .contract-column { background: var(--bs-info-bg-subtle); }
        .dashboard-cost-table .variation-column {
            background: color-mix(in srgb, var(--bs-info-bg-subtle) 70%, var(--bs-info) 30%);
        }
        .dashboard-cost-table .final-column { background: var(--bs-primary-bg-subtle); }
        .dashboard-cost-table .package-column { color: var(--bs-primary-text-emphasis); }
        .dashboard-cost-table .amount { text-align: right; white-space: nowrap; }

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

        .dashboard-cost-table tfoot td {
            height: 52px;
            border-top: 0;
            border-bottom: 1px solid var(--bs-border-color);
            background: var(--bs-secondary-bg);
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
                    <div class="card-header dashboard-cost-header border-0">
                        <span>Cost -</span>
                        <svg width="37" height="31" viewBox="0 0 37 31" fill="none" aria-hidden="true">
                            <rect x="1.5" y="1.5" width="34" height="28" rx="3" stroke="white" stroke-width="2.5" />
                            <circle cx="10" cy="9" r="2.5" stroke="white" stroke-width="2" />
                            <path d="M4.5 25L13 16.5L18 21L24 14L32 23" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

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
                                        <th scope="col" class="budget-column">Budget Plan<br>(IDR)</th>
                                        <th scope="col" class="contract-column">Estimated<br>Contract<br>(IDR)</th>
                                        <th scope="col" class="contract-column">Approved<br>Contract (IDR)</th>
                                        <th scope="col" class="variation-column">Estimated<br>Variation</th>
                                        <th scope="col" class="variation-column">Approved<br>Variation<br>(IDR)</th>
                                        <th scope="col" class="final-column">Estimated Final<br>Account (IDR)</th>
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
                                            <td class="budget-column amount">{{ $formatAmount($row['budget_plan']) }}</td>
                                            <td class="contract-column amount">{{ $formatAmount($row['estimated_contract']) }}</td>
                                            <td class="contract-column amount">{{ $formatAmount($row['approved_contract']) }}</td>
                                            <td class="variation-column amount">{{ $formatAmount($row['estimated_variation']) }}</td>
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
                                        <td class="amount">{{ $formatAmount($totals['approved_contract']) }}</td>
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

    <script>
        const financialReportDetailModal = document.getElementById('financialReportDetailModal');

        financialReportDetailModal.addEventListener('show.bs.modal', (event) => {
            const selectedPackage = event.relatedTarget.getAttribute('data-package-name');
            financialReportDetailModal.querySelector('[data-selected-package]').textContent = selectedPackage;
        });
    </script>
@endsection
