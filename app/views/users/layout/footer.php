<!-- Footer común y utilidades compartidas. -->
<script>

/**
 * Este script inyecta clases Bootstrap que fuerzan al footer a quedar abajo 
 * por más que haya o no elementos que lo lleven hagan quedar abajo de todo.
/ */
    document.body.classList.add('d-flex', 'flex-column', 'min-vh-100');
    const mainTag = document.querySelector('main');
    if (mainTag) {
        mainTag.classList.add('flex-grow-1');
    }
</script>

<footer class="text-center border-top w-100 py-3 bg-white">
    <p class="mb-0 text-muted">&copy; <?= date('Y') ?> - Club de Natación - El Delfín Saltarín 🚩</p>
</footer>

<?php include __DIR__ . '/../../components/cropper-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/../../components/modal_confirmacion.php'; ?>

<script type="module" src="<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/authMain.js"></script>
</body>
</html>
