<?php include __DIR__ . '/../users/layout/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/css/auth-vistas.css">

<div class="login-split-card register-split-card">
    
    <div class="login-form-side register-form-side">
        <div class="login-form-content register-form-content">
            
            <div class="brand-text-wrapper">
                <span class="brand-title-text">Club de Natación - El Delfín Saltarín</span>
                <span class="brand-flag">🚩</span>
            </div>

            <h2 class="welcome-title"><?php echo $title ?? 'Inscripción de Alumnos'; ?></h2>
            <p class="welcome-subtitle">Completá tus datos para unirte al club</p>

            <form id="formRegister" action="?url=register" method="POST" enctype="multipart/form-data">
                
                <div class="form-grid">
                    <div class="input-icon-group">
                        <i class="bi bi-person form-icon"></i>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Nombre" required>
                    </div>

                    <div class="input-icon-group">
                        <i class="bi bi-person-badge form-icon"></i>
                        <input type="text" name="apellido" class="form-control" placeholder="Ej: Apellido" required>
                    </div>

                    <div class="input-icon-group">
                        <i class="bi bi-telephone form-icon"></i>
                        <input type="text" name="telefono" class="form-control" placeholder="1112345678">
                    </div>

                    <div class="input-icon-group">
                        <i class="bi bi-calendar-event form-icon"></i>
                        <input type="date" name="cumple" class="form-control" required>
                    </div>

                    <div class="input-icon-group full-width">
                        <i class="bi bi-envelope form-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>

                    <div class="input-icon-group">
                        <i class="bi bi-lock form-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="******" required>
                    </div>

                    <div class="input-icon-group">
                        <i class="bi bi-shield-lock form-icon"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Repetir contraseña" required>
                    </div>

                    <div class="input-icon-group full-width">
                        <label class="file-custom-label">Foto de Perfil</label>
                        <i class="bi bi-camera form-icon file-icon-adjust"></i>
                        <input type="file" name="profile_image" class="form-control form-control-file" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn-login-submit">Crear Cuenta</button>
            </form>

            <div class="auth-footer-links">
                <div class="secondary-link-container">
                    <span class="no-account-text">¿Ya tienes cuenta? </span>
                    <a href="?url=login" class="register-link">Ingresa aquí</a>
                </div>
            </div>

        </div>
    </div>

    <div class="login-image-side register-image-side"></div>

</div>

<?php include __DIR__ . '/../users/layout/footer.php'; ?>