// Dibuja la grilla y conecta los clics del calendario.
export function initCalendar(config) {
    const { data, dayMap, dayNames, onCellClick, onEmptyClick } = config;

    const calendarData = {};
    data.forEach(item => {
        const dayIdx = dayMap[item.day_of_week];
        const hour = parseInt(item.start_time);
        if (dayIdx === undefined) return;
        if (!calendarData[dayIdx]) calendarData[dayIdx] = {};
        calendarData[dayIdx][hour] = item;
    });

    const calendarBody = document.getElementById('calendarBody');
    if (!calendarBody) return;

    for (let hour = 7; hour <= 22; hour++) {
        const time = String(hour).padStart(2, '0') + ':00';
        const row = document.createElement('tr');

        const timeCell = document.createElement('td');
        timeCell.className = 'fw-bold';
        timeCell.textContent = time;
        row.appendChild(timeCell);

        for (let d = 0; d < 6; d++) {
            const cell = document.createElement('td');
            const item = calendarData[d]?.[hour];

            if (item) {
                cell.className = 'bg-success text-white';
                cell.style.cursor = 'pointer';
                cell.textContent = item.level;
                cell.addEventListener('click', e => {
                    e.stopPropagation();
                    onCellClick(d, hour, item);
                });
            } else if (onEmptyClick) {
                cell.style.cursor = 'pointer';
                cell.addEventListener('click', e => {
                    e.stopPropagation();
                    onEmptyClick(d, hour);
                });
            }
            row.appendChild(cell);
        }
        calendarBody.appendChild(row);
    }
}
