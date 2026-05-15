<?php include __DIR__ . '/../users/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="text-center mb-4">
                        <i class="bi bi-shield-lock"></i> Recuperar contraseña
                    </h4>

                    <p class="text-muted text-center small mb-4">
                        Ingresá tu correo electrónico y te enviaremos un enlace para que puedas generar una nueva
                        contraseña.
                    </p>

                    <form id="formForgotPassword" action="?url=send-reset" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Email registrado</label>
                            <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Enviar enlace de recuperación
                        </button>

                    </form>

                    <div class="text-center mt-4">
                        <a href="?url=login" class="text-decoration-none small">
                            <i class="bi bi-arrow-left"></i> Volver al login
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?php include __DIR__ . '/../users/layout/footer.php'; ?>