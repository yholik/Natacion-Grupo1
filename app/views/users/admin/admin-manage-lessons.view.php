<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$lessons = $lessons ?? [];
$coaches = $coaches ?? [];
$levels = $levels ?? [];
$specialties = $specialties ?? [];

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
                        <label for="specialty_id" class="form-label">Especialidad</label>
                        <select id="specialty_id" name="specialty_id" class="form-select" required>
                            <option value="">Seleccionar especialidad...</option>
                            <?php foreach ($specialties as $specialty): ?>
                                <option value="<?= e($specialty['id']) ?>"><?= e($specialty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level_id" class="form-label">Nivel</label>
                        <select id="level_id" name="level_id" class="form-select" required>
                            <option value="">Seleccionar nivel...</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= e($level['id']) ?>"><?= e($level['name']) ?></option>
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

    <script>
    window.adminManageLessonsConfig = {
        dayMap: { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 },
        dayNames: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        lessons: <?= json_encode($lessons, JSON_UNESCAPED_UNICODE) ?>,
        appUrl: '<?= e($appUrl) ?>'
    };
    </script>
    <script type="module" src="<?= e($assetUrl) ?>/js/modules/admin/admin-manage-lessons-page.js"></script>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>
