<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="<?= Env::get('ASSET_URL') ?>/css/coach.css" rel="stylesheet">

<main class="d-flex flex-column flex-lg-row w-100"> <!-- CONFG PARA QUE EL CONTENIDO APAREZCA A LA DERECHA DEL PANEL-->
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">

            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="coach-content flex-grow-1">
        <div class="coach-welcome">
            <h2 class="coach-title">Mis alumnos</h2>
            <p class="coach-subtitle">Consulta facilmente tus alumnos incriptos</p>
        </div>
        <!-- Filtros -->
        <div class="row g-2 align-items-end p-4">
            <div class="col-md-3">
                <label class="form-label">Día</label>
                <select class="form-select" id="filterDay">
                    <option value="">Todos</option>                    
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Horario</label>
                <select class="form-select" id="filterTime">
                    <option value="">Todos</option>    
                                 
                </select>
            </div>


            <div class="col-md-3">
                <label class="form-label">Especialidad</label>
                <select class="form-select" id="filterSpecialty">                
                    <option value="">Todas</option>                
                </select>
            </div>

            <div class="col-md-3">
            <button type="button" class="btn btn-primary w-100" id="btnSearch">
                Buscar
            </button>
        </div>


        </div>

        <div id="resultsContainer">
        <div class="text-center text-muted py-5">
            <i class="bi bi-search fs-1"></i>
            <p class="mt-2">Seleccioná los filtros y presioná "Buscar" para ver tus alumnos.</p>
        </div>
    </div>

</main>

<script type="module" src="<?= Env::get('ASSET_URL') ?>/js/modules/coachMain.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>