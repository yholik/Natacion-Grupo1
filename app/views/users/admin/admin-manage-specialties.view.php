<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
// Datos base de la pantalla de especialidades.
$specialties = $specialties ?? [];
$editingSpecialty = $editingSpecialty ?? null;
$modalMessage = $modalMessage ?? null;
// Normaliza la URL base para armar acciones y enlaces del modulo.
$appUrl = htmlspecialchars(rtrim(Env::get('APP_URL'), '/'), ENT_QUOTES, 'UTF-8');

if (!function_exists('e')) {
    // Escapa texto antes de imprimirlo en la vista.
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Administrar Especialidades</h2>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <?= $editingSpecialty ? 'Editar Especialidad' : 'Agregar Especialidad' ?>
            </div>

            <div class="card-body">
                <!-- Un solo formulario sirve para alta y edicion. -->
                <form
                    action="<?= $appUrl ?>/?url=<?= $editingSpecialty ? 'admin-edit-specialty' : 'admin-create-specialty' ?>"
                    method="POST"
                    class="row g-3 align-items-end"
                >
                    <!-- En edicion se manda el id para actualizar el registro correcto. -->
                    <?php if ($editingSpecialty): ?>
                        <input type="hidden" name="specialty_id" value="<?= e($editingSpecialty['id']) ?>">
                    <?php endif; ?>

                    <div class="col-md-8">
                        <label for="name" class="form-label">Nombre</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="<?= e($editingSpecialty['name'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <?= $editingSpecialty ? 'Actualizar' : 'Guardar' ?>
                        </button>

                        <?php if ($editingSpecialty): ?>
                            <a href="<?= $appUrl ?>/?url=admin-manage-specialties" class="btn btn-secondary">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($specialties)): ?>
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No hay especialidades registradas.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($specialties as $specialty): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title fw-bold mb-0"><?= e($specialty['name']) ?></h6>
                                    <span class="badge bg-info"><?= e($specialty['coaches_count'] ?? 0) ?> profesores</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                                <div class="d-flex gap-2">
                                    <a href="<?= $appUrl ?>/?url=admin-manage-specialties&id=<?= e($specialty['id']) ?>" class="btn btn-sm btn-primary flex-fill">
                                        Editar
                                    </a>
                                    <form id="form-eliminar-especialidad-<?= e($specialty['id']) ?>" action="<?= $appUrl ?>/?url=admin-delete-specialty" method="POST" class="m-0 flex-fill">
                                        <input type="hidden" name="specialty_id" value="<?= e($specialty['id']) ?>">
                                        <button type="button" class="btn btn-sm btn-danger w-100 btn-eliminar-especialidad"
                                                data-form-id="form-eliminar-especialidad-<?= e($specialty['id']) ?>" data-nombre="<?= e($specialty['name']) ?>" data-coaches-count="<?= e($specialty['coaches_count'] ?? 0) ?>">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Modal comun del panel para confirmaciones y avisos. -->
        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-manage-specialties.js"></script>

<?php if (!empty($modalMessage)): ?>
<script>
// Reusa el modal comun para avisos que no requieren otra accion.
document.addEventListener('DOMContentLoaded', async () => {
    await mostrarConfirmacion('<?= e($modalMessage) ?>', {
        titulo: 'Especialidad duplicada',
        textoAceptar: 'Entendido',
        claseAceptar: 'btn-primary',
        mostrarCancelar: false
    });
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
