document.addEventListener('DOMContentLoaded', () => {
    const botonesCambiarEstado = document.querySelectorAll('.btn-cambiar-estado');

    botonesCambiarEstado.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const formId = boton.dataset.formId;
            const nombre = boton.dataset.nombre;
            const accion = boton.dataset.accion;

            const esBaja = accion === 'baja';

            const confirmado = await mostrarConfirmacion(
                esBaja
                    ? `Se dará de baja el siguiente usuario: ${nombre}`
                    : `Se dará de alta el siguiente usuario: ${nombre}`,
                {
                    titulo: esBaja ? 'Dar de baja usuario' : 'Dar de alta usuario',
                    textoAceptar: esBaja ? 'Dar de Baja' : 'Dar de Alta',
                    textoCancelar: 'Cancelar',
                    claseAceptar: esBaja ? 'btn-danger' : 'btn-success'
                }
            );

            if (!confirmado) {
                return;
            }

            const formulario = document.getElementById(formId);

            if (formulario) {
                formulario.submit();
            }
        });
    });
});