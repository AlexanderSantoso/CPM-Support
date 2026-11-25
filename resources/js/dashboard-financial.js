const amountFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
const formatAmount = (value) => value == null ? '' : amountFormatter.format(Number(value));

const detailButton = (label, modal, dataKey, endpoint, row) => {
    const button = document.createElement('button');
    const url = new URL(endpoint, window.location.href);
    url.searchParams.set('work_package_id', row.work_package_id);
    button.type = 'button';
    button.className = 'btn btn-link text-body text-decoration-none text-end border-0 p-0 d-block w-100';
    button.textContent = label;
    button.dataset.bsToggle = 'modal';
    button.dataset.bsTarget = modal;
    button.dataset[dataKey] = url.href;
    button.dataset.packageName = row.package;
    return button;
};

export function renderFinancialOverview(root, { data, totals }) {
    const template = document.querySelector('[data-financial-row-template]');
    const fragment = document.createDocumentFragment();

    data.forEach((row) => {
        const element = template.content.firstElementChild.cloneNode(true);
        const packageLink = element.querySelector('[data-package-link]');
        packageLink.textContent = row.package;
        packageLink.dataset.packageName = row.package;

        element.querySelectorAll('[data-amount]').forEach((cell) => {
            cell.textContent = formatAmount(row[cell.dataset.amount]);
        });

        const details = [
            ['budget_plan', row.budget_plan != null, '#budgetPlanDetailModal', 'budgetPlanDataUrl', root.dataset.budgetPlanUrl],
            ['estimated_contract', row.has_owner_estimate_details, '#estimatedContractDetailModal', 'ownerEstimateDataUrl', root.dataset.ownerEstimateUrl],
            ['contract_amount_excl_vat', row.has_contract_details, '#contractDetailModal', 'contractDataUrl', root.dataset.contractUrl],
            ['estimated_variation', row.has_change_order_details, '#estimatedVariationDetailModal', 'changeOrderDataUrl', root.dataset.changeOrderUrl],
            ['approved_variation', true, '#approvedVariationDetailModal', 'variationOrderDataUrl', root.dataset.approvedVariationUrl],
        ];
        details.forEach(([field, available, modal, dataKey, endpoint]) => {
            if (!available) return;
            element.querySelector(`[data-amount="${field}"]`).replaceChildren(
                detailButton(formatAmount(row[field]), modal, dataKey, endpoint, row)
            );
        });
        fragment.append(element);
    });

    if (data.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.className = 'p-3';
        cell.colSpan = 7;
        cell.textContent = 'No data found.';
        row.append(cell);
        fragment.append(row);
    }

    root.querySelector('[data-financial-rows]').replaceChildren(fragment);
    root.querySelectorAll('[data-total]').forEach((cell) => {
        cell.textContent = formatAmount(totals[cell.dataset.total]);
    });
}
