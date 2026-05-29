<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-center"><?php echo $title ?? 'Registro de Swimmer'; ?></h4>
                </div>
                <div class="card-body">
                    <form id="formRegister" action="?url=register" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" placeholder="Ej: Pérez"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha de cumpleaños</label>
                                    <input type="date" name="cumple" class="form-control" placeholder="Ej: 24/07/1995"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control" placeholder="juan@correo.com"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Mín. 6 caracteres" required>
                                    </div>
                                    <div class="mb-3">
                                         <label class="form-label">Repetir Contraseña</label>
                                    <input type="password" name="confirm_password" class="form-control"
                                        placeholder="Mín. 6 caracteres" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" placeholder="11 1234 5678">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" name="birth_date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Foto de Perfil</label>
                                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary px-5">Crear Cuenta</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="?url=login" class="text-decoration-none">¿Ya tienes cuenta? Ingresa aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>