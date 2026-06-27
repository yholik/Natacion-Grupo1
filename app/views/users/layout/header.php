<!DOCTYPE html>
<!-- Header común del área logueada. -->
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Escuela de Natación' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= Env::get('ASSET_URL') ?>/css/dashboard-header.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
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

            <a class="navbar-brand text-nowrap overflow-hidden" href="?url=landing">
                <span class="d-none d-sm-inline">Club de Natacion - </span>El Delfín Saltarín 🚩
            </a>
            <?php if (isset($_SESSION['role_id'])): ?>
            <button class="navbar-toggler ms-auto flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php endif; ?>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-item d-flex align-items-center my-3 my-lg-0 order-1 order-lg-2 ms-lg-auto">
                    <?php 
                        $foto = $_SESSION['profile_image'] ?? 'default-profile.png';
                        $rutaFoto = Env::get('ASSET_URL') . "/img/uploads/profiles/swimmers/" . $foto;
                        $headerLink = match ((int) ($_SESSION['role_id'] ?? 0)) {
                            1 => '?url=admin-manage-coaches',
                            2 => '?url=profile',
                            3 => '?url=profile',
                            default => '?url=landing',
                        };
                    ?>
                    <img src="<?= $rutaFoto ?>" alt="Perfil" class="profile-img-nav me-2">

                    <a href="<?= $headerLink ?>" class="nav-link text-info p-0 text-decoration-none">
                        Hola, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Usuario') ?>
                    </a>
                </div>
            <?php endif; ?>

            <aside class="sidebar-panel p-3 text-white order-2 order-lg-1 d-lg-none">
                    <?php include_once __DIR__ . '/side-bar.php'; ?>                
            </aside>         
               
            
            </div>
        </div>
    </nav>
