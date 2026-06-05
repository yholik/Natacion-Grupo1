<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Escuela de Natación' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= Env::get('ASSET_URL') ?>/css/dashboard-header.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .profile-img-nav {
        width: 35px;
        height: 35px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #17a2b8;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        <div class="container">

            <a class="navbar-brand" href="?url=landing">Club de Natacion - El Delfín Saltarín 🚩</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-item d-flex align-items-center my-3 my-lg-0 order-1 order-lg-2 ms-lg-auto">
                    <?php 
                        $foto = $_SESSION['profile_image'] ?? 'default-profile.png';
                        $rutaFoto = Env::get('ASSET_URL') . "/img/uploads/profiles/swimmers/" . $foto;
                    ?>
                    <img src="<?= $rutaFoto ?>" alt="Perfil" class="profile-img-nav me-2">

                    <a href="?url=swimmer/profile" class="nav-link text-info p-0 text-decoration-none">
                        Hola, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Usuario') ?>
                    </a>
                </div>
            <?php endif; ?>

            <aside class="sidebar-panel p-3 text-white order-2 order-lg-1">
                <a href="coach-home" class="d-flex align-items-center mb-3 text-white text-decoration-none gap-2">
                    <i class="bi bi-bootstrap-fill fs-2"></i>
                    <span class="fs-4 tracking-tight">Panel de control</span>
                </a>
                
                <hr class="border-secondary my-3">

                <ul class="nav nav-pills flex-column mb-auto gap-1">
                    
                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <li class="nav-item">
                            <a href="?url=admin-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'admin-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-coaches" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Administrar Profesores</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-swimmers" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Administrar Nadadores</span>
                            </a>
                        </li>
                    
                    <?php elseif ($_SESSION['role_id'] == 2): ?>
                        <li class="nav-item">
                            <a href="?url=coach-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'coach-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=coach-profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Gestión del perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Gestión de clases</span>
                            </a>
                        </li>

                    <?php elseif ($_SESSION['role_id'] == 3): ?>
                        <li class="nav-item">
                            <a href="?url=swimmer-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'swimmer-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=swimmer-profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=swimmer-lessons" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-journal-bookmark-fill fs-5"></i>
                                <span>Mis Clases</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'calendar') ? 'active bg-primary' : 'opacity-75'; ?>">
                            <i class="bi bi-grid-3x3-gap fs-5"></i>
                            <span>Calendario</span>
                        </a>
                    </li>

                </ul>
            </aside>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-item my-3 my-lg-0 order-3 order-lg-3">
                    <a class="nav-link btn btn-outline-danger btn-sm ms-lg-3 w-100 w-lg-auto" href="?url=logout">Salir</a>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </nav>