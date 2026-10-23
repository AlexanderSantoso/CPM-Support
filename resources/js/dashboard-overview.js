import { renderFinancialOverview } from './dashboard-financial';

const loadOverview = async (root, render) => {
    if (!root) return;

    try {
        const initialData = document.querySelector('[data-dashboard-initial]');
        if (initialData) {
            await render(root, JSON.parse(initialData.textContent));
            return;
        }

        const response = await fetch(root.dataset.sourceUrl, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!response.ok) throw new Error(`Request failed: ${response.status}`);

        await render(root, await response.json());
    } catch (error) {
        const message = root.querySelector('[data-overview-error]');
        message.textContent = 'Data belum bisa ditampilkan. Muat ulang halaman untuk mencoba lagi.';
        message.hidden = false;
        console.error('Dashboard data could not be loaded.', error);
    }
};

const initializeOverview = () => {
    loadOverview(document.querySelector('[data-financial-overview]'), renderFinancialOverview);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeOverview, { once: true });
} else {
    initializeOverview();
}
