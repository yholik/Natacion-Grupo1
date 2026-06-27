<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Calendario de clases</h2>
        </div>
        <div id="calendarContainer"></div>
    </div>

    <!-- Modal CREAR clase -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="?url=coach-create-lesson" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="day_of_week" id="createDay">
                    <p><strong>Día:</strong> <span id="createDayLabel"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Horario inicio</label>
                        <select name="start_time" id="createTime" class="form-select" required>
                            <?php for ($h = 7; $h <= 22; $h++): ?>
                            <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horario fin</label>
                        <select name="end_time" id="endTime" class="form-select" required>
                            <?php for ($h = 8; $h <= 23; $h++): ?>
                            <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <select name="specialty_id" class="form-select" required>
                            <option value="">Seleccionar especialidad...</option>
                            <?php foreach ($specialties as $specialty): ?>
                            <option value="<?= (int)$specialty['id'] ?>"><?= htmlspecialchars($specialty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel</label>
                        <select name="level_id" class="form-select" required>
                            <option value="">Seleccionar nivel...</option>
                            <?php foreach (($levels ?? []) as $level): ?>
                            <option value="<?= (int)$level['id'] ?>"><?= htmlspecialchars($level['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cupo máximo</label>
                        <input type="number" name="capacity" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear clase</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal DETALLE clase -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nivel:</strong> <span id="detailLevel"></span></p>
                    <p><strong>Horario:</strong> <span id="detailSchedule"></span></p>
                    <p><strong>Especialidad:</strong> <span id="detailSpecialty"></span></p>
                    <p><strong>Cupo:</strong> <span id="detailCapacity"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnEditLesson">Editar</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteLesson">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR clase -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="?url=coach-edit-lesson" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lesson_id" id="editLessonId">
                    <input type="hidden" name="day_of_week" id="editDay">
                    <p><strong>Día:</strong> <span id="editDayLabel"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Horario inicio</label>
                        <select name="start_time" id="editStartTime" class="form-select" required>
                            <?php for ($h = 7; $h <= 22; $h++): ?>
                            <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horario fin</label>
                        <select name="end_time" id="editEndTime" class="form-select" required>
                            <?php for ($h = 8; $h <= 23; $h++): ?>
                            <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <select name="specialty_id" id="editSpecialty" class="form-select" required>
                            <option value="">Seleccionar especialidad...</option>
                            <?php foreach ($specialties as $specialty): ?>
                            <option value="<?= (int)$specialty['id'] ?>"><?= htmlspecialchars($specialty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel</label>
                        <select name="level_id" id="editLevel" class="form-select" required>
                            <option value="">Seleccionar nivel...</option>
                            <?php foreach (($levels ?? []) as $level): ?>
                            <option value="<?= (int)$level['id'] ?>"><?= htmlspecialchars($level['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cupo máximo</label>
                        <input type="number" name="capacity" id="editCapacity" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const lessons = <?= json_encode($lessons) ?>;
    const APP_URL = '<?= rtrim(Env::get('APP_URL'), '/') ?>';
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';
    import { handleAlert } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/services/ui.js';
    import { dayMap, dayNames } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/dayConstants.js';

    let currentLesson = null;

    const createForm = document.querySelector('#createModal form');
    createForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(createForm);
        try {
            const res = await fetch(APP_URL + '/?url=coach-create-lesson', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.status === 'success') {
                handleAlert(json.status, json.message, json.redirect);
                bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                handleAlert(json.status, json.message);
            }
        } catch (err) {
            handleAlert('error', 'Error de conexión al servidor.');
        }
    });

    const editForm = document.querySelector('#editModal form');
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(editForm);
        try {
            const res = await fetch(APP_URL + '/?url=coach-edit-lesson', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.status === 'success') {
                handleAlert(json.status, json.message, json.redirect);
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                handleAlert(json.status, json.message);
            }
        } catch (err) {
            handleAlert('error', 'Error de conexión al servidor.');
        }
    });

    function updateEndTimeOptions(selectElement, startTimeValue) {
        const startHour = parseInt(startTimeValue.substring(0, 2));
        Array.from(selectElement.options).forEach(option => {
            const optionHour = parseInt(option.value.substring(0, 2));
            option.disabled = optionHour <= startHour;
        });
        const nextHour = startHour + 1;
        const nextHourStr = String(nextHour).padStart(2, '0') + ':00:00';
        if (selectElement.value <= startTimeValue) {
            selectElement.value = nextHourStr;
        }
    }

    document.getElementById('createTime').addEventListener('change', function() {
        updateEndTimeOptions(document.getElementById('endTime'), this.value);
    });

    document.getElementById('editStartTime').addEventListener('change', function() {
        updateEndTimeOptions(document.getElementById('editEndTime'), this.value);
    });

    document.getElementById('btnEditLesson').addEventListener('click', () => {
        if (!currentLesson) return;
        bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();

        document.getElementById('editLessonId').value = currentLesson.id;
        document.getElementById('editDay').value = currentLesson.day_of_week;
        document.getElementById('editDayLabel').textContent = currentLesson.day_of_week_label;
        document.getElementById('editStartTime').value = currentLesson.start_time;
        document.getElementById('editEndTime').value = currentLesson.end_time;
        document.getElementById('editSpecialty').value = currentLesson.specialty_id || '';
        document.getElementById('editLevel').value = currentLesson.level_id || '';
        document.getElementById('editCapacity').value = currentLesson.capacity || 1;

        updateEndTimeOptions(document.getElementById('editEndTime'), currentLesson.start_time);

        new bootstrap.Modal(document.getElementById('editModal')).show();
    });

    document.getElementById('btnDeleteLesson').addEventListener('click', async () => {
        if (!currentLesson) return;
        const enrolled = currentLesson.enrolled || 0;

        if (enrolled > 0) {
            await mostrarConfirmacion(
                `No se puede eliminar la clase porque tiene ${enrolled} alumno(s) inscripto(s) actualmente. Primero desinscribilos.`,
                { titulo: 'Clase con inscriptos', textoAceptar: 'Entendido', claseAceptar: 'btn-primary', mostrarCancelar: false }
            );
            return;
        }

        const totalBookings = currentLesson.total_bookings || 0;
        const hasHistory = totalBookings > enrolled;
        const message = hasHistory
            ? `La clase tiene reservas en el historial que se eliminarán. ¿Desea continuar?`
            : `Se eliminará la clase de ${currentLesson.specialty_name || 'Sin especialidad'} del día ${currentLesson.day_of_week_label}. ¿Desea continuar?`;

        const confirmado = await mostrarConfirmacion(
            message,
            { titulo: 'Eliminar clase', textoAceptar: 'Eliminar', textoCancelar: 'Cancelar', claseAceptar: 'btn-danger' }
        );

        if (!confirmado) return;

        try {
            const formData = new FormData();
            formData.append('lesson_id', currentLesson.id);
            const res = await fetch(APP_URL + '/?url=coach-delete-lesson', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.status === 'success') {
                handleAlert(json.status, json.message, json.redirect);
                bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                handleAlert(json.status, json.message);
            }
        } catch (err) {
            handleAlert('error', 'Error de conexión al servidor.');
        }
    });

    initCalendar({
        data: lessons, dayMap, dayNames,
        cardButtonLabel: 'Ver detalle',
        onCardClick: (dayIdx, lesson) => {
            currentLesson = lesson;
            currentLesson.day_of_week_label = dayNames[dayIdx];
            const start = lesson.start_time.substring(0, 5);
            const end = lesson.end_time.substring(0, 5);
            const enrolled = lesson.enrolled || 0;
            document.getElementById('detailLevel').textContent = lesson.level_name;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + start + ' - ' + end;
            document.getElementById('detailSpecialty').textContent =
                lesson.specialty_name || 'Sin especialidad';
            document.getElementById('detailCapacity').textContent =
                enrolled + '/' + lesson.capacity;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        },
        onAddClick: (dayKey, dayName) => {
            createForm.reset();
            document.getElementById('createDay').value = dayKey;
            document.getElementById('createDayLabel').textContent = dayName;
            document.getElementById('createTime').value = '08:00:00';
            updateEndTimeOptions(document.getElementById('endTime'), '08:00:00');
            new bootstrap.Modal(document.getElementById('createModal')).show();
        }
    });
    </script>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
