        const financialReportDetailModal = document.getElementById('financialReportDetailModal');

        financialReportDetailModal.addEventListener('show.bs.modal', (event) => {
            const selectedPackage = event.relatedTarget.getAttribute('data-package-name');
            financialReportDetailModal.querySelector('[data-selected-package]').textContent = selectedPackage;
        });
