// Maneja el alta y edición de profesores desde admin.
document.addEventListener('DOMContentLoaded', () => {
    const formCrearCoach = document.getElementById('formCrearCoach');
    const btnGuardarCoach = document.getElementById('btnGuardarCoach');

    if (!formCrearCoach || !btnGuardarCoach) {
        return;
    }

    formCrearCoach.addEventListener('submit', async (event) => {
        event.preventDefault();

        const esEdicion = formCrearCoach.dataset.mode === 'edit';

        btnGuardarCoach.disabled = true;
        btnGuardarCoach.textContent = esEdicion ? 'Actualizando...' : 'Guardando...';

        try {
            const formData = new FormData(formCrearCoach);

            const response = await fetch(formCrearCoach.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            const rawResponse = await response.text();

            if (!contentType.includes('application/json')) {
                console.error('Respuesta no JSON del servidor:', rawResponse);

                await mostrarConfirmacion(
                    esEdicion
                        ? 'No se pudo actualizar el profesor. El servidor devolvió una respuesta inválida.'
                        : 'No se pudo crear el profesor. El servidor devolvió una respuesta inválida.',
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
                        titulo: esEdicion ? 'Profesor actualizado' : 'Profesor creado',
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
                message || (esEdicion ? 'No se pudo actualizar el profesor.' : 'No se pudo crear el profesor.'),
                {
                    titulo: esEdicion ? 'No se pudo actualizar' : 'No se pudo crear',
                    textoAceptar: 'Aceptar',
                    claseAceptar: 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } catch (error) {
            console.error(error);

            await mostrarConfirmacion(
                esEdicion
                    ? 'No se pudo actualizar el profesor. Revisá la conexión, la ruta o el error del servidor.'
                    : 'No se pudo crear el profesor. Revisá la conexión, la ruta o el error del servidor.',
                {
                    titulo: 'Error',
                    textoAceptar: 'Aceptar',
                    claseAceptar: 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } finally {
            btnGuardarCoach.disabled = false;
            btnGuardarCoach.textContent = esEdicion ? 'Actualizar' : 'Guardar';
        }
    });
});
