import { gantt } from 'dhtmlx-gantt';
import 'dhtmlx-gantt/codebase/dhtmlxgantt.css';
import { durationLabel } from './gantt-duration';
import './datepicker-full.min.js';
import '../css/datepicker-bs5.min.css';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('project-gantt');
    if (!container) return;

    gantt.config.date_format = '%Y-%m-%d %H:%i';
    gantt.config.grid_width = 820;
    gantt.config.wai_aria_attributes = false;

    gantt.attachEvent('onParse', () => {
        gantt.eachTask((task) => {
            gantt.resetProjectDates(task);
        });
    });

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#39;',
    })[char]);

    gantt.templates.task_text = (start, end, task) => escapeHtml(task.text);

    gantt.config.columns = [
        {
            name: 'text',
            label: 'Task Name',
            tree: true,
            width: '*',
            min_width: 250,
            template: (task) => {
                const text = escapeHtml(task.text);

                if (gantt.calculateTaskLevel(task) === 0 && gantt.hasChild(task.id)) {
                    return `<strong>${text}</strong>`;
                } else {
                    return text;
                }
            }
        },
        {
            name: 'duration',
            label: 'Duration',
            align: 'center',
            width: 110,
            template: (task) => durationLabel(task),
        },
        {
            name: 'start_date',
            label: 'Start',
            align: 'center',
            width: 150,
            template: (task) => task.start_date
                ? gantt.templates.date_grid(task.start_date, task, 'start_date')
                : '-',
        },
        {
            name: 'end_date',
            label: 'Finish',
            align: 'center',
            width: 150,
            template: (task) => task.end_date
                ? gantt.templates.date_grid(task.end_date, task, 'end_date')
                : '-',
        },
       
        {
            name: 'add',
            label: '',
            width: 44
        }
    ];

    gantt.ext.zoom.init({
        levels: [
            {
                name: 'day',
                scale_height: 50,
                min_column_width: 40,
                scales: [
                    { unit: 'month', step: 1, format: '%F %Y' },
                    { unit: 'day', step: 1, format: '%d' },
                ],
            },
            {
                name: 'week',
                scale_height: 50,
                min_column_width: 70,
                scales: [
                    { unit: 'month', step: 1, format: '%F %Y' },
                    { unit: 'week', step: 1, format: 'Week #%W' },
                ],
            },
            {
                name: 'month',
                scale_height: 50,
                min_column_width: 120,
                scales: [
                    { unit: 'year', step: 1, format: '%Y' },
                    { unit: 'month', step: 1, format: '%M %Y' },
                ],
            },
        ],
    });

    gantt.plugins({
        drag_timeline: true
    });

    gantt.config.drag_timeline = {
        useKey: false,
        ignore: '.gantt_task_line, .gantt_task_link'
    };

    gantt.config.drag_move = true;
    gantt.config.drag_resize = true;

    ['sections', 'project_sections', 'milestone_sections'].forEach((name) => {
        if (!gantt.config.lightbox[name].some((section) => section.name === 'type')) {
            gantt.config.lightbox[name].splice(1, 0, {
                name: 'type',
                type: 'typeselect',
                map_to: 'type',
            });
        }

        gantt.config.lightbox[name].forEach((section) => {
            if (section.name === 'time') {
                section.year_range = [2026, new Date().getFullYear() + 7];
            }
        });
    });

    gantt.attachEvent('onTaskCreated', (task) => {
        task.start_date = gantt.date.date_part(new Date());
        task.end_date = gantt.calculateEndDate(task);
            return true;
        });

    const modalElement = document.getElementById('taskModal');
    const taskModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const deleteConfirmElement = document.getElementById('deleteConfirmModal');
    const deleteConfirmModal = bootstrap.Modal.getOrCreateInstance(deleteConfirmElement);
    const modalState = { taskId: null, deleteId: null, confirmingDelete: false, saving: false };
    const taskNameInput = document.getElementById('taskName');
    const taskTypeInput = document.getElementById('taskType');
    const taskStartInput = document.getElementById('taskStart');
    const taskEndInput = document.getElementById('taskEnd');
    const startPicker = new window.Datepicker(taskStartInput, {
        format: 'dd-M-yyyy',
        autohide: true,
        buttonClass: 'btn',
    });

    const endPicker = new window.Datepicker(taskEndInput, {
        format: 'dd-M-yyyy',
        autohide: true,
        buttonClass: 'btn',
    });
    const taskDurationLabel = document.getElementById('taskDurationLabel');
    const deleteTaskButton = document.getElementById('deleteTask');
    const parseInputDate = gantt.date.str_to_date('%Y-%m-%d');
    const durationRequestId = { id: 0};

    async function getDurationFromModel(startDate, endDate) {
        const response = await fetch(container.dataset.durationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': container.dataset.csrfToken,
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate,
            }),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Duration gagal dihitung.');
        }

        return Number(result.duration);
    }
    
    async function updateModalDuration() {
        const requestId = ++durationRequestId.id;
        const startDate = startPicker.getDate('yyyy-mm-dd') || '';
        const endDate =  endPicker.getDate('yyyy-mm-dd') || '';

        if (!startDate || !endDate || endDate < startDate) {
            taskDurationLabel.textContent = '-';
            return;
        }

        try {
            const duration = await getDurationFromModel(startDate, endDate);

            if (requestId !== durationRequestId.id) return;

            taskDurationLabel.textContent =
                `${duration} ${duration === 1 ? 'day' : 'days'}`;
        } catch (error) {
            if (requestId === durationRequestId.id) {
                taskDurationLabel.textContent = '-';
            }
        }
    }

    taskStartInput.addEventListener('changeDate', updateModalDuration);
    taskEndInput.addEventListener('changeDate', updateModalDuration);

gantt.attachEvent('onBeforeLightbox', (id) => {
    const task = gantt.getTask(id);

    modalState.taskId = id;
    taskNameInput.value = task.text || '';
    taskTypeInput.value = task.type || 'task';

    if (task.start_date) {
    startPicker.setDate(task.start_date);
    } else {
        startPicker.setDate([], { clear: true });
    }

    if (task.end_date) {
        endPicker.setDate(task.end_date);
    } else {
        endPicker.setDate([], { clear: true });
    }

    updateModalDuration();
    taskModal.show();

    return false;
});

const saveTaskButton = document.getElementById('saveTask');

saveTaskButton.addEventListener('click', async () => {
    if (modalState.taskId === null || modalState.saving) return;

    if (
        !taskNameInput.reportValidity() || !taskNameInput.value.trim() || !taskStartInput.reportValidity() || !taskEndInput.reportValidity()
    ) return;

    const taskId = modalState.taskId;
    const startDate = startPicker.getDate('yyyy-mm-dd');
    const endDate = endPicker.getDate('yyyy-mm-dd');

    if (!startDate || !endDate || endDate < startDate) return;

    const taskData = {
        text: taskNameInput.value.trim(),
        type: taskTypeInput.value,
        startDate,
        endDate,
    };

    modalState.saving = true;
    saveTaskButton.disabled = true;
    taskNameInput.disabled = true;
    taskTypeInput.disabled = true;
    taskStartInput.disabled = true;
    taskEndInput.disabled = true;
    deleteTaskButton.disabled = true;

    try {
        const duration = await getDurationFromModel( taskData.startDate, taskData.endDate,
        );

        if (modalState.taskId !== taskId || !gantt.isTaskExists(taskId)) return;

        const task = gantt.getTask(taskId);
        task.text = taskData.text;
        task.type = taskData.type;
        task.start_date = parseInputDate(taskData.startDate);
        task.end_date = parseInputDate(taskData.endDate);
        task.duration = duration;

        if (task.$new) {
            delete task.$new;
            gantt.addTask(task, task.parent);
        } else {
            gantt.updateTask(task.id);
        }

        modalState.taskId = null;
        taskModal.hide();
    } catch (error) {
        taskDurationLabel.textContent = '-';
        showImportMessage(error.message, true);
    } finally {
        modalState.saving = false;
        saveTaskButton.disabled = false;
        taskNameInput.disabled = false;
        taskTypeInput.disabled = false;
        taskStartInput.disabled = false;
        taskEndInput.disabled = false;
        deleteTaskButton.disabled = false;
    }
});

deleteTaskButton.addEventListener('click', () => {
    if (modalState.saving) return;

    const id = modalState.taskId;
    if (id === null || !gantt.isTaskExists(id)) return;
    modalState.deleteId = id;
    modalState.confirmingDelete = true;
    taskModal.hide();
});

document.getElementById('confirmDeleteTask').addEventListener('click', () => {
    const id = modalState.deleteId;
    modalState.deleteId = null;
    modalState.taskId = null;
    deleteConfirmModal.hide();

    if (id !== null && gantt.isTaskExists(id)) {
        gantt.deleteTask(id);
    }
});

deleteConfirmElement.addEventListener('hidden.bs.modal', () => {
    const id = modalState.deleteId;
    modalState.deleteId = null;

    if (id !== null && gantt.isTaskExists(id)) {
        modalState.taskId = id;
        taskModal.show();
    }
});

    modalElement.addEventListener('hidden.bs.modal', () => {
        if (modalState.confirmingDelete) {
            modalState.confirmingDelete = false;
            deleteConfirmModal.show();
            return;
        }

        const id = modalState.taskId;
        modalState.taskId = null;

        if (id === null || !gantt.isTaskExists(id)) return;

        const task = gantt.getTask(id);

        if (task.$new) {
            gantt.deleteTask(id);
        }
    });

    gantt.init(container);

    const parseModelDate = gantt.date.str_to_date('%d-%M-%Y');
    const parseImportDate = gantt.date.str_to_date('%Y-%m-%d %H:%i');

    gantt.templates.parse_date = (value) => {
        if (/^\d{4}-/.test(value)) {
            return parseImportDate(value);
        }

        return parseModelDate(value);
    };

    gantt.templates.date_grid = gantt.date.date_to_str('%d-%M-%Y');

    const zoomButtons = document.querySelectorAll('[data-gantt-zoom]');

    function setZoom(level) {
        gantt.ext.zoom.setLevel(level);

        zoomButtons.forEach((button) => {
            const active = button.dataset.ganttZoom === level;
            button.classList.toggle('btn-primary', active);
            button.classList.toggle('btn-outline-secondary', !active);
            button.setAttribute('aria-pressed', String(active));
        });
    }

    zoomButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setZoom(button.dataset.ganttZoom);
        });
    });

    setZoom('month');

    const databaseReady = gantt.load(container.dataset.sourceUrl);
    const importForm = document.getElementById('gantt-import-form');
    const importInput = document.getElementById('import-mpp');
    const importButton = document.getElementById('gantt-import-button');
    const importMessage = document.getElementById('gantt-import-message');
    const importActions = document.getElementById('gantt-import-actions');
    const importSaveButton = document.getElementById('gantt-import-save');
    const importState = {
        isPreview: false,
    };
    const formatDate = gantt.date.date_to_str('%Y-%m-%d %H:%i');
    gantt.templates.format_date = formatDate;
    const pendingSaves = new Map();
    const pendingDeletes = new Set();
    const confirmedDeletes = new Set();
    const result = new Map();
    const originalColumns = [...gantt.config.columns];

    async function saveTasks(task) {
        if (importState.isPreview) return;

        let parentDatabaseId = null;

        if (String(task.parent ?? 0) !== String(gantt.config.root_id)) {
            await pendingSaves.get(String(task.parent));

            const parentTask = gantt.getTask(task.parent);

         if (!parentTask.database_id) {
        throw new Error('Parent task has not been saved yet.');
    }

    parentDatabaseId = parentTask.database_id;
}
            const payload = {
                text: task.text,
                parent: parentDatabaseId,
                start_date: task.start_date ? formatDate(task.start_date) : null,
                end_date: task.end_date ? formatDate(task.end_date) : null,
                type: task.type || gantt.config.types.task,
            };
            const response = await fetch(container.dataset.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': container.dataset.csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (!response.ok || !result.tid) {
                throw new Error(result.message || 'Failed to save task.');
        }

        task.database_id = result.tid;

        return result.tid;
    }
    
    gantt.attachEvent('onLightbox', (id) => {
        const time = gantt.getLightboxSection('time');
        if (!time) return;

        const selects = Array.from(time.node.querySelectorAll('select'));
        const task = gantt.getTask(id);

        if (time.section.readonly && task.start_date instanceof Date) {
            const format = time.section.time_format ?? ['%d', '%m', '%Y'];
            const yearSelect = selects[format.indexOf('%Y')];

            if (yearSelect && yearSelect.value === '') {
                const year = String(task.start_date.getFullYear());
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                yearSelect.append(option);
                time.setValue(undefined, task);
            }
        }

        selects.forEach((select, index) => {
            select.required = true;

            if (select.value === '') {
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = ['Day', 'Month', 'Year'][index] ?? 'Select...';
                select.prepend(placeholder);
                select.value = '';
            }
        });

        const updateEndDate = () => {
            const endDate = time.node.querySelector('.gantt_duration_end_date');
            if (endDate) {
                endDate.style.visibility = selects.some((select) => select.value === '')
                    ? 'hidden'
                    : '';
            }
        };

        time.node.addEventListener('change', updateEndDate);
        updateEndDate();
    });

    gantt.attachEvent('onLightboxSave', (id, task, isNew) => {
        const time = gantt.getLightboxSection('time');
        const invalidDate = time && Array.from(time.node.querySelectorAll('select'))
            .find((select) => !select.checkValidity());

        if (invalidDate) {
            invalidDate.reportValidity();
            invalidDate.focus();
            return false;
        }

        showImportMessage('');
        return true;
    });

    gantt.attachEvent('onAfterTaskAdd', (id, task) => {
        if (importState.isPreview || task.database_id) return;

        const key = String(id);
        if (pendingSaves.has(key)) return;

        const saving = saveTasks(task)
        pendingSaves.set(key, saving);

        saving
            .then(() => showImportMessage('Task saved successfully.'))
            .catch((error) => showImportMessage(error.message, true))
            .finally(() => pendingSaves.delete(key));
    });

    gantt.attachEvent('onAfterTaskUpdate', async (id, task) => {
        if (importState.isPreview || !task.database_id) return;

        try {
            const url = container.dataset.updateUrl.replace(/\/0$/, `/${task.database_id}`);

            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': container.dataset.csrfToken,
                },
                body: JSON.stringify({
                    text: task.text,
                    start_date: task.start_date ? formatDate(task.start_date) : null,
                    end_date: task.end_date ? formatDate(task.end_date) : null,
                    type: task.type || gantt.config.types.task,
                }),
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update task.');
            }

            showImportMessage('Task updated successfully.');
        } catch (error) {
            showImportMessage(error.message, true);
        }
    });  

    gantt.attachEvent('onBeforeTaskDelete', (id, task) => {
        const key = String(id);

        if (confirmedDeletes.has(key) || importState.isPreview) return true;
        if (pendingDeletes.has(key)) return false;
        if (!task.database_id && !pendingSaves.has(key)) return true;

        const branch = [task];
        gantt.eachTask((child) => { branch.push(child) }, task.id);

        if (branch.some((item) => pendingDeletes.has(String(item.id)))) {
            return false;
        }

        branch.forEach((item) => pendingDeletes.add(String(item.id)));
        deleteSavedTask(id, task, branch);
        return false;   
    });

    async function deleteSavedTask(id, task, branch) {
    try {
        await Promise.all(
            branch.map((item) => pendingSaves.get(String(item.id)))
        );

        const url = container.dataset.deleteUrl.replace(
            /\/0$/,
            `/${task.database_id}`
        );

        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': container.dataset.csrfToken,
            },
        });

        const result = await response.json();

        if (!response.ok || result.action !== 'deleted') {
            throw new Error(result.message || 'Task gagal dihapus.');
        }

        if (
            !importState.isPreview &&
            gantt.isTaskExists(id) &&
            gantt.getTask(id) === task
        ) {
            branch.forEach((item) => confirmedDeletes.add(String(item.id)));
            gantt.deleteTask(id);
        }
    } catch (error) {
        showImportMessage(error.message, true);
    } finally {
        branch.forEach((item) => {
            pendingDeletes.delete(String(item.id));
            confirmedDeletes.delete(String(item.id));
        });
    }
}

    function showImportMessage(message, isError = false) {
        if (!importMessage) return;

        importMessage.textContent = message;
        importMessage.classList.toggle('text-danger', isError);
        importMessage.classList.toggle('text-body-secondary', !isError);
    }

    if (!importForm || !importInput || !importButton || !importActions) return;

    importForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const file = importInput.files?.[0];

        if (!file || !/\.mpp$/i.test(file.name) || file.size === 0) {
            showImportMessage('Please select a valid .mpp file.', true);
            return;
        }

        // Hide kolom tambah
        gantt.config.columns = originalColumns.filter((column) => column.name !== 'add');
        gantt.render();

        const abortController = new AbortController();
        const timeout = window.setTimeout(() => abortController.abort(), 90000);
        const actionsWereVisible = !importActions.classList.contains('d-none');

        importActions.classList.add('d-none');
        importInput.disabled = true;
        importButton.disabled = true;
        showImportMessage(`Importing ${file.name}...`);

        try {
            // Finish the initial database load before displaying an imported schedule.
            await databaseReady;
            const formData = new FormData();
            formData.append('file', file);

            const response = await fetch(container.dataset.importPreviewUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': container.dataset.csrfToken,
                },
                body: formData,
                signal: abortController.signal,
            });

            const project = await response.json();
            
            if (!response.ok) {
                throw new Error(project.message || `Import failed. HTTP ${response.status}`);
            }

            const tasks = project?.data?.data ?? project?.data?.tasks;
            const links = project?.data?.links ?? [];

            if (!Array.isArray(tasks) || !tasks.length || !Array.isArray(links)) {
                throw new Error('No tasks were found in the selected file.');
            }

            const importedTasks = tasks.map((task) => {
                const result = { ...task };
                delete result.database_id;
                delete result.source_duration_state;
                result.source_duration = task.duration;
                result.type = task.type || gantt.config.types.task;
                result.duration_unit = project?.config?.duration_unit ?? 'day';

                if (String(task.$custom_data?.Summary) === '1') {
                    result.type = gantt.config.types.project;
                }

                if (String(task.$custom_data?.Milestone) === '1') {
                    result.type = gantt.config.types.milestone;
                }

                return result;
            });

            const previousData = gantt.serialize();
            const previousUnit = gantt.config.duration_unit;
            const importedUnit = project?.config?.duration_unit ?? 'day';

            if (!['minute', 'hour', 'day', 'week', 'month', 'year'].includes(importedUnit)) {
                throw new Error('Unsupported duration unit.');
            }

            try {
                gantt.config.duration_unit = importedUnit;
                gantt.clearAll();
                gantt.parse({ data: importedTasks, links });
                gantt.showTask(importedTasks[0].id);
            } catch (error) {
                gantt.config.duration_unit = previousUnit;
                gantt.clearAll();
                gantt.parse(previousData);
                throw error;
            }

            if (!importState.isPreview) {
                result.set('beforeImport', {
                    data: previousData,
                    unit: previousUnit,
                });
            }

            importState.isPreview = true;
            importInput.value = '';
            showImportMessage('');
            importActions.classList.remove('d-none');
        } catch (error) {
            importActions.classList.toggle('d-none', !actionsWereVisible);
            const message = error.name === 'AbortError'
                ? 'Import timed out. Please try again.'
                : error instanceof TypeError
                    ? 'Unable to connect to the DHTMLX import service.'
                    : error instanceof SyntaxError
                        ? 'Invalid response from the import service.'
                        : error.message;

            showImportMessage(message, true);
            console.error('Gantt import error:', error);
        } finally {
            window.clearTimeout(timeout);
            importInput.disabled = false;
            importButton.disabled = false;
            importButton.textContent = 'Import';

        if (!importState.isPreview) {
            gantt.config.columns = originalColumns;
            gantt.render();
        }
    }
});

    function saveData() {
        // Masuk ke database
        if (!importState.isPreview || importSaveButton.disabled) return;

        const data = gantt.serialize();

        data.data.forEach((task) => {
            const chartTask = gantt.getTask(task.id);
            task.type = chartTask.type || gantt.config.types.task;
            delete task.duration;
            delete task.source_duration;
            task.id = String(task.id);
            task.parent = String(task.parent ?? 0) === String(gantt.config.root_id)
                ? null
                : String(task.parent);
        });

        result.set('tasks', data.data);
        result.set('links', data.links ?? []);

        const buttons = [
            importSaveButton,
            importButton,
            document.getElementById('gantt-import-cancel'),
        ];
        const wasReadonly = gantt.config.readonly;

        buttons.forEach((button) => button.disabled = true);
        gantt.config.readonly = true;

        return fetch(container.dataset.importUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': container.dataset.csrfToken,
            },
            body: JSON.stringify({
                tasks: result.get('tasks'),
                links: result.get('links'),
            }),
        })
            .then((response) => response.json().then((saved) => {
                if (!response.ok || !saved.task_ids) {
                    throw new Error(saved.message || 'Hasil impor gagal disimpan.');
                }

                return saved;
            }))
            .then((saved) => {
                const tasks = [];

                gantt.eachTask((task) => {
                    const newId = saved.task_ids[String(task.id)];

                    if (!newId) {
                        throw new Error('ID task hasil penyimpanan tidak ditemukan.');
                    }

                    tasks.push({ task, newId, temporaryId: `saved-${gantt.uid()}` });
                });

                gantt.batchUpdate(() => {
                    tasks.forEach(({ task, temporaryId }) => {
                        gantt.changeTaskId(task.id, temporaryId);
                    });

                    tasks.forEach(({ task, newId }) => {
                        task.database_id = newId;
                        gantt.changeTaskId(task.id, newId);
                    });
                });

                importState.isPreview = false;

                // Unhide kolom tambah
                gantt.config.columns = originalColumns;
                importActions.classList.add('d-none');
                result.delete('tasks');
                result.delete('beforeImport');
                showImportMessage('');
            })
            .catch((error) => {
                showImportMessage(error.message, true);
            })
            .finally(() => {
                buttons.forEach((button) => button.disabled = false);
                gantt.config.readonly = wasReadonly;
                gantt.render();
            });
    }
    importSaveButton.addEventListener('click', saveData);

    const importCancelButton = document.getElementById('gantt-import-cancel');

    importCancelButton.addEventListener('click', () => {
        if (!importState.isPreview || importSaveButton.disabled || importButton.disabled) return;

        gantt.hideLightbox();
        gantt.clearAll();
        gantt.config.duration_unit = 'day';
        importState.isPreview = false;
        gantt.config.columns = originalColumns;
        gantt.render();

        importActions.classList.add('d-none');
        importInput.value = '';
        result.delete('tasks');
        result.delete('beforeImport');
        showImportMessage('');
    });
});
