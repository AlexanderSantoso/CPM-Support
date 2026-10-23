<div class="my-3">
    <ul class="nav nav-tabs" id="dashboard-tabs">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeDashboardSection === 'dashboard' ? 'active text-body fw-semibold' : 'text-body-secondary' }}"
                href="{{ route('dashboard.dashboard') }}"
                @if ($activeDashboardSection === 'dashboard') aria-current="page" @endif>
                Dashboard
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeDashboardSection === 'current-status' ? 'active text-body fw-semibold' : 'text-body-secondary' }}"
                href="{{ route('dashboard.current-status') }}"
                @if ($activeDashboardSection === 'current-status') aria-current="page" @endif>
                Current Status
            </a>
        </li>
    </ul>

    @if ($activeDashboardSection === 'dashboard')
        @include('modules.dashboard.partials.financial-table')
    @else
        @include('modules.dashboard.partials.current-status')
    @endif
</div>
