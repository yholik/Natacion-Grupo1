// Renderiza clases del calendario como tarjetas agrupadas por día.
export function initCalendar(config) {
    const { data, dayMap, dayNames, onCardClick, onEmptyClick, onAddClick, cardButtonLabel } = config;

    const calendarContainer = document.getElementById('calendarContainer');
    if (!calendarContainer) return;

    const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    const grouped = {};
    dayOrder.forEach(day => { grouped[day] = []; });
    data.forEach(item => {
        if (grouped[item.day_of_week]) {
            grouped[item.day_of_week].push(item);
        }
    });

    calendarContainer.innerHTML = '';

    dayOrder.forEach((day, idx) => {
        const lessons = grouped[day];
        const section = document.createElement('div');
        section.className = 'mb-4';

        const header = document.createElement('div');
        header.className = 'd-flex justify-content-between align-items-center mb-3';

        const title = document.createElement('h5');
        title.className = 'text-muted mb-0';
        title.textContent = dayNames[idx];
        header.appendChild(title);

        if (onAddClick) {
            const addBtn = document.createElement('button');
            addBtn.className = 'btn btn-sm btn-outline-success';
            addBtn.innerHTML = '<i class="bi bi-plus-lg"></i> Agregar';
            addBtn.addEventListener('click', () => onAddClick(day, dayNames[idx]));
            header.appendChild(addBtn);
        }

        section.appendChild(header);

        if (lessons.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-muted fst-italic mb-3';
            empty.textContent = 'Sin clases este día';
            section.appendChild(empty);
        } else {
            const row = document.createElement('div');
            row.className = 'row g-3';

            lessons.forEach(item => {
                const col = document.createElement('div');
                col.className = 'col-12 col-sm-6 col-lg-4 col-xl-3';

                const card = document.createElement('div');
                card.className = 'card h-100 shadow-sm';
                card.style.cursor = 'pointer';
                card.style.transition = 'transform 0.15s, box-shadow 0.15s';

                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-2px)';
                    card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    card.style.boxShadow = '';
                });

                const start = item.start_time.substring(0, 5);
                const end = item.end_time.substring(0, 5);
                const enrolled = item.enrolled || 0;
                const capacity = item.capacity || 0;
                const full = enrolled >= capacity;

                let badgeClass = 'bg-success';
                let capacityText = `${enrolled}/${capacity}`;
                if (full) {
                    badgeClass = 'bg-danger';
                    capacityText = 'Lleno';
                } else if (enrolled > 0) {
                    badgeClass = 'bg-warning text-dark';
                }

                card.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title fw-bold mb-0">${escapeHtml(item.specialty_name || 'Sin especialidad')}</h6>
                            <span class="badge ${badgeClass}">${capacityText}</span>
                        </div>
                        <div class="card-text text-muted small">
                            <div class="mb-1">${start} - ${end}</div>
                            ${item.coach_first_name ? `<div class="mb-1">Prof. ${escapeHtml(item.coach_first_name)} ${escapeHtml(item.coach_last_name)}</div>` : ''}
                            <div class="mb-1">${escapeHtml(item.level_name)}</div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                        <button class="btn btn-sm btn-outline-${full ? 'secondary' : 'success'} w-100 btn-card-action" 
                                data-lesson-id="${item.id}"
                                ${full && !item.booking_id ? 'disabled' : ''}>
                            ${cardButtonLabel || (item.booking_id ? 'Cancelar' : (full ? 'Ver detalle' : 'Inscribirme'))}
                        </button>
                    </div>
                `;

                card.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-card-action')) return;
                    onCardClick(idx, item);
                });

                const btn = card.querySelector('.btn-card-action');
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    onCardClick(idx, item);
                });

                col.appendChild(card);
                row.appendChild(col);
            });

            section.appendChild(row);
        }

        calendarContainer.appendChild(section);
    });

    if (data.length === 0 && onEmptyClick) {
        const emptyState = document.createElement('div');
        emptyState.className = 'text-center py-5';
        emptyState.innerHTML = `
            <div class="text-muted mb-3">
                <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            </div>
            <p class="text-muted">No hay clases programadas</p>
        `;
        calendarContainer.appendChild(emptyState);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
