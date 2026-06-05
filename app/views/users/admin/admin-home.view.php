<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex w-100" style="min-height: calc(100vh - 56px);"> 
    
    <?php include_once __DIR__ . '/admin-sidebar.view.php'; ?>

    </div><!-- <<<<< manu: encierro el aside en un contenedor para que pueda ocupar toda la altura y 
        el main quede a su lado, sin que el aside se achique al ponerle un height: 100vh -->

    <main class="flex-grow-1 w-100 p-5 bg-white">
        <h1>Panel de Control - Admin</h1>
        <hr>
        
        <div class="container-fluid px-0">
            <div class="row g-4">
                
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Alumnos Totales</h5>
                            <p class="card-text fs-2 fw-bold">80</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Profesores Totales</h5>
                            <p class="card-text fs-2 fw-bold">7</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>