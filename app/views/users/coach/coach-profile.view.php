
<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="d-flex flex-column flex-lg-row w-100">

 <div class="d-flex">
<aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
       
       <?php include __DIR__ . '/../layout/side-bar.php'; ?>
</aside>
</div>

<div class="p-4 w-100">
    <h2 class="mb-4">Mis datos personales</h2>

    <form id="updateProfileForm" class="w-100">
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
                <label class="form-label">Teléfono</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($coach['phone'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Especialidad</label>
                <input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($coach['specialty'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($coach['email'] ?? '') ?>" disabled>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btnEdit">Editar</button>
            <button type="submit" class="btn btn-primary d-none" id="btnSave">Guardar</button>
        </div>
    </form>
</div>

</main>

<script>
const btnEdit = document.getElementById('btnEdit');
const btnSave = document.getElementById('btnSave');
const inputs  = document.querySelectorAll('#updateProfileForm input');

btnEdit.addEventListener('click', function() {
    inputs.forEach(input => input.removeAttribute('disabled'));
    btnEdit.classList.add('d-none');
    btnSave.classList.remove('d-none');
});

btnSave.addEventListener('click', function() {
    
});

document.getElementById('updateProfileForm')?.addEventListener('submit', async function(e){
     e.preventDefault(); 

     const formData = new FormData(this);    
     const resp = await fetch('?url=coach-update-profile', { method: 'POST', body: formData });
     const data = await resp.json();



    if(data.status === 'success') {
        inputs.forEach(input => input.removeAttribute('disabled', true));
        btnEdit.classList.add('d-none');
        btnSave.classList.remove('d-none');
        Swal.fire({ icon: 'success', title: 'Perfil actualizado' }).then(() => location.reload());
    } else {
        Swal.fire({ icon: 'error', title: data.message });
}
});
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>