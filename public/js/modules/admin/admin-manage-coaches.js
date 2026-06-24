// Activa las confirmaciones del listado de profesores.
document.addEventListener('DOMContentLoaded', () => {
    const botonesCambiarEstado = document.querySelectorAll('.btn-cambiar-estado');

    botonesCambiarEstado.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const formId = boton.dataset.formId;
            const nombre = boton.dataset.nombre;
            const accion = boton.dataset.accion;
            const especialidades = boton.dataset.especialidades || '';

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

            const formulario = document.getElementById(formId);

            if (formulario) {
                formulario.submit();
            }
        });
    });
});
