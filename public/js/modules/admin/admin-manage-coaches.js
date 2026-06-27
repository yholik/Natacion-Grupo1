// Activa las confirmaciones del listado de profesores.
document.addEventListener('DOMContentLoaded', () => {
    const botonesCambiarEstado = document.querySelectorAll('.btn-cambiar-estado');
    const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';

    botonesCambiarEstado.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const formId = boton.dataset.formId;
            const nombre = boton.dataset.nombre;
            const accion = boton.dataset.accion;
            const especialidades = boton.dataset.especialidades || '';
            const userId = boton.dataset.userId || '';

            const esBaja = accion === 'baja';
            const mensaje = esBaja
                ? `Se dara de baja el siguiente profesor: ${nombre}. Tambien se eliminara la relacion con sus especialidades${especialidades ? ` (${especialidades})` : ''}. Desea continuar?`
                : `Se dara de alta el siguiente usuario: ${nombre}`;

            const confirmado = await mostrarConfirmacion(mensaje, {
                titulo: esBaja ? 'Dar de baja profesor' : 'Dar de alta usuario',
                textoAceptar: esBaja ? 'Dar de Baja' : 'Dar de Alta',
                textoCancelar: 'Cancelar',
                claseAceptar: esBaja ? 'btn-danger' : 'btn-success'
            });

            if (!confirmado) {
                return;
            }

            if (esBaja && userId) {
                try {
                    const formData = new FormData();
                    formData.append('user_id', userId);

                    const response = await fetch(APP_URL + '/?url=admin-deactivate-coach', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        await mostrarConfirmacion(result.message || 'Profesor dado de baja correctamente.', {
                            titulo: 'Baja exitosa',
                            textoAceptar: 'Aceptar',
                            claseAceptar: 'btn-success',
                            mostrarCancelar: false
                        });
                        window.location.reload();
                    } else {
                        await mostrarConfirmacion(result.message || 'No se pudo dar de baja el profesor.', {
                            titulo: 'No se pudo dar de baja',
                            textoAceptar: 'Entendido',
                            claseAceptar: 'btn-danger',
                            mostrarCancelar: false
                        });
                    }
                } catch (err) {
                    await mostrarConfirmacion('Error de conexión al servidor.', {
                        titulo: 'Error',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    });
                }
                return;
            }

            const formulario = document.getElementById(formId);

            if (formulario) {
                formulario.submit();
            }
        });
    });
});
