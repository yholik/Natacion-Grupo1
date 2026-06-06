<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
// Si el controlador no envía $coaches, se usa un array vacío para evitar errores.
$coaches = $coaches ?? [];

/**
 * Escapa valores antes de imprimirlos en HTML.
 * Evita problemas de XSS si algún dato viene con caracteres especiales.
 */
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatea una fecha.
 * Si la fecha viene vacía o NULL, muestra "-".
 */
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
            <!-- Lleva al formulario de creación de profesor -->
            <a href="<?= e(Env::get('APP_URL')) ?>/?url=admin-create-coach" class="btn btn-success">
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
                            <!-- Si no hay profesores, se muestra una fila informativa -->
                            <?php if (empty($coaches)): ?>
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        No hay profesores registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Recorre los profesores enviados desde el controlador -->
                            <?php foreach ($coaches as $coach): ?>
                                <?php
                                // user_id es el ID de la cuenta en auth.
                                $userId = $coach['user_id'];

                                // Nombre completo usado en el mensaje del modal.
                                $fullName = trim($coach['first_name'] . ' ' . $coach['last_name']);

                                // Si deleted_at está vacío, el profesor está activo.
                                $isActive = empty($coach['deleted_at']);

                                // ID único del formulario de esta fila.
                                // Se usa luego desde JavaScript para enviar el formulario correcto.
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
                                            <a href="<?= e(Env::get('APP_URL')) ?>/?url=admin-edit-coach&id=<?= e($userId) ?>" class="btn btn-sm btn-primary">
                                                Editar
                                            </a>

                                            <!--
                                                Formulario real de alta/baja.
                                                No se envía directamente al tocar el botón.
                                                Primero el JS muestra el modal de confirmación.
                                            -->
                                            <form
                                                id="<?= e($formId) ?>"
                                                action="<?= e(Env::get('APP_URL')) ?>/?url=<?= $isActive ? 'admin-deactivate-coach' : 'admin-activate-coach' ?>"
                                                method="POST"
                                                class="m-0"
                                            >
                                                <input type="hidden" name="coach_id" value="<?= e($userId) ?>">

                                                <!--
                                                    data-form-id: identifica qué formulario debe enviarse.
                                                    data-nombre: nombre mostrado en el modal.
                                                    data-accion: define si el mensaje será alta o baja.
                                                -->
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

        <!-- Modal genérico reutilizable -->
        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Busca todos los botones que cambian el estado del profesor.
        const botonesCambiarEstado = document.querySelectorAll('.btn-cambiar-estado');

        botonesCambiarEstado.forEach(boton => {
            boton.addEventListener('click', async () => {
                // Obtiene datos cargados en el botón mediante data-*.
                const formId = boton.dataset.formId;
                const nombre = boton.dataset.nombre;
                const accion = boton.dataset.accion;

                // Determina si la acción es baja o alta.
                const esBaja = accion === 'baja';

                // Muestra el modal genérico y espera la respuesta del usuario.
                // true  = aceptó
                // false = canceló
                const confirmado = await mostrarConfirmacion(
                    esBaja
                        ? `Se dará de baja el siguiente usuario: ${nombre}`
                        : `Se dará de alta el siguiente usuario: ${nombre}`,
                    {
                        titulo: esBaja ? 'Dar de baja usuario' : 'Dar de alta usuario',
                        textoAceptar: esBaja ? 'Dar de Baja' : 'Dar de Alta',
                        textoCancelar: 'Cancelar',
                        claseAceptar: esBaja ? 'btn-danger' : 'btn-success'
                    }
                );

                // Si canceló, no se envía nada.
                if (!confirmado) {
                    return;
                }

                // Busca el formulario correspondiente a la fila.
                const formulario = document.getElementById(formId);

                // Si existe, lo envía por POST al controlador.
                if (formulario) {
                    formulario.submit();
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>