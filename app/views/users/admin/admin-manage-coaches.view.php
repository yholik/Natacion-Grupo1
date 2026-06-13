<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$coaches = $coaches ?? [];
$appUrl = htmlspecialchars(rtrim(Env::get('APP_URL'), '/'), ENT_QUOTES, 'UTF-8');

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatDateOrDash($value)
{
    if (empty($value)) {
        return '-';
    }

    return e(date('Y-m-d', strtotime($value)));
}
?>

<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="flex-grow-1 p-5 bg-white">
        <h1>Gestionar Profesores</h1>
        <hr>

        <div class="d-flex gap-2 mb-4">
            <a href="<?= $appUrl ?>/?url=admin-create-coach" class="btn btn-success">
                Agregar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Listado de Profesores
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Especialidad</th>
                                <th>Rol</th>
                                <th>Última Actualización</th>
                                <th>Fecha Alta</th>
                                <th>Fecha Baja</th>
                                <th>Estado</th>
                                <th style="width: 190px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($coaches)): ?>
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        No hay profesores registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($coaches as $coach): ?>
                                <?php
                                $userId = $coach['user_id'];
                                $fullName = trim($coach['first_name'] . ' ' . $coach['last_name']);
                                $isActive = empty($coach['deleted_at']);
                                $formId = 'form-cambiar-estado-' . $userId;
                                ?>

                                <tr>
                                    <td><?= e($coach['id']) ?></td>
                                    <td><?= e($coach['first_name']) ?></td>
                                    <td><?= e($coach['last_name']) ?></td>
                                    <td><?= e($coach['email']) ?></td>
                                    <td><?= e($coach['phone']) ?></td>
                                    <td><?= e($coach['specialty']) ?></td>
                                    <td>Profesor</td>
                                    <td><?= formatDateOrDash($coach['updated_at'] ?? null) ?></td>
                                    <td><?= formatDateOrDash($coach['created_at'] ?? null) ?></td>
                                    <td><?= formatDateOrDash($coach['deleted_at'] ?? null) ?></td>

                                    <td>
                                        <?php if ($isActive): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Baja</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a
                                                href="<?= $appUrl ?>/?url=admin-edit-coach&id=<?= e($userId) ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                id="<?= e($formId) ?>"
                                                action="<?= $appUrl ?>/?url=<?= $isActive ? 'admin-deactivate-coach' : 'admin-activate-coach' ?>"
                                                method="POST"
                                                class="m-0"
                                            >
                                                <input type="hidden" name="user_id" value="<?= e($userId) ?>">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm <?= $isActive ? 'btn-danger' : 'btn-success' ?> btn-cambiar-estado"
                                                    data-form-id="<?= e($formId) ?>"
                                                    data-nombre="<?= e($fullName) ?>"
                                                    data-accion="<?= $isActive ? 'baja' : 'alta' ?>"
                                                >
                                                    <?= $isActive ? 'Dar de Baja' : 'Dar de Alta' ?>
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

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-coaches.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>