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

.dashboard-cost-table .package-column {
    color: var(--bs-body-color);
}

.dashboard-cost-table .amount {
    text-align: right;
    white-space: nowrap;
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

.dashboard-cost-table tfoot td {
    height: 52px;
    border-top: 1px solid var(--bs-border-color);
    border-bottom: 1px solid var(--bs-border-color);
    background: transparent;
    color: var(--bs-body-color);
    font-weight: 700;
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
    border: 0;
    text-align: left;
    white-space: nowrap;
}

.dashboard-budget-modal-table[data-budget-plan-table] tbody td:first-child,
.dashboard-budget-modal-table th.dt-body-right,
.dashboard-budget-modal-table td.dt-body-right {
    text-align: right;
}

.dashboard-budget-modal-table th {
    font-weight: 600;
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

.dashboard-budget-modal-table td.dashboard-estimated-contract-remarks {
    min-width: 160px;
    max-width: 280px;
}

.dashboard-budget-modal-table td.dashboard-change-order-comment {
    width: 240px;
    min-width: 200px;
    max-width: 280px;
}

.dashboard-budget-modal-table th.dt-body-right {
    width: 1%;
    min-width: 160px;
}

.dashboard-budget-modal-table td.dt-body-right {
    font-variant-numeric: tabular-nums;
}

.dashboard-budget-modal-table td.dashboard-payment-terms {
    width: 240px;
    min-width: 200px;
    max-width: 280px;
}

.dashboard-payment-terms-content {
    width: 240px;
    max-width: 100%;
    white-space: normal;
    overflow-wrap: anywhere;
}

@media (max-width: 767.98px) {
    .dashboard-cost-body {
        padding: 32px 14px;
    }

    .dashboard-cost-table {
        font-size: 16px;
    }
}
