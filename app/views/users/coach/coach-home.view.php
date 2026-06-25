<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100"> <!-- CONFG PARA QUE EL CONTENIDO APAREZCA A LA DERECHA DEL PANEL-->
    <div class="d-flex">
<aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
       
       <?php include __DIR__ . '/../layout/side-bar.php'; ?>
</aside>
</div>
<!-- CONTENIDO PRINCIPAL -->
    <div class="flex-grow-1 p-5 bg-white text-center">
        <h1 class="mb-4">Bienvenido,
            <?= htmlspecialchars($_SESSION['first_name']) ?>
        </h1>

        
        <div class="container">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="?url=coach-lessons" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Alumnos</h5>
                                <p class="display-6 fw-bold" id="statStudents"></p>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="col-md-4">
                    <a href="?url=coach-lessons" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Clases</h5>
                                <p class="display-6 fw-bold" id="statClasses"></p>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="col-md-4">
                    <a href="?url=coach-calendar" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Próxima clase</h5>
                                <p class="display-6 fw-bold" id="statNextClass"></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

</main>

<script type="module" src="<?= Env::get('ASSET_URL') ?>/js/modules/coachMain.js"></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>