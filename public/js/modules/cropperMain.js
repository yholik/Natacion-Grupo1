export function initCropper(fileInput, options = {}) {
  const { aspectRatio = 1, format = 'jpeg', quality = 0.92 } = options;

  let cropper = null;
  let croppedBlob = null;
  let originalFileName = '';

  const modalEl = document.getElementById('cropperModal');
  const confirmBtn = document.getElementById('btnCropConfirm');
  const image = document.getElementById('cropperImage');

  if (!modalEl || !confirmBtn || !image) return null;

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    originalFileName = file.name;
    croppedBlob = null;

    const reader = new FileReader();
    reader.onload = (e) => {
      image.src = e.target.result;
      if (cropper) { cropper.destroy(); cropper = null; }

      const modal = new bootstrap.Modal(modalEl);
      modal.show();

      modalEl.addEventListener('shown.bs.modal', () => {
        cropper = new Cropper(image, {
          aspectRatio,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
        });
      }, { once: true });
    };
    reader.readAsDataURL(file);
  });

  confirmBtn.addEventListener('click', () => {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas();
    canvas.toBlob((blob) => {
      croppedBlob = blob;
      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();
    }, `image/${format}`, quality);
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    if (cropper) { cropper.destroy(); cropper = null; }
  });

  return {
    getCroppedFile() {
      if (!croppedBlob) return null;
      const ext = format === 'jpeg' ? 'jpg' : format;
      const name = originalFileName.replace(/\.[^.]+$/, '') + '_cropped.' + ext;
      return new File([croppedBlob], name, { type: `image/${format}` });
    },
  };
}
