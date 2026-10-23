<div class="card border rounded-3 shadow-sm overflow-hidden bg-body text-body" data-financial-overview
    data-source-url="{{ route('dashboard.data') }}"
    data-budget-plan-url="{{ route('dashboard.current-status.budget-plans') }}"
    data-owner-estimate-url="{{ route('dashboard.owner-estimates') }}"
    data-contract-url="{{ route('dashboard.current-status.contracts') }}"
    data-change-order-url="{{ route('dashboard.current-status.change-orders') }}"
    data-approved-variation-url="{{ route('dashboard.approved-variations') }}">
    <div class="card-body p-3 p-md-4 pb-md-5">
        <p class="text-danger" data-overview-error role="alert" hidden></p>
        <div class="table-responsive">
            <table class="table w-100 mb-0 align-middle dashboard-cost-table">
                <colgroup>
                    <col style="width: 15.3%;">
                    <col style="width: 12.9%;">
                    <col style="width: 14.6%;">
                    <col style="width: 14.5%;">
                    <col style="width: 12.8%;">
                    <col style="width: 13.7%;">
                    <col style="width: 16.2%;">
                </colgroup>
                <thead class="text-center border-top border-bottom">
                    <tr>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Package Name</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Budget Plan</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Estimated<br>Contract</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Contract Amount<br>Excl VAT</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Estimated<br>Variation</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Approved<br>Variation</th>
                        <th scope="col" class="p-3 text-body-secondary fw-semibold">Estimated Final<br>Account</th>
                    </tr>
                </thead>
                <tbody data-financial-rows>
                    @forelse ($initialDashboardData['data'] as $row)
                        @include('modules.dashboard.partials.financial-row', ['row' => $row])
                    @empty
                        <tr><td class="p-3" colspan="7">No data found.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold border-top border-bottom">
                    <tr>
                        <td class="p-3">Total</td>
                        @foreach ($initialDashboardData['totals'] as $field => $total)
                            <td class="p-3 text-end text-nowrap" data-total="{{ $field }}">{{ Helper::formatRupiah($total) }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<template data-financial-row-template>
    <tr>
        <td class="p-3">
            <button class="btn btn-link text-body text-decoration-none text-start border-0 p-0" type="button" data-package-link
                data-bs-toggle="modal" data-bs-target="#financialReportDetailModal"></button>
        </td>
        <td class="p-3 text-end text-nowrap" data-amount="budget_plan"></td>
        <td class="p-3 text-end text-nowrap" data-amount="estimated_contract"></td>
        <td class="p-3 text-end text-nowrap" data-amount="contract_amount_excl_vat"></td>
        <td class="p-3 text-end text-nowrap" data-amount="estimated_variation"></td>
        <td class="p-3 text-end text-nowrap" data-amount="approved_variation"></td>
        <td class="p-3 text-end text-nowrap" data-amount="final_account"></td>
    </tr>
</template>
