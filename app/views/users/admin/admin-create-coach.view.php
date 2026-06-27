<?php
$appUrl = htmlspecialchars(rtrim(Env::get('APP_URL'), '/'), ENT_QUOTES, 'UTF-8');

$coach = $coach ?? null;
$specialties = $specialties ?? [];
$isEdit = !empty($coach);
$selectedSpecialtyIds = array_map('intval', $coach['specialty_ids'] ?? []);

// Estos textos cambian segun si el admin crea o edita un profesor.
$pageTitle = $isEdit ? 'Editar Profesor' : 'Agregar Profesor';
$buttonText = $isEdit ? 'Actualizar' : 'Guardar';

$formAction = $isEdit
    ? $appUrl . '/?url=admin-edit-coach'
    : $appUrl . '/?url=admin-create-coach';

if (!function_exists('e')) {
    // Escapa texto antes de imprimirlo en atributos o contenido HTML.
    function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

include __DIR__ . '/../layout/header.php';
?>

<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="flex-grow-1 p-5 bg-white">
        <h1><?= e($pageTitle) ?></h1>
        <hr>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Datos del Profesor
            </div>

            <div class="card-body">
                <form
                    id="formCrearCoach"
                    action="<?= e($formAction) ?>"
                    method="POST"
                    <?= $isEdit ? 'data-mode="edit"' : '' ?>
                >
                    <!-- En edicion se conserva el auth id del profesor para actualizarlo. -->
                    <?php if ($isEdit): ?>
                        <input
                            type="hidden"
                            name="user_id"
                            value="<?= e($coach['user_id'] ?? '') ?>"
                        >
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input
                                type="text"
                                class="form-control"
                                id="first_name"
                                name="first_name"
                                value="<?= e($coach['first_name'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input
                                type="text"
                                class="form-control"
                                id="last_name"
                                name="last_name"
                                value="<?= e($coach['last_name'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= e($coach['email'] ?? '') ?>"
                                <?= $isEdit ? 'readonly' : '' ?>
                                required
                            >

                            <?php if ($isEdit): ?>
                                <div class="form-text">
                                    El email no se modifica desde este formulario.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefono</label>
                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= e($coach['phone'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-md-12">
                            <label class="form-label d-block">Especialidades</label>
                            <div class="border rounded p-3">
                                <!-- Las especialidades del coach se guardan como relacion multiple. -->
                                <div class="row g-2">
                                    <?php foreach ($specialties as $specialty): ?>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="specialty_<?= e($specialty['id']) ?>"
                                                    name="specialty_ids[]"
                                                    value="<?= e($specialty['id']) ?>"
                                                    <?= in_array((int) $specialty['id'], $selectedSpecialtyIds, true) ? 'checked' : '' ?>
                                                >
                                                <label class="form-check-label" for="specialty_<?= e($specialty['id']) ?>">
                                                    <?= e($specialty['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button
                            type="submit"
                            class="btn btn-success"
                            id="btnGuardarCoach"
                        >
                            <?= e($buttonText) ?>
                        </button>

                        <a href="<?= $appUrl ?>/?url=admin-manage-coaches" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal comun del panel para confirmaciones del formulario. -->
        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script src="<?= $appUrl ?>/public/js/modules/admin/admin-create-coach.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
