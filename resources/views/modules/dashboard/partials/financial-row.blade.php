@php
    $packageFilter = ['work_package_id' => $row['work_package_id']];
    $columns = [
        'budget_plan' => [
            $row['budget_plan'] !== null,
            'budgetPlanDetailModal',
            'budget-plan-data-url',
            route('dashboard.current-status.budget-plans', $packageFilter),
        ],
        'estimated_contract' => [
            $row['has_owner_estimate_details'],
            'estimatedContractDetailModal',
            'owner-estimate-data-url',
            route('dashboard.owner-estimates', $packageFilter),
        ],
        'contract_amount_excl_vat' => [
            $row['has_contract_details'],
            'contractDetailModal',
            'contract-data-url',
            route('dashboard.current-status.contracts', $packageFilter),
        ],
        'estimated_variation' => [
            $row['has_change_order_details'],
            'estimatedVariationDetailModal',
            'change-order-data-url',
            route('dashboard.current-status.change-orders', $packageFilter),
        ],
        'approved_variation' => [
            true,
            'approvedVariationDetailModal',
            'variation-order-data-url',
            route('dashboard.approved-variations', $packageFilter),
        ],
        'final_account' => [false, null, null, null],
    ];

    $columnClasses = [
        'budget_plan' => 'budget-column',
        'estimated_contract' => 'contract-column',
        'contract_amount_excl_vat' => 'contract-column',
        'estimated_variation' => 'variation-column',
        'approved_variation' => 'variation-column',
        'final_account' => 'final-column',
    ];
@endphp

<tr>
    <td class="package-column">
        <button
            class="dashboard-package-link"
            type="button"
            data-package-link
            data-package-name="{{ $row['package'] }}"
            data-bs-toggle="modal"
            data-bs-target="#financialReportDetailModal"
        >
            {{ $row['package'] }}
        </button>
    </td>
    @foreach ($columns as $field => [$available, $modal, $dataAttribute, $url])
        <td
            class="{{ $columnClasses[$field] }} amount"
            data-amount="{{ $field }}"
        >
            @if ($available)
                <button
                    class="dashboard-package-link dashboard-budget-link"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#{{ $modal }}"
                    data-{{ $dataAttribute }}="{{ $url }}"
                    data-package-name="{{ $row['package'] }}"
                >
                    {{ Helper::formatRupiah($row[$field]) }}
                </button>
            @elseif ($row[$field] !== null)
                {{ Helper::formatRupiah($row[$field]) }}
            @endif
        </td>
    @endforeach
</tr>
