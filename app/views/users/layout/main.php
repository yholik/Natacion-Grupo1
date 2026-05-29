<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Club de Natación | El Delfín Saltarín <?php echo $title ?? 'Bienvenido'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include __DIR__ . '/header.php'; ?>

    <main class="container py-4">
        <?php echo $contenido; ?> 
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>