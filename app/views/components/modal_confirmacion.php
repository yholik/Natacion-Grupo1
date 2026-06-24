<!-- Modal reutilizable para confirmar acciones del panel. -->
<div class="modal fade" id="modalConfirmacionGenerico" tabindex="-1" aria-labelledby="modalConfirmacionGenericoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalConfirmacionGenericoLabel">
                    Confirmar acción
                </h5>

                <button type="button" class="btn-close btn-close-white" id="modalConfirmacionCerrar" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0 fs-5" id="modalConfirmacionMensaje"></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalConfirmacionCancelar">
                    Cancelar
                </button>

                <button type="button" class="btn btn-primary" id="modalConfirmacionAceptar">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Muestra un modal Bootstrap reutilizable.
     *
     * Devuelve una Promise:
     * true  = el usuario aceptó
     * false = el usuario canceló o cerró el modal
     */
    function mostrarConfirmacion(mensaje, opciones = {}) {
        return new Promise((resolve) => {
            // Elemento principal del modal.
            const modalElemento = document.getElementById('modalConfirmacionGenerico');

            // Obtiene o crea una instancia Bootstrap del modal.
            const modal = bootstrap.Modal.getOrCreateInstance(modalElemento);

            // Elementos internos del modal.
            const titulo = document.getElementById('modalConfirmacionGenericoLabel');
            const mensajeElemento = document.getElementById('modalConfirmacionMensaje');
            const btnAceptar = document.getElementById('modalConfirmacionAceptar');
            const btnCancelar = document.getElementById('modalConfirmacionCancelar');
            const btnCerrar = document.getElementById('modalConfirmacionCerrar');

            // Configura el contenido visible del modal.
            titulo.textContent = opciones.titulo || 'Confirmar acción';
            mensajeElemento.textContent = mensaje;

            btnAceptar.textContent = opciones.textoAceptar || 'Aceptar';
            btnCancelar.textContent = opciones.textoCancelar || 'Cancelar';

            // Reinicia las clases del botón aceptar.
            btnAceptar.className = 'btn';

            // Aplica el color solicitado.
            btnAceptar.classList.add(opciones.claseAceptar || 'btn-primary');

            // Permite ocultar el botón cancelar cuando se usa como mensaje informativo.
            if (opciones.mostrarCancelar === false) {
                btnCancelar.classList.add('d-none');
            } else {
                btnCancelar.classList.remove('d-none');
            }

            // Evita resolver la Promise más de una vez.
            let resuelto = false;

            /**
             * Cierra el modal y devuelve true o false.
             */
            function finalizar(resultado) {
                if (resuelto) {
                    return;
                }

                resuelto = true;
                limpiarEventos();
                modal.hide();
                resolve(resultado);
            }

            // Acciones posibles del usuario.
            function aceptar() {
                finalizar(true);
            }

            function cancelar() {
                finalizar(false);
            }

            function cerrarPorFondo() {
                finalizar(false);
            }

            /**
             * Limpia eventos para evitar que se acumulen
             * cada vez que se vuelve a abrir el modal.
             */
            function limpiarEventos() {
                btnAceptar.removeEventListener('click', aceptar);
                btnCancelar.removeEventListener('click', cancelar);
                btnCerrar.removeEventListener('click', cancelar);
                modalElemento.removeEventListener('hidden.bs.modal', cerrarPorFondo);
            }

            // Asigna eventos a los botones del modal.
            btnAceptar.addEventListener('click', aceptar);
            btnCancelar.addEventListener('click', cancelar);
            btnCerrar.addEventListener('click', cancelar);

            // Si el usuario cierra tocando fuera del modal o con ESC, se toma como cancelar.
            modalElemento.addEventListener('hidden.bs.modal', cerrarPorFondo);

            // Muestra el modal.
            modal.show();
        });
    }
</script>
