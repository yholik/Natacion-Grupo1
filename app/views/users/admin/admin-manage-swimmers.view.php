<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$swimmers = $swimmers ?? [];
$appUrl = htmlspecialchars(rtrim(Env::get('APP_URL'), '/'), ENT_QUOTES, 'UTF-8');

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
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
        <h1>Gestionar Nadadores</h1>
        <hr>

        <div class="d-flex gap-2 mb-4">
            <a href="<?= $appUrl ?>/?url=admin-create-swimmer" class="btn btn-success">
                Agregar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Listado de Nadadores
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha Nac.</th>
                                <th>Última Actualización</th>
                                <th>Fecha Alta</th>
                                <th>Fecha Baja</th>
                                <th>Estado</th>
                                <th style="width: 190px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($swimmers)): ?>
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        No hay nadadores registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($swimmers as $swimmer): ?>
                                <?php
                                $userId = $swimmer['user_id'];
                                $fullName = trim($swimmer['first_name'] . ' ' . $swimmer['last_name']);
                                $isActive = empty($swimmer['deleted_at']);
                                $formId = 'form-cambiar-estado-swimmer-' . $userId;
                                $profileImage = $swimmer['profile_image'] ?? 'default-profile.png';
                                ?>

                                <tr>
                                    <td><?= e($swimmer['id']) ?></td>
                                    <td>
                                        <img
                                            src="<?= $appUrl ?>/public/img/uploads/profiles/swimmers/<?= e($profileImage) ?>"
                                            alt="Foto de <?= e($fullName) ?>"
                                            class="rounded-circle border"
                                            style="width: 48px; height: 48px; object-fit: cover;"
                                        >
                                    </td>
                                    <td><?= e($swimmer['first_name']) ?></td>
                                    <td><?= e($swimmer['last_name']) ?></td>
                                    <td><?= e($swimmer['email']) ?></td>
                                    <td><?= e($swimmer['phone'] ?? '-') ?></td>
                                    <td><?= formatDateOrDash($swimmer['birth_date'] ?? null) ?></td>
                                    <td><?= formatDateOrDash($swimmer['updated_at'] ?? null) ?></td>
                                    <td><?= formatDateOrDash($swimmer['created_at'] ?? null) ?></td>
                                    <td><?= formatDateOrDash($swimmer['deleted_at'] ?? null) ?></td>
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
                                                href="<?= $appUrl ?>/?url=admin-edit-swimmer&id=<?= e($userId) ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                id="<?= e($formId) ?>"
                                                action="<?= $appUrl ?>/?url=<?= $isActive ? 'admin-deactivate-swimmer' : 'admin-activate-swimmer' ?>"
                                                method="POST"
                                                class="m-0"
                                            >
                                                <input type="hidden" name="user_id" value="<?= e($userId) ?>">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm <?= $isActive ? 'btn-danger' : 'btn-success' ?> btn-cambiar-estado-swimmer"
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

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-swimmers.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
