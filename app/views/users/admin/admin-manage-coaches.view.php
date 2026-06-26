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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gestionar Profesores</h2>
            <a href="<?= $appUrl ?>/?url=admin-create-coach" class="btn btn-success">
                Agregar
            </a>
        </div>

        <?php if (empty($coaches)): ?>
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No hay profesores registrados.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($coaches as $coach): ?>
                    <?php
                    $userId = $coach['user_id'];
                    $fullName = trim($coach['first_name'] . ' ' . $coach['last_name']);
                    $isActive = empty($coach['deleted_at']);
                    $formId = 'form-cambiar-estado-' . $userId;
                    ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title fw-bold mb-0"><?= e($fullName) ?></h6>
                                    <?php if ($isActive): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Baja</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-text text-muted small">
                                    <div class="mb-1"><?= e($coach['email']) ?></div>
                                    <div class="mb-1"><?= e($coach['phone'] ?? '-') ?></div>
                                    <div class="mb-1"><?= e($coach['specialty_names'] ?? 'Sin especialidad') ?></div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                                <div class="d-flex gap-2">
                                    <a href="<?= $appUrl ?>/?url=admin-edit-coach&id=<?= e($userId) ?>" class="btn btn-sm btn-primary flex-fill">
                                        Editar
                                    </a>
                                    <form id="<?= e($formId) ?>" action="<?= $appUrl ?>/?url=<?= $isActive ? 'admin-deactivate-coach' : 'admin-activate-coach' ?>" method="POST" class="m-0 flex-fill">
                                        <input type="hidden" name="user_id" value="<?= e($userId) ?>">
                                        <button type="button" class="btn btn-sm <?= $isActive ? 'btn-danger' : 'btn-success' ?> w-100 btn-cambiar-estado"
                                                data-form-id="<?= e($formId) ?>" data-nombre="<?= e($fullName) ?>" data-accion="<?= $isActive ? 'baja' : 'alta' ?>" data-especialidades="<?= e($coach['specialty_names'] ?? '') ?>">
                                            <?= $isActive ? 'Dar de Baja' : 'Dar de Alta' ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-coaches.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
