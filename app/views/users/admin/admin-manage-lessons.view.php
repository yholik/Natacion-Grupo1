<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$lessons = $lessons ?? [];
$coaches = $coaches ?? [];
$levels = $levels ?? [];

if (!function_exists('e')) {
    // Escapa texto antes de imprimirlo en la vista.
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('encodeJsonForAttr')) {
    // Deja un array listo para usar dentro de data-*.
    function encodeJsonForAttr($value)
    {
        return htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
    }
}

$appUrl = rtrim(Env::get('APP_URL'), '/');
$assetUrl = rtrim(Env::get('ASSET_URL'), '/');
?>

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

    <!-- Un solo modal para alta y edición. -->
    <div class="modal fade" id="lessonModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="lessonForm" method="POST" action="<?= e($appUrl) ?>/?url=admin-create-lesson" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lessonModalTitle">Agregar clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lesson_id" id="lessonId">
                    <input type="hidden" name="day_of_week" id="lessonDay">

                    <p><strong>Día:</strong> <span id="lessonDayLabel"></span></p>

                    <div class="mb-3">
                        <label for="coach_id" class="form-label">Profesor</label>
                        <select id="coach_id" name="coach_id" class="form-select" required>
                            <option value="">Seleccionar profesor...</option>
                            <?php foreach ($coaches as $coach): ?>
                                <?php
                                $coachId = $coach['id'] ?? '';
                                $coachFullName = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                                $specialtyNames = array_values(array_filter(array_map('trim', explode(',', $coach['specialty_names'] ?? ''))));
                                ?>
                                <option
                                    value="<?= e($coachId) ?>"
                                    data-coach-name="<?= e($coachFullName !== '' ? $coachFullName : 'Profesor ID ' . $coachId) ?>"
                                    data-specialties="<?= encodeJsonForAttr($specialtyNames) ?>"
                                >
                                    <?= e($coachFullName !== '' ? $coachFullName : 'Profesor ID ' . $coachId) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="createTime" class="form-label">Horario inicio</label>
                        <select name="start_time" id="createTime" class="form-select" required>
                            <?php for ($h = 7; $h <= 22; $h++): ?>
                                <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00">
                                    <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="endTime" class="form-label">Horario fin</label>
                        <select name="end_time" id="endTime" class="form-select" required>
                            <?php for ($h = 8; $h <= 23; $h++): ?>
                                <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00">
                                    <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="specialty" class="form-label">Especialidad</label>
                        <select id="specialty" name="specialty" class="form-select" required>
                            <option value="">Seleccionar especialidad...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level" class="form-label">Nivel</label>
                        <select id="level" name="level" class="form-select" required>
                            <option value="">Seleccionar nivel...</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= e($level) ?>"><?= e($level) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="capacity" class="form-label">Cupo máximo</label>
                        <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="lessonSubmitButton">Crear clase</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Muestra los datos y deja editar o borrar. -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Profesor:</strong> <span id="detailCoach"></span></p>
                    <p><strong>Nivel:</strong> <span id="detailLevel"></span></p>
                    <p><strong>Horario:</strong> <span id="detailSchedule"></span></p>
                    <p><strong>Especialidad:</strong> <span id="detailSpecialty"></span></p>
                    <p><strong>Cupo:</strong> <span id="detailCapacity"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="detailEditButton">Editar</button>
                    <button type="button" class="btn btn-danger" id="detailDeleteButton">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>

    <script>
    const dayMap = { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 };
    const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const lessons = <?= json_encode($lessons, JSON_UNESCAPED_UNICODE) ?>;
    const APP_URL = '<?= e($appUrl) ?>';
    const ASSET_URL = '<?= e($assetUrl) ?>';
    </script>

    <script type="module">
    import { initCalendar } from '<?= e($assetUrl) ?>/js/modules/calendar.js';

    const lessonForm = document.getElementById('lessonForm');
    const lessonModalElement = document.getElementById('lessonModal');
    const detailModalElement = document.getElementById('detailModal');
    const lessonModalTitle = document.getElementById('lessonModalTitle');
    const lessonSubmitButton = document.getElementById('lessonSubmitButton');
    const lessonIdInput = document.getElementById('lessonId');
    const lessonDayInput = document.getElementById('lessonDay');
    const lessonDayLabel = document.getElementById('lessonDayLabel');
    const coachSelect = document.getElementById('coach_id');
    const specialtySelect = document.getElementById('specialty');
    const levelSelect = document.getElementById('level');
    const startTimeSelect = document.getElementById('createTime');
    const endTimeSelect = document.getElementById('endTime');
    const capacityInput = document.getElementById('capacity');
    const detailEditButton = document.getElementById('detailEditButton');
    const detailDeleteButton = document.getElementById('detailDeleteButton');

    let selectedLesson = null;

    // Ajusta los horarios de fin según la hora de inicio elegida.
    function updateEndTimeOptions() {
        const startTime = startTimeSelect.value;
        const startHour = parseInt(startTime.substring(0, 2), 10);

        Array.from(endTimeSelect.options).forEach((option) => {
            const optionHour = parseInt(option.value.substring(0, 2), 10);
            option.disabled = optionHour <= startHour;
        });

        const nextHour = String(startHour + 1).padStart(2, '0') + ':00:00';
        if (endTimeSelect.value <= startTime) {
            endTimeSelect.value = nextHour;
        }
    }

    // Devuelve las especialidades del profesor elegido.
    function getCoachSpecialties() {
        const selectedOption = coachSelect.options[coachSelect.selectedIndex];

        if (!selectedOption || !selectedOption.dataset.specialties) {
            return [];
        }

        try {
            return JSON.parse(selectedOption.dataset.specialties);
        } catch (error) {
            console.error('No se pudieron leer las especialidades del profesor.', error);
            return [];
        }
    }

    // Reconstruye el combo de especialidades según el profesor.
    function rebuildSpecialtyOptions(selectedValue = '') {
        const specialties = getCoachSpecialties();

        specialtySelect.innerHTML = '<option value="">Seleccionar especialidad...</option>';

        specialties.forEach((specialty) => {
            const option = document.createElement('option');
            option.value = specialty;
            option.textContent = specialty;
            option.selected = specialty === selectedValue;
            specialtySelect.appendChild(option);
        });

        if (!specialties.includes(selectedValue)) {
            specialtySelect.value = '';
        }
    }

    function openLessonModal() {
        bootstrap.Modal.getOrCreateInstance(lessonModalElement).show();
    }

    function closeLessonModal() {
        bootstrap.Modal.getOrCreateInstance(lessonModalElement).hide();
    }

    function closeDetailModal() {
        bootstrap.Modal.getOrCreateInstance(detailModalElement).hide();
    }

    // Prepara el formulario para una clase nueva.
    function setupCreateMode(dayKey, dayName) {
        lessonForm.reset();
        lessonForm.action = APP_URL + '/?url=admin-create-lesson';
        lessonModalTitle.textContent = 'Agregar clase';
        lessonSubmitButton.textContent = 'Crear clase';
        lessonIdInput.value = '';
        lessonDayInput.value = dayKey;
        lessonDayLabel.textContent = dayName;
        startTimeSelect.value = '08:00:00';
        capacityInput.value = '1';
        rebuildSpecialtyOptions();
        updateEndTimeOptions();
    }

    // Carga los datos de una clase para editarla.
    function setupEditMode(lesson) {
        lessonForm.reset();
        lessonForm.action = APP_URL + '/?url=admin-edit-lesson';
        lessonModalTitle.textContent = 'Editar clase';
        lessonSubmitButton.textContent = 'Guardar cambios';
        lessonIdInput.value = lesson.id || '';
        lessonDayInput.value = lesson.day_of_week || '';
        lessonDayLabel.textContent = dayNames[dayMap[lesson.day_of_week]] || '';
        coachSelect.value = lesson.coach_id || '';
        rebuildSpecialtyOptions(lesson.specialty || '');
        levelSelect.value = lesson.level || '';
        startTimeSelect.value = lesson.start_time || '08:00:00';
        updateEndTimeOptions();
        endTimeSelect.value = lesson.end_time || '';
        capacityInput.value = lesson.capacity || 1;
    }

    // Muestra un mensaje informativo usando el modal compartido.
    async function showMessage(title, message, acceptClass = 'btn-primary') {
        await window.mostrarConfirmacion(message, {
            titulo: title,
            textoAceptar: 'Aceptar',
            claseAceptar: acceptClass,
            mostrarCancelar: false
        });
    }

    // Envía el alta o edición al backend.
    async function submitLessonForm(event) {
        event.preventDefault();

        const isEditing = lessonIdInput.value !== '';
        lessonSubmitButton.disabled = true;
        lessonSubmitButton.textContent = isEditing ? 'Guardando...' : 'Creando...';

        try {
            const response = await fetch(lessonForm.action, {
                method: 'POST',
                body: new FormData(lessonForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                closeLessonModal();
                await showMessage(
                    isEditing ? 'Clase actualizada' : 'Clase creada',
                    result.message || 'Operación finalizada.',
                    'btn-success'
                );
                window.location.reload();
                return;
            }

            await showMessage(
                isEditing ? 'No se pudo actualizar' : 'No se pudo crear',
                result.message || 'No se pudo guardar la clase.',
                'btn-danger'
            );
        } catch (error) {
            console.error('Error al guardar la clase.', error);
            await showMessage('Error', 'No se pudo guardar la clase. Revisá la conexión o la respuesta del servidor.', 'btn-danger');
        } finally {
            lessonSubmitButton.disabled = false;
            lessonSubmitButton.textContent = isEditing ? 'Guardar cambios' : 'Crear clase';
        }
    }

    // Pide confirmación y borra la clase elegida.
    async function deleteSelectedLesson() {
        if (!selectedLesson) {
            return;
        }

        const coachName = `${selectedLesson.coach_first_name || ''} ${selectedLesson.coach_last_name || ''}`.trim();
        const confirmed = await window.mostrarConfirmacion(
            `Se eliminará la clase de ${coachName || 'sin profesor'} del ${dayNames[dayMap[selectedLesson.day_of_week]]}.`,
            {
                titulo: 'Eliminar clase',
                textoAceptar: 'Eliminar',
                textoCancelar: 'Cancelar',
                claseAceptar: 'btn-danger'
            }
        );

        if (!confirmed) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('lesson_id', selectedLesson.id);

            const response = await fetch(APP_URL + '/?url=admin-delete-lesson', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                closeDetailModal();
                await showMessage('Clase eliminada', result.message || 'Clase eliminada correctamente.', 'btn-success');
                window.location.reload();
                return;
            }

            await showMessage('No se pudo eliminar', result.message || 'No se pudo eliminar la clase.', 'btn-danger');
        } catch (error) {
            console.error('Error al eliminar la clase.', error);
            await showMessage('Error', 'No se pudo eliminar la clase. Revisá la conexión o la respuesta del servidor.', 'btn-danger');
        }
    }

    // Completa el modal detalle con la clase elegida.
    function showLessonDetail(dayIdx, lesson) {
        selectedLesson = lesson;

        const start = lesson.start_time.substring(0, 5);
        const end = lesson.end_time.substring(0, 5);
        const enrolled = lesson.enrolled || 0;
        const coachName = `${lesson.coach_first_name || ''} ${lesson.coach_last_name || ''}`.trim();

        document.getElementById('detailCoach').textContent = coachName || 'Sin profesor';
        document.getElementById('detailLevel').textContent = lesson.level || '-';
        document.getElementById('detailSchedule').textContent = `${dayNames[dayIdx]} ${start} - ${end}`;
        document.getElementById('detailSpecialty').textContent = lesson.specialty || 'Sin especialidad';
        document.getElementById('detailCapacity').textContent = `${enrolled}/${lesson.capacity}`;

        bootstrap.Modal.getOrCreateInstance(detailModalElement).show();
    }

    coachSelect.addEventListener('change', () => rebuildSpecialtyOptions());
    startTimeSelect.addEventListener('change', updateEndTimeOptions);
    lessonForm.addEventListener('submit', submitLessonForm);

    detailEditButton.addEventListener('click', () => {
        if (!selectedLesson) {
            return;
        }

        closeDetailModal();
        setupEditMode(selectedLesson);
        openLessonModal();
    });

    detailDeleteButton.addEventListener('click', deleteSelectedLesson);

    initCalendar({
        data: lessons,
        dayMap,
        dayNames,
        cardButtonLabel: 'Ver detalle',
        onCardClick: (dayIdx, lesson) => showLessonDetail(dayIdx, lesson),
        onAddClick: (dayKey, dayName) => {
            setupCreateMode(dayKey, dayName);
            openLessonModal();
        }
    });
    </script>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>
