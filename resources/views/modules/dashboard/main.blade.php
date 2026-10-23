@extends('layouts.main')

@section('head')
    @if ($activeDashboardSection === 'dashboard')
        @vite('resources/js/dashboard-detail-tables.js')
    @else
        <link rel="preload"
              href="{{ asset('dist/external_plugins/apexcharts.min.js') }}"
              as="script">
    @endif
@endsection

@section('content')
    @include('modules.dashboard.partials.styles')
    @include('modules.dashboard.partials.content')

    @if ($activeDashboardSection === 'dashboard')
        @include('modules.dashboard.partials.modals')

        <script type="application/json" data-dashboard-initial>
            @json($initialDashboardData)
        </script>

        @include('modules.dashboard.partials.scripts-dashboard')
    @endif
@endsection