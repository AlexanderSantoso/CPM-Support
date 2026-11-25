@extends('layouts.main')
@section('head')
    @if ($activeDashboardSection === 'dashboard')
        @vite('resources/js/dashboard-detail-tables.js')
        <link rel="stylesheet" href="{{ asset('dist/external_plugins/dhtmlxgantt.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/dashboard-gantt.css') }}">
    @else
        <link rel="preload"
              href="{{ asset('dist/external_plugins/apexcharts-6.7.0.min.js') }}"
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
        <script src="{{ asset('dist/external_plugins/dhtmlxgantt.js') }}"></script>
        <script src="{{ asset('dist/js/dashboard-gantt.js') }}"></script>
    @endif
@endsection
