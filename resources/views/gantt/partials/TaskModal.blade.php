<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light px-4">
                <h5 class="modal-title fw-semibold" id="taskModalTitle">Task Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="taskName" class="form-label fw-medium">Task Name</label>
                    <input type="text" id="taskName" class="form-control" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label for="taskType" class="form-label fw-medium">Type</label>
                    <select id="taskType" class="form-select">
                        <option value="task">Task</option>
                        <option value="project">Project</option>
                        <option value="milestone">Milestone</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="taskStart" class="form-label fw-medium">Start</label>
                        <div class="position-relative">
                            <input type="text" id="taskStart" class="form-control" placeholder="dd-M-yyyy" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="taskEnd" class="form-label fw-medium">End Date</label>
                        <div class="position-relative">
                            <input type="text" id="taskEnd" class="form-control" placeholder="dd-M-yyyy" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Duration</label>
                        <div><span id="taskDurationLabel">-</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between px-4 pb-4 border-0">
                <button type="button" class="btn btn-outline-danger" id="deleteTask">Delete</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" id="saveTask">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="deleteConfirmTitle">Delete task?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-secondary">
                This task will be removed from the schedule.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTask">Delete</button>
            </div>
        </div>
    </div>
</div>
