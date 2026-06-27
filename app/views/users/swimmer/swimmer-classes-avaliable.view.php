<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">

<div class="d-flex">
    <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
        <?php include __DIR__ . '/../layout/side-bar.php'; ?>
    </aside>
</div>
    <div class="flex-grow-1 p-4 bg-white">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Clases Disponibles</h2>
        </div>

        <?php if (!empty($lessons)): ?>
            <div class="row g-3">
                <?php
                $dayTranslations = [
                    'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'
                ];
                ?>
                <?php foreach ($lessons as $lesson): ?>
                    <?php
                        $enrolled = isset($bookingsIds) && in_array($lesson['id'], $bookingsIds);
                        $full = (int)$lesson['enrolled'] >= (int)$lesson['capacity'];
                        $start = substr($lesson['start_time'], 0, 5);
                        $end = substr($lesson['end_time'], 0, 5);
                        $spots = (int)$lesson['capacity'] - (int)$lesson['enrolled'];
                        $dayEs = $dayTranslations[$lesson['day_of_week']] ?? $lesson['day_of_week'];
                    ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title fw-bold mb-0"><?= htmlspecialchars($lesson['specialty_name'] ?? 'Sin especialidad') ?></h6>
                                    <?php if ($enrolled): ?>
                                        <span class="badge bg-secondary">Inscripto</span>
                                    <?php elseif ($full): ?>
                                        <span class="badge bg-danger">Lleno</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $spots ?>/<?= (int)$lesson['capacity'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-text text-muted small">
                                    <div class="mb-1"><?= $dayEs ?> · <?= $start ?> - <?= $end ?></div>
                                    <div class="mb-1">Prof. <?= htmlspecialchars(($lesson['coach_first_name'] ?? '') . ' ' . ($lesson['coach_last_name'] ?? '')) ?></div>
                                    <div class="mb-1"><?= htmlspecialchars($lesson['level_name']) ?></div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                                <?php if ($enrolled): ?>
                                    <button class="btn btn-sm btn-secondary w-100" disabled>Inscripto</button>
                                <?php elseif ($full): ?>
                                    <button class="btn btn-sm btn-danger w-100" disabled>Lleno</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-success w-100 btn-enroll" data-lesson-id="<?= $lesson['id'] ?>">
                                        Inscribirme
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No hay clases disponibles.</p>
            </div>
        <?php endif; ?>

    </div>
    <script>
    document.querySelectorAll('.btn-enroll').forEach(btn => {
        btn.addEventListener('click', async function() {
            const lessonId = this.dataset.lessonId;
            const resp = await fetch('?url=swimmer-enroll', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'lesson_id=' + lessonId
            });
            const data = await resp.json();
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Inscripto correctamente' }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: data.message });
            }
        });
    });
    </script>

</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
