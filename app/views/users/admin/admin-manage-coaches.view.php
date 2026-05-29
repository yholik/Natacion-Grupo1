<div class="d-flex" style="min-height: calc(100vh - 56px);"> 
    
    <?php include_once __DIR__ . '/admin-sidebar.view.php'; ?>

     <div class="flex-grow-1 p-5 bg-white">
        <h1>Gestionar Profesores</h1>
        <hr>

        <div class="d-flex gap-2 mb-4">
            <button type="button" class="btn btn-success">
                Agregar
            </button>

            <button type="button" class="btn btn-primary">
                Editar
            </button>

            <button type="button" class="btn btn-danger">
                Dar de Baja
            </button>
        </div>

        <form>
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    Listado de Profesores
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">Sel.</th>
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
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="radio" name="coach_id" value="1">
                                    </td>
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
                                </tr>

                                <tr>
                                    <td>
                                        <input type="radio" name="coach_id" value="2">
                                    </td>
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
                                </tr>

                                <tr>
                                    <td>
                                        <input type="radio" name="coach_id" value="3">
                                    </td>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>