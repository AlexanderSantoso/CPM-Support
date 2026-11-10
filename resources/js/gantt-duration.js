function calendarDate(value) {
    if (!value) return null;

    const date = value instanceof Date
        ? value
        : new Date(String(value).replace(' ', 'T'));

    if (Number.isNaN(date.getTime())) return null;

    return Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());
}

export function taskDuration(task) {
    if (task.type === 'milestone') return 0;

    if (task.source_duration !== null && task.source_duration !== undefined) {
        const importedDuration = Number(task.source_duration);

        if (Number.isFinite(importedDuration)) return importedDuration;
    }

    const start = calendarDate(task.start_date);
    const end = calendarDate(task.end_date);

    if (start === null || end === null || end < start) return 0;

    return Math.round((end - start) / 86400000);
}

export function durationLabel(task) {
    const duration = taskDuration(task);
    
    if (duration === 1) {
        return `${duration} day`;
    } else {
        return `${duration} days`;
    }
}
