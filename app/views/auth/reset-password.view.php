<?php include __DIR__ . '/../../layout/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/css/auth-vistas.css">

<div class="login-split-card reset-center-card" style="max-width: 480px; min-height: auto; margin: 40px auto;">
    
    <div class="login-form-side" style="flex: 1; padding: 45px 40px;">
        <div class="login-form-content" style="max-width: 100%;">
            
            <div class="brand-text-wrapper" style="justify-content: center; margin-bottom: 25px;">
                <span class="brand-title-text">Club de Natación - El Delfín Saltarín</span>
                <span class="brand-flag">🚩</span>
            </div>

            <h2 class="welcome-title text-center" style="font-size: 24px;">Nueva contraseña</h2>
            <p class="welcome-subtitle text-center" style="margin-bottom: 25px;">
                Estás a un paso de recuperar tu acceso. Elegí una contraseña segura.
            </p>

            <form id="formResetPassword" action="?url=update-password" method="POST">

                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                
                <div class="input-icon-group">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                </div>

                <div class="input-icon-group" style="margin-bottom: 25px;">
                    <i class="bi bi-shield-lock form-icon"></i>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Repetir contraseña" required>
                </div>

                <button type="submit" class="btn-login-submit">Actualizar contraseña</button>
            </form>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
