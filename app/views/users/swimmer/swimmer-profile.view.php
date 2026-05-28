<?php include __DIR__ . '/../layout/header.php'; ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mis Datos Personales</h5>
                </div>
                <div class="card-body">
                    <form id="formUpdateProfile">
                        <div class="text-center mb-3">
                            <img src="<?= rtrim(Env::get('ASSET_URL'), '/') ?>/img/uploads/profiles/swimmers/<?= htmlspecialchars($swimmer['profile_image'] ?? 'default-profile.png') ?>"
                                alt="Foto" class="rounded-circle" style="width:100px;height:100px;object-fit:cover">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="first_name" class="form-control"
                                value="<?= htmlspecialchars($swimmer['first_name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="last_name" class="form-control"
                                value="<?= htmlspecialchars($swimmer['last_name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control"
                                value="<?= htmlspecialchars($swimmer['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control"
                                value="<?= htmlspecialchars($swimmer['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="birth_date" class="form-control"
                                value="<?= htmlspecialchars($swimmer['birth_date'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto de Perfil</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('formUpdateProfile')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resp = await fetch('?url=swimmer/update-profile', { method: 'POST', body: formData });
        const data = await resp.json();
        if (data.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Perfil actualizado' }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: data.message });
        }
    });
    </script>
<?php include __DIR__ . '/../layout/footer.php'; ?>