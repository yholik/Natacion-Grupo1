// Activa la confirmación antes de borrar clases.
document.addEventListener('DOMContentLoaded', () => {
    const botonesEliminarClase = document.querySelectorAll('.btn-eliminar-clase');

    botonesEliminarClase.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const formId = boton.dataset.formId;
            const clase = boton.dataset.clase;
            const profesor = boton.dataset.profesor;

            const confirmado = await mostrarConfirmacion(
                `Se eliminará la clase: ${clase} del profesor ${profesor}.`,
                {
                    titulo: 'Eliminar clase',
                    textoAceptar: 'Eliminar',
                    textoCancelar: 'Cancelar',
                    claseAceptar: 'btn-danger'
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
