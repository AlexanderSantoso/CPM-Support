export function applyStatusColor(element, bgColor, { subtle = true } = {}) {
    const theme = String(bgColor ?? '').replace(/^light-/, '');
    const variant = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'].includes(theme)
        ? theme : 'secondary';
    const classes = subtle && bgColor?.startsWith('light-')
        ? [`bg-${variant}-subtle`, `text-${variant}-emphasis`]
        : [`text-bg-${variant}`];
    element.classList.add(...classes);
}
