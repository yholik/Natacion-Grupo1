<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100"> <!-- CONFG PARA QUE EL CONTENIDO APAREZCA A LA DERECHA DEL PANEL-->
    <div class="d-flex">
<aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
       
       <?php include __DIR__ . '/../layout/side-bar.php'; ?>
</aside>
</div>
    <div class="flex-grow-1 p-5 bg-white text-center">
       
        
        <div class="container-fluid px-0">
             <h1>Panel de Control - Admin</h1>
        <hr>
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
    </div>



</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>