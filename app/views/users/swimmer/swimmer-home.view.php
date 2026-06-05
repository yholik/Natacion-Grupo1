<div class="d-flex" style="min-height: calc(100vh - 56px);">

    <?php include_once __DIR__ . '/swimmer-sidebar.view.php'; ?>

    <main class="flex-grow-1 p-4 bg-white">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Clases Disponibles</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Nivel</th>
                                        <th>Día</th>
                                        <th>Horario</th>
                                        <th>Profesor</th>
                                        <th>Cupo</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($lessons)): ?>
                                        <?php foreach ($lessons as $lesson): ?>
                                            <?php
                                                $enrolled = isset($bookingsIds) && in_array($lesson['id'], $bookingsIds);
                                                $full = (int)$lesson['enrolled'] >= (int)$lesson['capacity'];
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($lesson['level']) ?></td>
                                                <td><?= htmlspecialchars($lesson['day_of_week']) ?></td>
                                                <td><?= substr($lesson['start_time'], 0, 5) ?> - <?= substr($lesson['end_time'], 0, 5) ?></td>
                                                <td>
                                                    <?= htmlspecialchars(($lesson['coach_first_name'] ?? '') . ' ' . ($lesson['coach_last_name'] ?? '')) ?>
                                                    <small class="d-block text-muted"><?= htmlspecialchars($lesson['specialty'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $full ? 'bg-danger' : 'bg-success' ?>">
                                                        <?= ($lesson['enrolled'] ?? 0) ?>/<?= $lesson['capacity'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($enrolled): ?>
                                                        <button class="btn btn-sm btn-secondary" disabled>Inscripto</button>
                                                    <?php elseif ($full): ?>
                                                        <button class="btn btn-sm btn-danger" disabled>Completo</button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-success btn-enroll" data-lesson-id="<?= $lesson['id'] ?>">
                                                            Inscribirme
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center py-3">No hay clases disponibles.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Mis Clases</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Nivel</th>
                                        <th>Día</th>
                                        <th>Horario</th>
                                        <th>Profesor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($bookings)): ?>
                                        <?php foreach ($bookings as $b): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($b['level']) ?></td>
                                                <td><?= htmlspecialchars($b['day_of_week']) ?></td>
                                                <td><?= substr($b['start_time'], 0, 5) ?> - <?= substr($b['end_time'], 0, 5) ?></td>
                                                <td><?= htmlspecialchars(($b['coach_first_name'] ?? '') . ' ' . ($b['coach_last_name'] ?? '')) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger btn-cancel"
                                                            data-booking-id="<?= $b['booking_id'] ?>">
                                                        Cancelar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-3">No estás inscripto en ninguna clase.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        document.querySelectorAll('.btn-enroll').forEach(btn => {
            btn.addEventListener('click', async function() {
                const lessonId = this.dataset.lessonId;
                const resp = await fetch('?url=swimmer/enroll', {
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
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', async function() {
                const bookingId = this.dataset.bookingId;
                const result = await Swal.fire({ icon: 'warning', title: '¿Cancelar inscripción?', showCancelButton: true });
                if (!result.isConfirmed) return;
                const resp = await fetch('?url=swimmer/cancel-enroll', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'booking_id=' + bookingId
                });
                const data = await resp.json();
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Inscripción cancelada' }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: data.message });
                }
            });
        });
        </script>
    </main>

</div>