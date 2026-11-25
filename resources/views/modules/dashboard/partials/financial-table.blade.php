<div
    class="card dashboard-cost-card overflow-hidden"
    data-financial-overview
    data-source-url="{{ route('dashboard.data') }}"
    data-budget-plan-url="{{ route('dashboard.current-status.budget-plans') }}"
    data-owner-estimate-url="{{ route('dashboard.owner-estimates') }}"
    data-contract-url="{{ route('dashboard.current-status.contracts') }}"
    data-change-order-url="{{ route('dashboard.current-status.change-orders') }}"
    data-approved-variation-url="{{ route('dashboard.approved-variations') }}"
>
    <div class="card-body dashboard-cost-body">
        <p
            class="text-danger"
            data-overview-error
            role="alert"
            hidden
        ></p>

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
                        <th scope="col" class="contract-column">
                            Estimated<br>Contract
                        </th>
                        <th scope="col" class="contract-column">
                            Contract Amount<br>Excl VAT
                        </th>
                        <th scope="col" class="variation-column">
                            Estimated<br>Variation
                        </th>
                        <th scope="col" class="variation-column">
                            Approved<br>Variation
                        </th>
                        <th scope="col" class="final-column">
                            Estimated Final<br>Account
                        </th>
                    </tr>
                </thead>
                <tbody data-financial-rows>
                    @forelse ($initialDashboardData['data'] as $row)
                        @include('modules.dashboard.partials.financial-row', ['row' => $row])
                    @empty
                        <tr>
                            <td class="p-3" colspan="7">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        @foreach ($initialDashboardData['totals'] as $field => $total)
                            <td
                                class="amount"
                                data-total="{{ $field }}"
                            >
                                {{ Helper::formatRupiah($total) }}
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<template data-financial-row-template>
    <tr>
        <td class="package-column">
            <button
                class="dashboard-package-link"
                type="button"
                data-package-link
                data-bs-toggle="modal"
                data-bs-target="#financialReportDetailModal"
            ></button>
        </td>
        <td class="budget-column amount" data-amount="budget_plan"></td>
        <td class="contract-column amount" data-amount="estimated_contract"></td>
        <td class="contract-column amount" data-amount="contract_amount_excl_vat"></td>
        <td class="variation-column amount" data-amount="estimated_variation"></td>
        <td class="variation-column amount" data-amount="approved_variation"></td>
        <td class="final-column amount" data-amount="final_account"></td>
    </tr>
</template>
