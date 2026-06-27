import { initCropper } from '../cropperMain.js';

export function initCoachProfile(){
      const form = document.getElementById("updateProfileForm");
    if (!form) return;

const profileFields = document.querySelectorAll('#updateProfileForm input, #updateProfileForm select');
const passwordFields = document.querySelectorAll('#updatePasswordForm input');


    //profile
const btnCancelProfile = document.getElementById('btnCancelProfile');
const btnEditProfile = document.getElementById('btnEditProfile');
const btnSaveProfile = document.getElementById('btnSaveProfile');


    //password
const btnCancelPassword = document.getElementById('btnCancelPassword');
const btnEditPassword = document.getElementById('btnEditPassword');
const btnSavePassword = document.getElementById('btnSavePassword');

let profileCropper = null;




function toogleBlockMode(fields, isEditing, btnCancel, btnEdit, btnSave){
    fields.forEach(field => {
        if(field.id !== 'emailField'){
            if (isEditing) {
                field.removeAttribute('disabled');  // editar: habilita
            } else {
                field.setAttribute('disabled', true); // cancelar: deshabilita
            }
        }
    });

    if (isEditing) {
        btnEdit?.classList.add('d-none');
        btnSave?.classList.remove('d-none');
        btnCancel?.classList.remove('d-none');
    } else {
        btnEdit?.classList.remove('d-none');
        btnSave?.classList.add('d-none');
        btnCancel?.classList.add('d-none');
    }   
}



async function handleFormSubmit(e, url){
e.preventDefault();
const formData = new FormData(e.target);

if (profileCropper) {
    const croppedFile = profileCropper.getCroppedFile();
    if (croppedFile) {
        formData.set('profile_image', croppedFile, croppedFile.name);
    }
}

try{
    const resp = await fetch(url, { method: 'POST', body: formData });
    const data = await resp.json();

if (data.status === 'success') {
        Swal.fire({ icon: 'success', title: 'Perfil actualizado' }).then(() => location.reload());
    } else {
        Swal.fire({ icon: 'error', title: data.message });
    }
}catch{
Swal.fire({icon: 'error', title: "Error de conexion", text: 'Intentalo nuevamente mas tarde'});
}

}



//eventos profile
btnEditProfile?.addEventListener('click', () => {
    toogleBlockMode(profileFields, true, btnCancelProfile, btnEditProfile, btnSaveProfile);
});

btnCancelProfile?.addEventListener('click', () => {
    document.getElementById('updateProfileForm').reset();
    toogleBlockMode(profileFields, false, btnCancelProfile, btnEditProfile, btnSaveProfile);
});


//eventos password
btnEditPassword?.addEventListener('click', () => {
    toogleBlockMode(passwordFields, true, btnCancelPassword, btnEditPassword, btnSavePassword);
});

btnCancelPassword?.addEventListener('click', () => {
    document.getElementById('updatePasswordForm').reset();
    toogleBlockMode(passwordFields, false, btnCancelPassword, btnEditPassword, btnSavePassword);
});

//envio de form profile
document.getElementById('updateProfileForm')?.addEventListener('submit', (e) => {
    handleFormSubmit(e, '?url=coach-update-profile');
});

//envio de form passw
document.getElementById('updatePasswordForm')?.addEventListener('submit', (e) => {
    handleFormSubmit(e, '?url=update-profile-credentials');
});

// Inicializar cropper para foto de perfil
const fileInput = form.querySelector('input[name="profile_image"]');
if (fileInput && typeof initCropper !== 'undefined') {
    profileCropper = initCropper(fileInput, { aspectRatio: 1 });
}

}
