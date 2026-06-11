<?php include __DIR__ . '/../users/layout/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/css/auth-vistas.css">

<div class="login-split-card forgot-split-card">
    
    <div class="login-form-side forgot-form-side">
        <div class="login-form-content">
            
            <div class="brand-text-wrapper">
                <span class="brand-title-text">Club de Natación - El Delfín Saltarín</span>
                <span class="brand-flag">🚩</span>
            </div>

            <h2 class="welcome-title">Recuperar contraseña</h2>
            <p class="welcome-subtitle">Ingresá tu correo electrónico y te enviaremos un enlace para que puedas generar una nueva contraseña.</p>

            <form id="formForgotPassword" action="?url=send-reset" method="POST">
                
                <div class="input-icon-group">
                    <i class="bi bi-envelope form-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
                </div>

                <button type="submit" class="btn-login-submit">Enviar enlace de recuperación</button>
            </form>

            <div class="auth-footer-links">
                <div class="secondary-link-container">
                    <a href="?url=login" class="register-link">
                        <i class="bi bi-arrow-left"></i> Volver al login
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="login-image-side forgot-image-side"></div>

</div>

<?php include __DIR__ . '/../users/layout/footer.php'; ?>