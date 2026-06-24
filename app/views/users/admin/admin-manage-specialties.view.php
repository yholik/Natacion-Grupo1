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
        <h1>Administrar Especialidades</h1>
        <hr>

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

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Catalogo de Especialidades
            </div>

            <div class="card-body p-0">
                <!-- El listado refleja cuantas relaciones activas tiene cada especialidad. -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Profesores Asociados</th>
                                <th style="width: 220px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($specialties)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No hay especialidades registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($specialties as $specialty): ?>
                                <!-- Cada fila deja editar o intentar borrar una especialidad puntual. -->
                                <tr>
                                    <td><?= e($specialty['id']) ?></td>
                                    <td><?= e($specialty['name']) ?></td>
                                    <td><?= e($specialty['coaches_count'] ?? 0) ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a
                                                href="<?= $appUrl ?>/?url=admin-manage-specialties&id=<?= e($specialty['id']) ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                id="form-eliminar-especialidad-<?= e($specialty['id']) ?>"
                                                action="<?= $appUrl ?>/?url=admin-delete-specialty"
                                                method="POST"
                                                class="m-0"
                                            >
                                                <input type="hidden" name="specialty_id" value="<?= e($specialty['id']) ?>">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger btn-eliminar-especialidad"
                                                    data-form-id="form-eliminar-especialidad-<?= e($specialty['id']) ?>"
                                                    data-nombre="<?= e($specialty['name']) ?>"
                                                    data-coaches-count="<?= e($specialty['coaches_count'] ?? 0) ?>"
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
