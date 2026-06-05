<div class="d-flex" style="min-height: calc(100vh - 56px);">

    <?php include_once __DIR__ . '/coach-sidebar.view.php'; ?>
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
                                <p class="display-6 fw-bold">-</p>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="col-md-4">
                    <a href="?url=coach-lessons" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Clases</h5>
                                <p class="display-6 fw-bold">-</p>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="col-md-4">
                    <a href="?url=coach-calendar" class="text-decoration-none">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Próxima clase</h5>
                                <p class="display-6 fw-bold">-</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>