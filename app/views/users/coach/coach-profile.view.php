<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="<?= Env::get('ASSET_URL') ?>/css/coach.css" rel="stylesheet">
<?php
$specialties = $specialties ?? [];
$selectedSpecialtyIds = array_map('intval', $coach['specialty_ids'] ?? []);
?>

<main class="d-flex flex-column flex-lg-row w-100">

 <div class="d-flex">
<aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
       
       <?php include __DIR__ . '/../layout/side-bar.php'; ?>
</aside>
</div>

<!--CONTENIDO PRINCIPAL-->
<div class="coach-content flex-grow-1">
    <div class="coach-welcome">
    <h2 class="coach-title">Mis datos personales</h2>
        <p class="coach-subtitle">Actualiza datos personales desde este panel</p>
    </div>
    <form id="updateProfileForm" class="profile-card">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($coach['first_name'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($coach['last_name'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Telefono</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($coach['phone'] ?? '') ?>" disabled>
            </div>            

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="emailField" value="<?= htmlspecialchars($coach['email'] ?? '') ?>" disabled>
            </div>
           
        
        <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-secondary d-none" id="btnCancelProfile">Cancelar</button>
            <button type="button" class="btn btn-secondary" id="btnEditProfile">Editar</button>
            <button type="submit" class="btn btn-primary d-none" id="btnSaveProfile">Guardar</button>
        </div>
    </form>
</div>

<!--BLOQUE ACTUALIZACION DE CONTRASEÑA-->
    <div class="coach-welcome">
        <h2 class="coach-title">Actualización de contraseña</h2>
        <p class="coach-subtitle">Actualiza tu contraseña de acceso desde aqui</p>
    </div>

<form id="updatePasswordForm" class="profile-card">
<div class="row g-3">
<div class="col-md-6" style="position:relative">
    <label class="form-label">Contraseña actual</label>
    <input type="password" name="current_password" class="form-control" disabled style="padding-right:40px">
    <button type="button" class="btn-toggle-password" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('bi-eye'); this.querySelector('i').classList.toggle('bi-eye-slash');" style="position:absolute;right:8px;top:38px;background:none;border:none;cursor:pointer;padding:0">
        <i class="bi bi-eye" style="font-size:1.1rem;color:#6c757d"></i>
    </button>
</div>


<div class="col-md-6" style="position:relative">
    <label class="form-label">Nueva contraseña</label>
    <input type="password" name="new_password" class="form-control" disabled style="padding-right:40px">
    <button type="button" class="btn-toggle-password" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('bi-eye'); this.querySelector('i').classList.toggle('bi-eye-slash');" style="position:absolute;right:8px;top:38px;background:none;border:none;cursor:pointer;padding:0">
        <i class="bi bi-eye" style="font-size:1.1rem;color:#6c757d"></i>
    </button>
</div>


<div class="col-md-6" style="position:relative">
    <label class="form-label">Confirmar nueva contraseña</label>
    <input type="password" name="confirm_password" class="form-control" disabled style="padding-right:40px">
    <button type="button" class="btn-toggle-password" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('bi-eye'); this.querySelector('i').classList.toggle('bi-eye-slash');" style="position:absolute;right:8px;top:38px;background:none;border:none;cursor:pointer;padding:0">
        <i class="bi bi-eye" style="font-size:1.1rem;color:#6c757d"></i>
    </button>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="button" class="btn btn-secondary d-none" id="btnCancelPassword">Cancelar</button>
            <button type="button" class="btn btn-secondary" id="btnEditPassword">Editar</button>
            <button type="submit" class="btn btn-primary d-none" id="btnSavePassword">Guardar</button>
        </div>
</div>
</form>

</div>
</main>
<script type="module" src="<?= Env::get('ASSET_URL') ?>/js/modules/coachMain.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
