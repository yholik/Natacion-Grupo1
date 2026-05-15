<?php include __DIR__ . '/../users/layout/header.php';
?>

<div class='container mt-5'>
    <div class='row justify-content-center'>
        <div class='col-md-5'>

            <div class='card shadow-sm border-0'>
                <div class='card-body p-4'>

                    <h4 class='text-center mb-4'>
                        <i class='bi bi-key'></i> Nueva contraseña
                    </h4>

                    <p class='text-muted text-center small mb-4'>
                        Estás a un paso de recuperar tu acceso. Elegí una contraseña segura.
                    </p>

                    <form id='formResetPassword' action='?url=update-password' method='POST'>

                        <input type='hidden' name='token' value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                        <div class='mb-3'>
                            <label class='form-label'>Nueva contraseña</label>
                            <input type='password' name='password' class='form-control'
                                placeholder='Mínimo 6 caracteres' minlength='6' required>
                        </div>

                        <div class='mb-3'>
                            <label class='form-label'>Confirmar contraseña</label>
                            <input type='password' name='confirm_password' class='form-control'
                                placeholder='Repetí tu contraseña' required>
                        </div>

                        <button type='submit' class='btn btn-success w-100 py-2'>
                            Actualizar contraseña
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../users/layout/footer.php';
?>