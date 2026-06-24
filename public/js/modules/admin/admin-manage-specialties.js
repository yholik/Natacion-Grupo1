// Maneja confirmaciones y bloqueos del ABM de especialidades.
document.addEventListener('DOMContentLoaded', () => {
    const botonesEliminar = document.querySelectorAll('.btn-eliminar-especialidad');

    botonesEliminar.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const formId = boton.dataset.formId;
            const nombre = boton.dataset.nombre;
            const coachesCount = Number(boton.dataset.coachesCount || 0);

            if (coachesCount > 0) {
                await mostrarConfirmacion(
                    `No es posible borrar la especialidad ${nombre} porque hay coaches relacionados. Primero debes dar de baja a esos coaches para borrar la relacion.`,
                    {
                        titulo: 'Especialidad en uso',
                        textoAceptar: 'Entendido',
                        claseAceptar: 'btn-primary',
                        mostrarCancelar: false
                    }
                );
                return;
            }

            const confirmado = await mostrarConfirmacion(
                `Se eliminara la especialidad ${nombre}. Desea continuar?`,
                {
                    titulo: 'Eliminar especialidad',
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
