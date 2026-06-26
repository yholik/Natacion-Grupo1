<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">

<div class="d-flex">
    <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
        <?php include __DIR__ . '/../layout/side-bar.php'; ?>
    </aside>
</div>
    <div class="flex-grow-1 p-4 bg-white">

        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Mis Clases</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($bookings)): ?>
                    <div class="row g-3">
                        <?php foreach ($bookings as $b): ?>
                            <?php
                                $start = substr($b['start_time'], 0, 5);
                                $end = substr($b['end_time'], 0, 5);
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title fw-bold mb-0"><?= htmlspecialchars($b['level']) ?></h6>
                                            <span class="badge bg-info">Inscripto</span>
                                        </div>
                                        <div class="card-text text-muted small">
                                            <div class="mb-1">📅 <?= htmlspecialchars($b['day_of_week']) ?></div>
                                            <div class="mb-1">🕐 <?= $start ?> - <?= $end ?></div>
                                            <div class="mb-1">👨‍🏫 <?= htmlspecialchars(($b['coach_first_name'] ?? '') . ' ' . ($b['coach_last_name'] ?? '')) ?></div>
                                            <?php if (!empty($b['specialty'])): ?>
                                                <div class="mb-1">⭐ <?= htmlspecialchars($b['specialty']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                                        <button class="btn btn-sm btn-outline-danger w-100 btn-cancel"
                                                data-booking-id="<?= $b['booking_id'] ?>">
                                            Cancelar inscripción
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0">No estás inscripto en ninguna clase.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <script>
    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', async function() {
            const bookingId = this.dataset.bookingId;
            const result = await Swal.fire({ icon: 'warning', title: '¿Cancelar inscripción?', showCancelButton: true });
            if (!result.isConfirmed) return;
            const resp = await fetch('?url=swimmer-cancel-enrollment', {
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
<?php include __DIR__ . '/../layout/footer.php'; ?>
