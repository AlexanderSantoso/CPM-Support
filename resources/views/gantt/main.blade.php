@extends('layouts.main')
@section('head')
@vite('resources/js/ProjectGantt.js')
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Project Schedule</h5>
            <form id="gantt-import-form" class="mb-3">
                <label for="import-mpp" class="form-label">Import Microsoft Project</label>
                <div class="d-flex flex-wrap gap-2 align-items-start">
                    <input type="file" id="import-mpp" class="form-control w-auto mw-100"
                        accept=".mpp" required>
                    <button type="submit" id="gantt-import-button" class="btn btn-primary">
                        Import
                    </button>
                </div>
                <p id="gantt-import-message" class="small mt-2 mb-0" role="status" aria-live="polite"></p>
            </form>
            <div id="gantt-import-actions" class="d-none mb-3">
                <div class="d-flex gap-2">
                    <button type="button" id="gantt-import-save" class="btn btn-primary" >
                        Save
                    </button>
                    <button type="button" id="gantt-import-cancel" class="btn btn-outline-secondary">
                        Cancel
                    </button>
                </div>
            </div>
            <div id="gantt-zoom-controls" class="d-flex justify-content-end align-items-center gap-2 mb-3">
                <button type="button" id="gantt-settings-button" data-bs-toggle="modal" data-bs-target="#holidaySettingsModal" aria-label="Public Holiday" title="Public Holiday"> ⚙</button>
                <span class="me-2">Zoom:</span>
                <button type="button" class="btn btn-sm btn-link text-decoration-inone text-body" data-gantt-zoom="day">
                    Days
                </button>
                <button type="button" class="btn btn-sm btn-link text-decoration-none text-body" data-gantt-zoom="week">
                    Weeks
                </button>
                <button type="button" class="btn btn-sm btn-link text-decoration-none text-body" data-gantt-zoom="month">
                    Months
                </button>
            </div>
            <div id="project-gantt"
                data-source-url="{{ route('gantt.data') }}"
                data-store-url="{{ route('gantt.task.store', [], false) }}"
                data-update-url="{{ route('gantt.task.update', ['id' => 0], false) }}"
                data-delete-url="{{ route('gantt.task.destroy', ['id' => 0], false) }}"
                data-import-url="{{ route('gantt.import.store', [], false) }}"
                data-import-preview-url="{{ route('gantt.import.preview', [], false) }}"
                data-duration-url="{{ route('gantt.duration', [], false) }}"
                data-csrf-token="{{ csrf_token() }}"
                style="height: 600px;"></div>
        </div>
    </div>
    <div class="modal fade" id="holidaySettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Public Holidays</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
    @include('gantt.partials.TaskModal')
@endsection
