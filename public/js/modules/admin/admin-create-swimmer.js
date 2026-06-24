// Maneja el alta y edición de nadadores desde admin.
import { initCropper } from "../cropperMain.js";

document.addEventListener('DOMContentLoaded', () => {
    const formCrearSwimmer = document.getElementById('formCrearSwimmer');
    const btnGuardarSwimmer = document.getElementById('btnGuardarSwimmer');

    if (!formCrearSwimmer || !btnGuardarSwimmer) {
        return;
    }

    const fileInput = formCrearSwimmer.querySelector('input[name="profile_image"]');
    const cropper = fileInput ? initCropper(fileInput, { aspectRatio: 1 }) : null;

    formCrearSwimmer.addEventListener('submit', async (event) => {
        event.preventDefault();

        const esEdicion = formCrearSwimmer.dataset.mode === 'edit';

        btnGuardarSwimmer.disabled = true;
        btnGuardarSwimmer.textContent = esEdicion ? 'Actualizando...' : 'Guardando...';

        try {
            const formData = new FormData(formCrearSwimmer);

            if (cropper) {
                const croppedFile = cropper.getCroppedFile();
                if (croppedFile) {
                    formData.set('profile_image', croppedFile, croppedFile.name);
                }
            }

            const response = await fetch(formCrearSwimmer.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            const rawResponse = await response.text();

            if (!contentType.includes('application/json')) {
                await mostrarConfirmacion(
                    esEdicion
                        ? 'No se pudo actualizar el nadador. El servidor devolvió una respuesta inválida.'
                        : 'No se pudo crear el nadador. El servidor devolvió una respuesta inválida.',
                    {
                        titulo: 'Error',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

                return;
            }

            const result = JSON.parse(rawResponse);
            const status = result.status || result.type || '';
            const message = result.message || 'Operación finalizada.';
            const redirectUrl = result.redirect || result.url || null;

            if (status === 'success') {
                await mostrarConfirmacion(
                    message,
                    {
                        titulo: esEdicion ? 'Nadador actualizado' : 'Nadador creado',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-success',
                        mostrarCancelar: false
                    }
                );

                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }

                return;
            }

            await mostrarConfirmacion(
                message || (esEdicion ? 'No se pudo actualizar el nadador.' : 'No se pudo crear el nadador.'),
                {
                    titulo: esEdicion ? 'No se pudo actualizar' : 'No se pudo crear',
                    textoAceptar: 'Aceptar',
                    claseAceptar: status === 'warning' ? 'btn-warning' : 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } catch (error) {
            console.error(error);

            await mostrarConfirmacion(
                esEdicion
                    ? 'No se pudo actualizar el nadador. Revisá la conexión, la ruta o el error del servidor.'
                    : 'No se pudo crear el nadador. Revisá la conexión, la ruta o el error del servidor.',
                {
                    titulo: 'Error',
                    textoAceptar: 'Aceptar',
                    claseAceptar: 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } finally {
            btnGuardarSwimmer.disabled = false;
            btnGuardarSwimmer.textContent = esEdicion ? 'Actualizar' : 'Guardar';
        }
    });
});
