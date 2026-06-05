<?php include __DIR__ . '/../layout/header.php'; ?>

<main>
    <div class="flex-grow-1 p-5 bg-white">
        <h1>Gestionar Profesores</h1>
        <hr>

        <div class="d-flex gap-2 mb-4">
            <a href="agregar_profesor.php" class="btn btn-success">
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
                            <tr>
                                <td>1</td>
                                <td>Juan</td>
                                <td>Pérez</td>
                                <td>juan.perez@email.com</td>
                                <td>1122334455</td>
                                <td>Natación inicial</td>
                                <td>Profesor</td>
                                <td>2026-05-15</td>
                                <td>2026-05-01</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-success">Activo</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="editar_profesor.php?id=1" class="btn btn-sm btn-primary">
                                            Editar
                                        </a>

                                        <form id="form-cambiar-estado-1" action="dar_baja_profesor.php" method="POST" class="m-0">
                                            <input type="hidden" name="coach_id" value="1">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger btn-cambiar-estado"
                                                data-form-id="form-cambiar-estado-1"
                                                data-nombre="Juan Pérez"
                                                data-accion="baja"
                                            >
                                                Dar de Baja
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>María</td>
                                <td>Gómez</td>
                                <td>maria.gomez@email.com</td>
                                <td>1155667788</td>
                                <td>Competición</td>
                                <td>Profesor</td>
                                <td>2026-05-18</td>
                                <td>2026-05-03</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-success">Activo</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="editar_profesor.php?id=2" class="btn btn-sm btn-primary">
                                            Editar
                                        </a>

                                        <form id="form-cambiar-estado-2" action="dar_baja_profesor.php" method="POST" class="m-0">
                                            <input type="hidden" name="coach_id" value="2">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger btn-cambiar-estado"
                                                data-form-id="form-cambiar-estado-2"
                                                data-nombre="María Gómez"
                                                data-accion="baja"
                                            >
                                                Dar de Baja
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>Carlos</td>
                                <td>Ramírez</td>
                                <td>carlos.ramirez@email.com</td>
                                <td>1199887766</td>
                                <td>Aquagym</td>
                                <td>Profesor</td>
                                <td>2026-05-20</td>
                                <td>2026-05-05</td>
                                <td>2026-05-25</td>
                                <td>
                                    <span class="badge bg-secondary">Baja</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="editar_profesor.php?id=3" class="btn btn-sm btn-primary">
                                            Editar
                                        </a>

                                        <form id="form-cambiar-estado-3" action="dar_alta_profesor.php" method="POST" class="m-0">
                                            <input type="hidden" name="coach_id" value="3">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success btn-cambiar-estado"
                                                data-form-id="form-cambiar-estado-3"
                                                data-nombre="Carlos Ramírez"
                                                data-accion="alta"
                                            >
                                                Dar de Alta
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const botonesCambiarEstado = document.querySelectorAll('.btn-cambiar-estado');

        botonesCambiarEstado.forEach(boton => {
            boton.addEventListener('click', async () => {
                const formId = boton.dataset.formId;
                const nombre = boton.dataset.nombre;
                const accion = boton.dataset.accion;

                const esBaja = accion === 'baja';

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

                if (!confirmado) {
                    return;
                }

                const formulario = document.getElementById(formId);

                if (formulario) {
                    formulario.submit();
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>