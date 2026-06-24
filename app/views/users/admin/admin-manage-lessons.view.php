<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$lessons = $lessons ?? [];
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
            <a href="<?= $appUrl ?>/?url=admin-create-lesson" class="btn btn-success">
                Agregar
            </a>
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
                                            <a
                                                href="<?= $appUrl ?>/?url=admin-edit-lesson&id=<?= e($lessonId) ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Editar
                                            </a>

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

        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-lessons.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>