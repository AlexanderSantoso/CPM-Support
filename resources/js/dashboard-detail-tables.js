import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import './dashboard-overview';
import { applyStatusColor } from './dashboard-status';

document.addEventListener('DOMContentLoaded', () => {
    const text = DataTable.render.text();
    const sortedText = (field) => (value, type, row) => type === 'sort' || type === 'type'
        ? row[field] : text.display(value);
    const detailFreshnessMs = 30_000;
    const detailRequests = new Map();

    const loadDetail = (url) => {
        const cached = detailRequests.get(url);
        if (cached && Date.now() < cached.expiresAt) return cached.promise;

        const entry = { expiresAt: Infinity };
        entry.promise = fetch(url, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        }).then(async (response) => {
            if (!response.ok) throw new Error(`Request failed: ${response.status}`);
            const data = await response.json();
            if (data.error || !Array.isArray(data.data)) throw new Error('Invalid detail response');
            entry.expiresAt = Date.now() + detailFreshnessMs;
            return data;
        }).catch((error) => {
            detailRequests.delete(url);
            throw error;
        });

        detailRequests.set(url, entry);
        return entry.promise;
    };

    const renderStatus = (value, type, row) => {
        if (type !== 'display') return value || '-';
        const badge = document.createElement('span');
        badge.className = 'badge rounded-pill border px-2 py-1 fw-semibold';
        badge.textContent = value || '-';
        applyStatusColor(badge, row.status_bg_color ?? 'light-secondary');
        return badge;
    };

    const tableOptions = (state, columns, options = {}) => ({
        ajax: (_request, callback) => {
            const url = state.url;
            const requestId = ++state.requestId;
            state.loading = true;
            state.loadedAt = 0;

            loadDetail(url)
                .then((data) => {
                    if (url !== state.url || requestId !== state.requestId) return;
                    state.loading = false;
                    state.loadedAt = Date.now();
                    callback(data);
                })
                .catch(() => {
                    if (url !== state.url || requestId !== state.requestId) return;
                    state.loading = false;
                    callback({
                        data: [],
                        error: 'Data detail belum bisa dimuat. Tutup dan buka kembali untuk mencoba lagi.',
                    });
                });
        },
        serverSide: false,
        processing: false,
        searching: false,
        paging: false,
        info: false,
        ordering: true,
        lengthChange: false,
        autoWidth: false,
        scrollX: options.scrollX ?? false,
        order: options.order ?? [],
        dom: 't',
        language: {
            emptyTable: 'No data found.',
            loadingRecords: '',
        },
        columns,
    });

    const connectTable = ({ modal, tableElement, urlKey, columns, options }) => {
        if (!modal || !tableElement) return null;

        let table;
        const state = { url: null, loading: false, loadedAt: 0, requestId: 0 };

        const prefetch = (event) => {
            const button = event.target.closest?.(`[data-bs-target="#${modal.id}"]`);
            const url = button?.dataset[urlKey];
            if (url) loadDetail(url).catch(() => {});
        };
        document.addEventListener('pointerover', prefetch);
        document.addEventListener('focusin', prefetch);

        modal.addEventListener('show.bs.modal', (event) => {
            const url = event.relatedTarget?.dataset?.[urlKey] || modal.dataset[urlKey];
            if (!url) return;

            if (table && state.url === url
                && (state.loading || Date.now() - state.loadedAt < detailFreshnessMs)) return;

            if (state.url !== url) {
                state.requestId++;
                state.loadedAt = 0;
                state.loading = false;
                state.url = url;

                if (table) {
                    table.clear();
                    tableElement.tBodies[0].replaceChildren();
                }
            }

            if (!table) {
                table = new DataTable(tableElement, tableOptions(state, columns, options));
            } else {
                table.ajax.reload();
            }
        });

        modal.addEventListener('shown.bs.modal', () => table?.columns.adjust());

    };

    const budgetModal = document.getElementById('budgetPlanDetailModal');
    connectTable({
        modal: budgetModal,
        tableElement: budgetModal?.querySelector('[data-budget-plan-table]'),
        urlKey: 'budgetPlanDataUrl',
        options: { order: [[0, 'desc']] },
        columns: [
            {
                data: 'issued_date', name: 'issued_date',
                render: sortedText('issued_date_sort'),
            },
            { data: 'work_package', name: 'workPackage.work_package', defaultContent: '-', render: text },
            {
                data: 'budget_estimate', name: 'budget_estimate', className: 'dt-head-left dt-body-right',
                render: sortedText('budget_estimate_raw'),
            },
            {
                data: 'status',
                name: 'logStatus.status',
                defaultContent: '-',
                render: renderStatus,
            },
            { data: 'assigned_to', name: 'latestActivity.assignee.first_name', defaultContent: '-', render: text },
            { data: 'last_updated', name: 'updated_at', orderable: false, searchable: false, defaultContent: '-', render: text },
        ],
    });

    const ownerEstimateModal = document.getElementById('estimatedContractDetailModal');
    connectTable({
        modal: ownerEstimateModal,
        tableElement: ownerEstimateModal?.querySelector('[data-owner-estimate-table]'),
        urlKey: 'ownerEstimateDataUrl',
        options: { order: [[0, 'desc']], scrollX: true },
        columns: [
            { data: 'issued_date', name: 'issued_date', render: sortedText('issued_date_sort'), defaultContent: '-' },
            { data: 'work_package', name: 'workPackage.work_package', render: text, defaultContent: '-' },
            { data: 'remarks', name: 'remarks', className: 'dashboard-estimated-contract-remarks text-wrap text-break', render: text, defaultContent: '-' },
            { data: 'estimated_contract', name: 'estimated_contract', className: 'dt-head-left dt-body-right', render: sortedText('estimated_contract_raw') },
            { data: 'status', name: 'logStatus.status', render: renderStatus, defaultContent: '-' },
            { data: 'assigned_to', name: 'latestActivity.assignee.first_name', render: text, defaultContent: '-' },
            {
                data: 'last_updated', name: 'updated_at', orderable: false, searchable: false,
                render: (value, type, row) => {
                    if (type !== 'display') return `${row.last_updated_by || '-'} ${value || '-'}`;
                    const content = document.createElement('div');
                    const name = document.createElement('div');
                    name.textContent = row.last_updated_by || '-';
                    const date = document.createElement('div');
                    date.className = 'mt-1 text-body-secondary small text-nowrap';
                    date.textContent = value || '-';
                    content.append(name, date);
                    return content;
                },
            },
        ],
    });

    const contractModal = document.getElementById('contractDetailModal');
    connectTable({
        modal: contractModal,
        tableElement: contractModal?.querySelector('[data-contract-table]'),
        urlKey: 'contractDataUrl',
        options: { order: [[1, 'desc']], scrollX: true },
        columns: [
            { data: 'contract_no', name: 'contract_no', defaultContent: '-', render: text },
            { data: 'issued_date', name: 'issued_date', defaultContent: '-', render: sortedText('issued_date_sort') },
            { data: 'vendor', name: 'vendor.company_name', defaultContent: '-', render: text },
            { data: 'address', name: 'address', defaultContent: '-', render: text },
            { data: 'work_package', name: 'workPackage.work_package', defaultContent: '-', render: text },
            { data: 'start_date', name: 'start_date', defaultContent: '-', render: sortedText('start_date_sort') },
            { data: 'end_date', name: 'end_date', defaultContent: '-', render: sortedText('end_date_sort') },
            { data: 'contract_amount_excl_vat', name: 'contract_amount_excl_vat', className: 'dt-head-left dt-body-right', defaultContent: '-', render: sortedText('contract_amount_excl_vat_raw') },
            { data: 'contract_amount_incl_vat', name: 'contract_amount_excl_vat', className: 'dt-head-left dt-body-right', orderable: false, searchable: false, defaultContent: '-', render: text },
            { data: 'down_payment', name: 'down_payment', className: 'dt-head-left dt-body-right', defaultContent: '-', render: sortedText('down_payment_raw') },
            { data: 'retention', name: 'retention', className: 'dt-head-left dt-body-right', defaultContent: '-', render: sortedText('retention_raw') },
            { data: 'term_of_payment', name: 'term_of_payment', className: 'dt-head-left dt-body-right', orderable: false, searchable: false, defaultContent: '-', render: text },
        ],
    });

    const changeOrderModal = document.getElementById('estimatedVariationDetailModal');
    connectTable({
        modal: changeOrderModal,
        tableElement: changeOrderModal?.querySelector('[data-change-order-table]'),
        urlKey: 'changeOrderDataUrl',
        options: { order: [[0, 'desc']], scrollX: true },
        columns: [
            { data: 'date_raised', name: 'date_raised', defaultContent: '-', render: sortedText('date_raised_sort') },
            { data: 'input_by', name: 'latestActivity.creator.first_name', defaultContent: '-', render: text },
            { data: 'cr_no', name: 'doc_sequence', defaultContent: '-', render: sortedText('doc_sequence') },
            { data: 'contract_no', name: 'contractDocument.contract_no', defaultContent: '-', render: text },
            { data: 'vendor', name: 'contractDocument.vendor.company_name', defaultContent: '-', render: text },
            { data: 'work_package', name: 'contractDocument.workPackage.work_package', defaultContent: '-', render: text },
            { data: 'building_phase', name: 'phase.name', defaultContent: '-', render: text },
            { data: 'type_of_change', name: 'type.name', defaultContent: '-', render: text },
            { data: 'originator', name: 'origin.name', defaultContent: '-', render: text },
            { data: 'priority', name: 'priority.name', defaultContent: '-', render: text },
            { data: 'description', name: 'description', defaultContent: '-', render: text },
            { data: 'estimated_variation', name: 'estimated_variation', className: 'dt-head-left dt-body-right', orderable: false, defaultContent: '-', render: text },
            { data: 'potential_impact_on_project', name: 'potential_impact_on_project', defaultContent: '-', render: text },
            { data: 'cost_estimation', name: 'cost_estimation', className: 'dt-head-left dt-body-right', orderable: false, defaultContent: '-', render: text },
            { data: 'time_estimation', name: 'time_estimation', className: 'dt-head-left dt-body-right', defaultContent: '-', render: text },
            { data: 'pm_comment', name: 'pm_comment', className: 'dashboard-change-order-comment text-wrap text-break align-top', defaultContent: '-', render: text },
            { data: 'consultant_design_specialist_comment', name: 'consultant_design_specialist_comment', className: 'dashboard-change-order-comment text-wrap text-break align-top', defaultContent: '-', render: text },
            { data: 'qs_comment', name: 'qs_comment', className: 'dashboard-change-order-comment text-wrap text-break align-top', defaultContent: '-', render: text },
            { data: 'client_comment', name: 'client_comment', className: 'dashboard-change-order-comment text-wrap text-break align-top', defaultContent: '-', render: text },
            { data: 'status', name: 'latestActivity.logStatus.status', defaultContent: '-', render: renderStatus },
            { data: 'assigned_to', name: 'latestActivity.assignee.first_name', defaultContent: '-', render: text },
        ],
    });

    const approvedVariationModal = document.getElementById('approvedVariationDetailModal');
    connectTable({
        modal: approvedVariationModal,
        tableElement: approvedVariationModal?.querySelector('[data-variation-order-table]'),
        urlKey: 'variationOrderDataUrl',
        options: { order: [[0, 'desc']], scrollX: true },
        columns: [
            { data: 'variation_order_no', defaultContent: '-', render: sortedText('doc_sequence') },
            { data: 'vendor', defaultContent: '-', render: text },
            { data: 'work_package', name: 'workPackage.work_package', defaultContent: '-', render: text },
            { data: 'si_no', defaultContent: '-', render: text },
            { data: 'change_order_no', defaultContent: '-', render: text },
            {
                data: 'approved_variation', name: 'approved_variation', className: 'dt-head-left dt-body-right',
                defaultContent: '-',
                render: sortedText('approved_variation_raw'),
            },
            { data: 'status', name: 'logStatus.status', defaultContent: '-', render: renderStatus },
            { data: 'assigned_to', defaultContent: '-', render: text },
            { data: 'input_date', defaultContent: '-', render: sortedText('input_date_sort') },
            { data: 'last_updated', defaultContent: '-', render: sortedText('last_updated_sort') },
        ],
    });

});
