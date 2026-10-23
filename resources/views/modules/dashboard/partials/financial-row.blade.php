@php
    $packageFilter = ['work_package_id' => $row['work_package_id']];
    $columns = [
        'budget_plan' => [$row['budget_plan'] !== null, 'budgetPlanDetailModal', 'budget-plan-data-url', route('dashboard.current-status.budget-plans', $packageFilter)],
        'estimated_contract' => [$row['estimated_contract'] !== null, 'estimatedContractDetailModal', 'owner-estimate-data-url', route('dashboard.owner-estimates', $packageFilter)],
        'contract_amount_excl_vat' => [$row['has_contract_details'], 'contractDetailModal', 'contract-data-url', route('dashboard.current-status.contracts', $packageFilter)],
        'estimated_variation' => [$row['has_change_order_details'], 'estimatedVariationDetailModal', 'change-order-data-url', route('dashboard.current-status.change-orders', $packageFilter)],
        'approved_variation' => [true, 'approvedVariationDetailModal', 'variation-order-data-url', route('dashboard.approved-variations', $packageFilter)],
        'final_account' => [false, null, null, null],
    ];
@endphp

<tr>
    <td class="p-3">
        <button class="btn btn-link text-body text-decoration-none text-start border-0 p-0" type="button" data-package-link
            data-package-name="{{ $row['package'] }}"
            data-bs-toggle="modal" data-bs-target="#financialReportDetailModal">
            {{ $row['package'] }}
        </button>
    </td>
    @foreach ($columns as $field => [$available, $modal, $dataAttribute, $url])
        <td class="p-3 text-end text-nowrap" data-amount="{{ $field }}">
            @if ($available)
                <button class="btn btn-link text-body text-decoration-none text-end border-0 p-0 d-block w-100" type="button"
                    data-bs-toggle="modal" data-bs-target="#{{ $modal }}"
                    data-{{ $dataAttribute }}="{{ $url }}"
                    data-package-name="{{ $row['package'] }}">
                    {{ Helper::formatRupiah($row[$field]) }}
                </button>
            @elseif ($row[$field] !== null)
                {{ Helper::formatRupiah($row[$field]) }}
            @endif
        </td>
    @endforeach
</tr>
