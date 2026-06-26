<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$lessons = $lessons ?? [];
$coaches = $coaches ?? [];

$appUrl = htmlspecialchars(rtrim(Env::get('APP_URL'), '/'), ENT_QUOTES, 'UTF-8');

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatTimeOrDash')) {
    function formatTimeOrDash($value)
    {
        if (empty($value)) {
            return '-';
        }

        return e(substr($value, 0, 5));
    }
}

if (!function_exists('formatDateOrDash')) {
    function formatDateOrDash($value)
    {
        if (empty($value)) {
            return '-';
        }

        return e(date('Y-m-d', strtotime($value)));
    }
}
?>

<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="flex-grow-1 p-5 bg-white">
        <h1>Gestionar Clases</h1>
        <hr>

        <div class="d-flex gap-2 mb-4">
            <button
                type="button"
                class="btn btn-success"
                id="btnAbrirModalCrearClase"
            >
                Agregar
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Listado de Clases
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Profesor</th>
                                <th>Especialidad</th>
                                <th>Nivel</th>
                                <th>Día</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Cupo</th>
                                <th>Inscriptos</th>
                                <th>Disponibles</th>
                                <th>Fecha Alta</th>
                                <th style="width: 170px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($lessons)): ?>
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        No hay clases registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($lessons as $lesson): ?>
                                <?php
                                $lessonId = $lesson['id'];

                                $coachName = trim(
                                    ($lesson['coach_first_name'] ?? '') . ' ' . ($lesson['coach_last_name'] ?? '')
                                );

                                if ($coachName === '') {
                                    $coachName = 'Profesor ID ' . ($lesson['coach_id'] ?? '-');
                                }

                                $capacity = (int) ($lesson['capacity'] ?? 0);
                                $enrolled = (int) ($lesson['enrolled'] ?? 0);
                                $available = max($capacity - $enrolled, 0);

                                $formId = 'form-eliminar-clase-' . $lessonId;

                                $lessonDescription = trim(
                                    ($lesson['level'] ?? 'Clase') .
                                    ' - ' .
                                    ($lesson['day_of_week'] ?? '') .
                                    ' ' .
                                    formatTimeOrDash($lesson['start_time'] ?? null)
                                );
                                ?>

                                <tr>
                                    <td><?= e($lessonId) ?></td>
                                    <td><?= e($coachName) ?></td>
                                    <td><?= e($lesson['specialty'] ?? '-') ?></td>
                                    <td><?= e($lesson['level'] ?? '-') ?></td>
                                    <td><?= e($lesson['day_of_week'] ?? '-') ?></td>
                                    <td><?= formatTimeOrDash($lesson['start_time'] ?? null) ?></td>
                                    <td><?= formatTimeOrDash($lesson['end_time'] ?? null) ?></td>
                                    <td><?= e($capacity) ?></td>
                                    <td><?= e($enrolled) ?></td>
                                    <td><?= e($available) ?></td>
                                    <td><?= formatDateOrDash($lesson['created_at'] ?? null) ?></td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary btn-editar-clase"
                                                data-lesson-id="<?= e($lessonId) ?>"
                                                data-coach-id="<?= e($lesson['coach_id'] ?? '') ?>"
                                                data-level="<?= e($lesson['level'] ?? '') ?>"
                                                data-day-of-week="<?= e($lesson['day_of_week'] ?? '') ?>"
                                                data-start-time="<?= e($lesson['start_time'] ?? '') ?>"
                                                data-end-time="<?= e($lesson['end_time'] ?? '') ?>"
                                                data-capacity="<?= e($capacity) ?>"
                                            >
                                                Editar
                                            </button>

                                            <form
                                                id="<?= e($formId) ?>"
                                                action="<?= $appUrl ?>/?url=admin-delete-lesson"
                                                method="POST"
                                                class="m-0"
                                            >
                                                <input type="hidden" name="lesson_id" value="<?= e($lessonId) ?>">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger btn-eliminar-clase"
                                                    data-form-id="<?= e($formId) ?>"
                                                    data-clase="<?= e($lessonDescription) ?>"
                                                    data-profesor="<?= e($coachName) ?>"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal CREAR / EDITAR clase -->
        <div class="modal fade" id="modalClase" tabindex="-1">
            <div class="modal-dialog">
                <form
                    id="formClase"
                    method="POST"
                    action="<?= $appUrl ?>/?url=admin-create-lesson"
                    class="modal-content"
                >
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalClaseTitulo">Agregar Clase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="lesson_id" id="lesson_id">

                        <div class="mb-3">
                            <label for="coach_id" class="form-label">Profesor</label>
                            <select id="coach_id" name="coach_id" class="form-select" required>
                                <option value="">Seleccione un profesor</option>

                                <?php foreach ($coaches as $coach): ?>
                                    <?php
                                    $coachId = $coach['id'] ?? $coach['user_id'] ?? '';

                                    $coachFullName = trim(
                                        ($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? '')
                                    );

                                    if ($coachFullName === '') {
                                        $coachFullName = 'Profesor ID ' . $coachId;
                                    }
                                    ?>

                                    <option value="<?= e($coachId) ?>">
                                        <?= e($coachFullName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label">Nivel</label>
                            <input
                                type="text"
                                id="level"
                                name="level"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="day_of_week" class="form-label">Día</label>
                            <select id="day_of_week" name="day_of_week" class="form-select" required>
                                <option value="">Seleccione un día</option>
                                <option value="Monday">Lunes</option>
                                <option value="Tuesday">Martes</option>
                                <option value="Wednesday">Miércoles</option>
                                <option value="Thursday">Jueves</option>
                                <option value="Friday">Viernes</option>
                                <option value="Saturday">Sábado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label">Horario inicio</label>
                            <select id="start_time" name="start_time" class="form-select" required>
                                <option value="">Seleccione horario de inicio</option>

                                <?php for ($h = 7; $h <= 22; $h++): ?>
                                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00">
                                        <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="end_time" class="form-label">Horario fin</label>
                            <select id="end_time" name="end_time" class="form-select" required>
                                <option value="">Seleccione horario de fin</option>

                                <?php for ($h = 8; $h <= 23; $h++): ?>
                                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00">
                                        <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label">Cupo máximo</label>
                            <input
                                type="number"
                                id="capacity"
                                name="capacity"
                                class="form-control"
                                min="1"
                                required
                            >
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="btn btn-success"
                            id="btnGuardarClase"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-lessons.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const appUrl = '<?= $appUrl ?>';

    const btnAbrirModalCrearClase = document.getElementById('btnAbrirModalCrearClase');
    const botonesEditarClase = document.querySelectorAll('.btn-editar-clase');

    const modalClaseElement = document.getElementById('modalClase');
    const modalClaseTitulo = document.getElementById('modalClaseTitulo');

    const formClase = document.getElementById('formClase');
    const btnGuardarClase = document.getElementById('btnGuardarClase');

    const inputLessonId = document.getElementById('lesson_id');
    const inputCoachId = document.getElementById('coach_id');
    const inputLevel = document.getElementById('level');
    const inputDayOfWeek = document.getElementById('day_of_week');
    const inputStartTime = document.getElementById('start_time');
    const inputEndTime = document.getElementById('end_time');
    const inputCapacity = document.getElementById('capacity');

    function normalizarHora(value) {
        if (!value) {
            return '';
        }

        const time = String(value).trim();

        if (time.length === 5) {
            return time + ':00';
        }

        return time.substring(0, 8);
    }

    function abrirModalClase() {
        const modalClase =
            bootstrap.Modal.getInstance(modalClaseElement) ||
            new bootstrap.Modal(modalClaseElement);

        modalClase.show();
    }

    function limpiarFormularioClase() {
        formClase.reset();

        inputLessonId.value = '';
        formClase.action = appUrl + '/?url=admin-create-lesson';
        modalClaseTitulo.textContent = 'Agregar Clase';
        btnGuardarClase.textContent = 'Guardar';
    }

    function cargarFormularioEdicion(button) {
        formClase.reset();

        inputLessonId.value = button.dataset.lessonId || '';
        inputCoachId.value = button.dataset.coachId || '';
        inputLevel.value = button.dataset.level || '';
        inputDayOfWeek.value = button.dataset.dayOfWeek || '';
        inputStartTime.value = normalizarHora(button.dataset.startTime || '');
        inputEndTime.value = normalizarHora(button.dataset.endTime || '');
        inputCapacity.value = button.dataset.capacity || '';

        formClase.action = appUrl + '/?url=admin-edit-lesson';
        modalClaseTitulo.textContent = 'Editar Clase';
        btnGuardarClase.textContent = 'Actualizar';
    }

    if (btnAbrirModalCrearClase) {
        btnAbrirModalCrearClase.addEventListener('click', () => {
            limpiarFormularioClase();
            abrirModalClase();
        });
    }

    botonesEditarClase.forEach((button) => {
        button.addEventListener('click', () => {
            cargarFormularioEdicion(button);
            abrirModalClase();
        });
    });

    if (formClase && btnGuardarClase) {
        formClase.addEventListener('submit', async (event) => {
            event.preventDefault();

            const esEdicion = inputLessonId.value !== '';

            btnGuardarClase.disabled = true;
            btnGuardarClase.textContent = esEdicion ? 'Actualizando...' : 'Guardando...';

            try {
                const formData = new FormData(formClase);

                const response = await fetch(formClase.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const contentType = response.headers.get('content-type') || '';
                const rawResponse = await response.text();

                if (!contentType.includes('application/json')) {
                    console.error('Respuesta no JSON del servidor:', rawResponse);

                    await window.mostrarConfirmacion(
                        esEdicion
                            ? 'No se pudo actualizar la clase. El servidor devolvió una respuesta inválida.'
                            : 'No se pudo crear la clase. El servidor devolvió una respuesta inválida.',
                        {
                            titulo: 'Error',
                            textoAceptar: 'Aceptar',
                            claseAceptar: 'btn-danger',
                            mostrarCancelar: false
                        }
                    );

                    return;
                }

                const result = JSON.parse(rawResponse);

                const status = result.status || result.type || '';
                const message = result.message || 'Operación finalizada.';
                const redirectUrl = result.redirect || result.url || null;

                if (status === 'success') {
                    const modalClase =
                        bootstrap.Modal.getInstance(modalClaseElement) ||
                        new bootstrap.Modal(modalClaseElement);

                    modalClase.hide();

                    await window.mostrarConfirmacion(
                        message,
                        {
                            titulo: esEdicion ? 'Clase actualizada' : 'Clase creada',
                            textoAceptar: 'Aceptar',
                            claseAceptar: 'btn-success',
                            mostrarCancelar: false
                        }
                    );

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        return;
                    }

                    window.location.reload();
                    return;
                }

                await window.mostrarConfirmacion(
                    message || (esEdicion ? 'No se pudo actualizar la clase.' : 'No se pudo crear la clase.'),
                    {
                        titulo: esEdicion ? 'No se pudo actualizar' : 'No se pudo crear',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

            } catch (error) {
                console.error('Error capturado:', error);

                await window.mostrarConfirmacion(
                    esEdicion
                        ? 'No se pudo actualizar la clase. Revisá la conexión, la ruta o el error del servidor.'
                        : 'No se pudo crear la clase. Revisá la conexión, la ruta o el error del servidor.',
                    {
                        titulo: 'Error',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

            } finally {
                btnGuardarClase.disabled = false;
                btnGuardarClase.textContent = esEdicion ? 'Actualizar' : 'Guardar';
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>