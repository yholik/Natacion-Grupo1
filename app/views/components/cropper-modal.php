<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recortar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="img-container" style="max-height: 400px; overflow: hidden;">
                    <img id="cropperImage" src="" alt="Imagen a recortar">
                </div>
                <p class="text-muted small mt-2 mb-0 text-center">Arrastra las esquinas para ajustar el recorte</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCropConfirm">Recortar y Guardar</button>
            </div>
        </div>
    </div>
</div>
