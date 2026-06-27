<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="<?= Env::get('ASSET_URL') ?>/css/coach.css" rel="stylesheet">
<main class="d-flex flex-column flex-lg-row w-100"> <!-- CONFG PARA QUE EL CONTENIDO APAREZCA A LA DERECHA DEL PANEL-->
    <div class="d-flex">
<aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
       
       <?php include __DIR__ . '/../layout/side-bar.php'; ?>
</aside>
</div>
<!-- CONTENIDO PRINCIPAL -->
<div class="coach-content flex-grow-1">
    
    <!-- Header de bienvenida -->
    <div class="coach-welcome mb-5" id="welcome-coach">
        <h1 class="coach-title">Bienvenido, <?= htmlspecialchars($_SESSION['first_name']) ?></h1>
        <p class="coach-subtitle">Resumen de tu actividad</p>
    </div>

    <!-- Stats -->
    <div class="row g-4 justify-content-center">
        
        <div class="col-md-4">
            <a href="?url=coach-lessons" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon">🎓</div>
                    <p class="stat-value" id="statStudents"></p>
                    <p class="stat-label">Alumnos</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="?url=coach-calendar" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <p class="stat-value" id="statClasses"></p>
                    <p class="stat-label">Clases</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="?url=coach-calendar" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    
                    <p class="stat-label">Próxima clase</p>
                    <p class="stat-value" id="statNextClass"></p>
                </div>
            </a>
        </div>

    </div>
</div>

</main>

<script type="module" src="<?= Env::get('ASSET_URL') ?>/js/modules/coachMain.js"></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>