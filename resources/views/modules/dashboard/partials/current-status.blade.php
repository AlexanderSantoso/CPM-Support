<section class="card mt-2 border rounded-3 overflow-hidden bg-body text-body shadow-none">
    <div class="card-body d-flex flex-column gap-4 p-3 p-md-4">
        <p class="text-danger" data-overview-error role="alert" hidden></p>
        <x-dashboard.status-category label="COST AND CONTRACT" :cards="$initialDashboardData['data']"/>
        <x-dashboard.status-category label="ADMINISTRATION" :cards="$initialDashboardData['administration']"/>
        <x-dashboard.status-category label="COMPLIANCE" :cards="$initialDashboardData['compliance']"/>
        <x-dashboard.status-category label="DRAWING" :cards="$initialDashboardData['drawing']"/>
    </div>
</section>


