<?php include __DIR__ . '/../users/layout/header.php'; ?>
<main class="container mt-4">
<div class="container mt-4">
    <h2>Listado de Nadadores</h2>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($swimmers as $s): ?>
            <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                <td><?php echo $s['email']; ?></td>
                <td><?php echo $s['phone']; ?></td>
                <td>
                    <button class="btn btn-sm btn-info">Editar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>