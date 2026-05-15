<?php include __DIR__ . '/..//users/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Iniciar Sesión</h3>
                    <form id="formLogin">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Entrar</button>
                    </form>

                    <hr>
                    <div class="mt-3 text-center">
                        <p class="mb-1">¿No tienes cuenta? <a href="?url=register">Regístrate aquí</a></p>
                        <a href="?url=forgot-password" class="text-muted small">Olvidé mi contraseña</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include __DIR__ . '/../users/layout/footer.php'; ?>