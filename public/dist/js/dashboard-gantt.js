document.addEventListener('DOMContentLoaded', async () => {
    const container = document.querySelector('[data-dashboard-gantt]');
    const message = document.getElementById('dashboard-gantt-message');

    if (!container) return;

    const showMessage = (text, isError = false) => {
        if (!message) return;

        message.textContent = text;
        message.hidden = text === '';
        message.classList.toggle('text-danger', isError);
        message.classList.toggle('text-body-secondary', !isError);
        message.setAttribute('role', isError ? 'alert' : 'status');
    };

    const gantt = window.dhtmlxgantt?.gantt;

    if (!gantt) {
        showMessage('Gantt chart library could not be loaded.', true);
        return;
    }

    const parseModelDate = gantt.date.str_to_date('%d-%M-%Y');
    const parseDatabaseDate = gantt.date.str_to_date('%Y-%m-%d %H:%i');
    const parseDate = (value) => {
        if (!value) return null;

        const date = /^\d{4}-/.test(String(value))
            ? parseDatabaseDate(value)
            : parseModelDate(value);

        return date instanceof Date && !Number.isNaN(date.getTime()) ? date : null;
    };

    const months = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[character]);

    gantt.plugins({ marker: true });
    gantt.config.readonly = true;
    gantt.config.drag_move = false;
    gantt.config.drag_resize = false;
    gantt.config.drag_progress = false;
    gantt.config.drag_links = false;
    gantt.config.show_links = false;
    gantt.config.show_unscheduled = true;
    gantt.config.grid_width = 650;
    gantt.config.row_height = 34;
    gantt.config.bar_height = 22;
    gantt.config.scale_height = 52;
    gantt.config.min_column_width = 80;
    gantt.config.scales = [
        { unit: 'year', step: 1, format: '%Y' },
        { unit: 'month', step: 1, format: (date) => months[date.getMonth()] },
    ];
    gantt.config.columns = [
        {
            name: 'wbs',
            label: 'WBS',
            width: 55,
            template: (task) => typeof gantt.getWBSCode === 'function'
                ? gantt.getWBSCode(task)
                : '',
        },
        {
            name: 'task_name',
            label: 'Task Name',
            tree: true,
            width: '*',
            min_width: 200,
            template: (task) => escapeHtml(task.task_name ?? task.text ?? ''),
        },
        {
            name: 'status',
            label: 'Status',
            width: 100,
            template: (task) => escapeHtml(task.status ?? ''),
        },
    ];
    gantt.templates.task_text = () => '';
    gantt.templates.parse_date = parseDate;
    gantt.templates.task_class = (start, end, task) =>
        gantt.hasChild(task.id) || task.type === 'project' ? 'dashboard-gantt-parent' : '';

    gantt.init(container);

    const today = gantt.date.day_start(new Date());
    gantt.addMarker({
        start_date: today,
        css: 'dashboard-gantt-today',
        text: 'Today',
        title: gantt.date.date_to_str('%d %M %Y')(today),
    });

    document.getElementById('dashboard-tab')?.addEventListener('shown.bs.tab', () => {
        gantt.setSizes();
    });

    try {
        const response = await fetch(container.dataset.sourceUrl, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error(`Project schedule request failed (HTTP ${response.status}).`);
        }

        const payload = await response.json();
        if (!Array.isArray(payload.data)) throw new Error('Invalid project schedule response.');

        const allTasks = payload.data.map((task) => ({
            ...task,
            text: task.task_name ?? task.text ?? '',
            open: true,
        }));
        const tasksById = new Map(allTasks.map((task) => [String(task.id), task]));
        const getTaskDepth = (task) => {
            let depth = 1;
            let parentId = String(task.parent ?? '');
            const visitedIds = new Set([String(task.id)]);

            while (parentId && parentId !== '0' && tasksById.has(parentId) && !visitedIds.has(parentId)) {
                depth += 1;
                visitedIds.add(parentId);
                parentId = String(tasksById.get(parentId).parent ?? '');
            }

            return depth;
        };
        const tasks = allTasks.filter((task) => getTaskDepth(task) <= 2);
        const dates = tasks.flatMap((task) => [task.start_date, task.end_date])
            .map(parseDate)
            .filter(Boolean);

        const earliest = dates.length
            ? new Date(Math.min(today.getTime(), ...dates.map((date) => date.getTime())))
            : today;
        const latest = dates.length
            ? new Date(Math.max(today.getTime(), ...dates.map((date) => date.getTime())))
            : today;

        gantt.config.start_date = gantt.date.add(earliest, -1, 'month');
        gantt.config.end_date = gantt.date.add(latest, 2, 'month');
        const visibleTaskIds = new Set(tasks.map((task) => String(task.id)));
        const links = (payload.links ?? []).filter((link) =>
            visibleTaskIds.has(String(link.source)) &&
            visibleTaskIds.has(String(link.target))
        );
        gantt.parse({ data: tasks, links });
        gantt.showDate(today);

        showMessage(tasks.length ? '' : 'No project schedule yet.');
    } catch (error) {
        console.error('Dashboard Gantt error:', error);
        showMessage('Unable to load project schedule. Please try again later.', true);
    }
});
