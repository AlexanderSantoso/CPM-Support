<script>
    @include('modules.dashboard.partials.script-detail-modals')

var contractorSelect = document.getElementById('contractorFilter');
var workPackageSelect = document.getElementById('workPackageFilter');
var defaultContractorCode = @json($dashboardDefaults['contractor']['id'] ?? null);
var defaultWorkPackageText = @json($dashboardDefaults['work_package']['text'] ?? null);
var workPackagesUrl = @json(route('dashboard.s-curve.work-packages'));
var progressUrl = @json(route('dashboard.s-curve.progress'));
var chart = new ApexCharts(
    document.querySelector('#monthly-signups-chart'),
    {
        series: [
            { name: 'Actual', data: [] },
            { name: 'Plan', data: [] },
        ],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        colors: ["#FA003F", "#5F90E6"],
        markers: { size: 5 },
        xaxis: {
            categories: [],
            labels: { show: false },
        },
        yaxis: {
            min: 0,
            max: 100,
            tickAmount: 10,
        },
    }
);

chart.render();

contractorSelect.addEventListener('change', function () {
    workPackageSelect.innerHTML = '<option value="">Select Work Package</option>';
    workPackageSelect.disabled = true;

    if (!contractorSelect.value) {
        return;
    }

    var params = new URLSearchParams({
        contractor_code: contractorSelect.value,
    });

    fetch(workPackagesUrl + '?' + params.toString())
        .then(function (response) {
            return response.json();
        })
        .then(function (workPackages) {
            workPackages.forEach(function (workPackage) {
                var option = document.createElement('option');
                option.value = workPackage.id;
                option.textContent = workPackage.text;
                workPackageSelect.appendChild(option);
            });

            if (
                contractorSelect.value === defaultContractorCode &&
                defaultWorkPackageText
            ) {
                var defaultOption = Array.from(workPackageSelect.options)
                    .find(function (option) {
                        return option.textContent === defaultWorkPackageText;
                    });

                if (defaultOption) {
                    workPackageSelect.value = defaultOption.value;
                }
            }

            workPackageSelect.disabled = false;

            if (workPackageSelect.value) {
                workPackageSelect.dispatchEvent(new Event('change'));
            }
        });
});

workPackageSelect.addEventListener('change', function () {
    if (!contractorSelect.value || !workPackageSelect.value) {
        return;
    }

    var params = new URLSearchParams({
        contractor_code: contractorSelect.value,
        work_package_id: workPackageSelect.value,
    });

    fetch(progressUrl + '?' + params.toString())
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            chart.updateOptions({
                xaxis: {
                    categories: ['Start', ...data.periods],
                    labels: { show: false },
                },
                series: [
                    { name: 'Actual', data: [0, ...data.actual] },
                    { name: 'Plan', data: [0, ...data.planned] },
                ],
            });
            document.getElementById('sCurveStartDate').textContent = data.duration.start;
            document.getElementById('sCurveEndDate').textContent = data.duration.end;
            document.getElementById('sCurveDurationDays').textContent = data.duration.days;
            var durationDays = Number(data.duration.days);

            if (durationDays === 1) {
                document.getElementById('sCurveDurationUnit').textContent = ' Day';
            } else {
                document.getElementById('sCurveDurationUnit').textContent = ' Days';
            }
        });
    });

    if (contractorSelect.value) {
        contractorSelect.dispatchEvent(new Event('change'));
    }

    var currentStatusDetails = document.querySelectorAll('#current-status-pane details');
    var toggleAllCurrentStatus = document.getElementById('toggleAllCurrentStatus');
    var toggleAllCurrentStatusText = document.getElementById('toggleAllCurrentStatusText');
    var toggleAllCurrentStatusIcon = document.getElementById('toggleAllCurrentStatusIcon');

    function isCurrentStatusOpen(details) {
        if (details.dataset.targetOpen !== undefined) {
            return details.dataset.targetOpen === 'true';
        }

        return details.open;
    }

    function updateCurrentStatusToggle() {
        var allOpen = Array.from(currentStatusDetails).every(function (details) {
            return isCurrentStatusOpen(details);
        });

        toggleAllCurrentStatusText.textContent = allOpen ? 'Collapse' : 'Expand';
        toggleAllCurrentStatusIcon.className = allOpen
            ? 'ti ti-arrows-diagonal-2'
            : 'ti ti-arrows-diagonal';
    }

    function animateCurrentStatusDetails(details, shouldOpen) {
        var content = details.querySelector('.current-status-content');

        if (!content) {
            details.open = shouldOpen;
            return;
        }

        content.getAnimations().forEach(function (animation) {
            animation.cancel();
        });

        var startHeight = content.getBoundingClientRect().height;
        details.dataset.targetOpen = String(shouldOpen);
        content.style.overflow = 'hidden';

        if (shouldOpen) {
            details.open = true;
            content.style.height = '0px';
            content.style.opacity = '0';

            requestAnimationFrame(function () {
                var endHeight = content.scrollHeight;

                content.animate(
                    [
                        { height: '0px', opacity: 0 },
                        { height: endHeight + 'px', opacity: 1},
                    ],
                    { duration: 500, easing: 'ease' }
                ).onfinish = function () {
                    content.style.removeProperty('height');
                    content.style.removeProperty('opacity');
                    content.style.removeProperty('overflow');
                    delete details.dataset.targetOpen;
                    updateCurrentStatusToggle();
                };
            });
           
        } else {
            content.style.height = startHeight + 'px';
            content.style.opacity = '1';

            content.animate(
                [
                    { height: startHeight + 'px', opacity: 1 },
                    { height: '0px', opacity: 0 },
                ],
                { duration: 500, easing: 'ease' }
            ).onfinish = function () {
                details.open = false;
                content.style.removeProperty('height');
                content.style.removeProperty('opacity');
                content.style.removeProperty('overflow');
                delete details.dataset.targetOpen;
                updateCurrentStatusToggle();
            };
        }

        updateCurrentStatusToggle();
    }

    currentStatusDetails.forEach(function (details) {
        var summary = details.querySelector(':scope > summary');

        summary.addEventListener('click', function (event) {
            event.preventDefault();
            animateCurrentStatusDetails(details, !isCurrentStatusOpen(details));
        });
    });

    toggleAllCurrentStatus.addEventListener('click', function () {
        var shouldOpen = Array.from(currentStatusDetails).some(function (item) {
            return !isCurrentStatusOpen(item);
        });

        currentStatusDetails.forEach(function (item) {
            animateCurrentStatusDetails(item, shouldOpen);
        });
    });

    currentStatusDetails.forEach(function (item) {
        item.addEventListener('toggle', updateCurrentStatusToggle);
    });
</script>
