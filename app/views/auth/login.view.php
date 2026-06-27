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

                <div class="input-icon-group" style="position:relative">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required style="padding-right:40px">
                    <button type="button" class="btn-toggle-password" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('bi-eye'); this.querySelector('i').classList.toggle('bi-eye-slash');" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0">
                        <i class="bi bi-eye" style="font-size:1.1rem;color:#6c757d"></i>
                    </button>
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

    <div class="login-image-side">
    </div>

</div>

<?php include __DIR__ . '/../users/layout/footer.php'; ?>