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
                        <select name="level" class="form-select" required>
                            <option value="">Seleccionar especialidad...</option>
                            <?php foreach ($specialties as $specialty): ?>
                            <option value="<?= htmlspecialchars($specialty['name']) ?>"><?= htmlspecialchars($specialty['name']) ?></option>
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
            </div>
        </div>
    </div>

    <script>
    const dayMap = { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 };
    const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const dayKeys = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const lessons = <?= json_encode($lessons) ?>;
    const APP_URL = '<?= rtrim(Env::get('ASSET_URL'), '/') ?>';

    function updateEndTimeOptions() {
        const startTime = document.getElementById('createTime').value;
        const endTimeSelect = document.getElementById('endTime');
        const startHour = parseInt(startTime.substring(0, 2));

        Array.from(endTimeSelect.options).forEach(option => {
            const optionHour = parseInt(option.value.substring(0, 2));
            option.disabled = optionHour <= startHour;
        });

        const nextHour = startHour + 1;
        const nextHourStr = String(nextHour).padStart(2, '0') + ':00:00';
        if (endTimeSelect.value <= startTime) {
            endTimeSelect.value = nextHourStr;
        }
    }

    document.getElementById('createTime').addEventListener('change', updateEndTimeOptions);
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';
    import { handleAlert } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/services/ui.js';

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

    initCalendar({
        data: lessons, dayMap, dayNames,
        cardButtonLabel: 'Ver detalle',
        onCardClick: (dayIdx, lesson) => {
            const start = lesson.start_time.substring(0, 5);
            const end = lesson.end_time.substring(0, 5);
            const enrolled = lesson.enrolled || 0;
            document.getElementById('detailLevel').textContent = lesson.level;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + start + ' - ' + end;
            document.getElementById('detailSpecialty').textContent =
                lesson.specialty || 'Sin especialidad';
            document.getElementById('detailCapacity').textContent =
                enrolled + '/' + lesson.capacity;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        },
        onAddClick: (dayKey, dayName) => {
            createForm.reset();
            document.getElementById('createDay').value = dayKey;
            document.getElementById('createDayLabel').textContent = dayName;
            document.getElementById('createTime').value = '08:00:00';
            updateEndTimeOptions();
            new bootstrap.Modal(document.getElementById('createModal')).show();
        }
    });
    </script>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
