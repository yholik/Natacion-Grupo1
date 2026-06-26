<?php include __DIR__ . '/../users/layout/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= Env::get('ASSET_URL') ?>/css/auth-vistas.css">

<div class="login-split-card flex-grow-1">
    
    <div class="login-form-side">
        <div class="login-form-content">
            
            <div class="brand-text-wrapper">
                <span class="brand-title-text">Club de Natación - El Delfín Saltarín</span>
                <span class="brand-flag">🚩</span>
            </div>

            <h2 class="welcome-title">Bienvenido</h2>
            <p class="welcome-subtitle">Iniciá sesión para continuar</p>

            <form id="formLogin" method="POST">
                
                <div class="input-icon-group">
                    <i class="bi bi-person form-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                </div>

                <div class="input-icon-group">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>

                <button type="submit" class="btn-login-submit">Ingresar</button>
            </form>

            <div class="auth-footer-links">
                <a href="?url=forgot-password" class="forgot-link">¿Olvidaste tu contraseña?</a>
                
                <div class="secondary-link-container">
                    <span class="no-account-text">¿No tenés cuenta? </span>
                    <a href="?url=register" class="register-link">Registrate aquí</a>
                </div>
            </div>

        </div>
    </div>

    <div class="login-image-side"></div>

</div>

<?php include __DIR__ . '/../users/layout/footer.php'; ?>