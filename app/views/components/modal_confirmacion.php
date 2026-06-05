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
    function mostrarConfirmacion(mensaje, opciones = {}) {
        return new Promise((resolve) => {
            const modalElemento = document.getElementById('modalConfirmacionGenerico');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElemento);

            const titulo = document.getElementById('modalConfirmacionGenericoLabel');
            const mensajeElemento = document.getElementById('modalConfirmacionMensaje');
            const btnAceptar = document.getElementById('modalConfirmacionAceptar');
            const btnCancelar = document.getElementById('modalConfirmacionCancelar');
            const btnCerrar = document.getElementById('modalConfirmacionCerrar');

            titulo.textContent = opciones.titulo || 'Confirmar acción';
            mensajeElemento.textContent = mensaje;

            btnAceptar.textContent = opciones.textoAceptar || 'Aceptar';
            btnCancelar.textContent = opciones.textoCancelar || 'Cancelar';

            btnAceptar.className = 'btn';
            btnAceptar.classList.add(opciones.claseAceptar || 'btn-primary');

            let resuelto = false;

            function finalizar(resultado) {
                if (resuelto) {
                    return;
                }

                resuelto = true;
                limpiarEventos();
                modal.hide();
                resolve(resultado);
            }

            function aceptar() {
                finalizar(true);
            }

            function cancelar() {
                finalizar(false);
            }

            function cerrarPorFondo() {
                finalizar(false);
            }

            function limpiarEventos() {
                btnAceptar.removeEventListener('click', aceptar);
                btnCancelar.removeEventListener('click', cancelar);
                btnCerrar.removeEventListener('click', cancelar);
                modalElemento.removeEventListener('hidden.bs.modal', cerrarPorFondo);
            }

            btnAceptar.addEventListener('click', aceptar);
            btnCancelar.addEventListener('click', cancelar);
            btnCerrar.addEventListener('click', cancelar);
            modalElemento.addEventListener('hidden.bs.modal', cerrarPorFondo);

            modal.show();
        });
    }
</script>