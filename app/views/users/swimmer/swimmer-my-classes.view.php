<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">

<div class="d-flex">
    <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
        <?php include __DIR__ . '/../layout/side-bar.php'; ?>
    </aside>
</div>
    <div class="flex-grow-1 p-4 bg-white">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Mis Clases</h2>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btnViewTable">
                    <i class="bi bi-list-ul"></i> Lista
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnViewCalendar">
                    <i class="bi bi-calendar3"></i> Calendario
                </button>
            </div>
        </div>

        <!-- Vista TABLA -->
        <div id="tableView">
            <?php if (!empty($bookings)): ?>
                <div class="row g-3">
                    <?php
                    $dayTranslations = [
                        'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
                        'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'
                    ];
                    ?>
                    <?php foreach ($bookings as $b): ?>
                        <?php
                            $start = substr($b['start_time'], 0, 5);
                            $end = substr($b['end_time'], 0, 5);
                            $dayEs = $dayTranslations[$b['day_of_week']] ?? $b['day_of_week'];
                        ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title fw-bold mb-0"><?= htmlspecialchars($b['specialty_name'] ?? 'Sin especialidad') ?></h6>
                                        <span class="badge bg-info">Inscripto</span>
                                    </div>
                                    <div class="card-text text-muted small">
                                        <div class="mb-1"><?= $dayEs ?> · <?= $start ?> - <?= $end ?></div>
                                        <div class="mb-1">Prof. <?= htmlspecialchars(($b['coach_first_name'] ?? '') . ' ' . ($b['coach_last_name'] ?? '')) ?></div>
                                        <div class="mb-1"><?= htmlspecialchars($b['level_name']) ?></div>
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

        <!-- Vista CALENDARIO -->
        <div id="calendarView" style="display: none;">
            <div id="calendarContainer"></div>
        </div>

    </div>

    <!-- Modal DETALLE clase -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nivel:</strong> <span id="detailLevel"></span></p>
                    <p><strong>Horario:</strong> <span id="detailSchedule"></span></p>
                    <p><strong>Profesor:</strong> <span id="detailCoach"></span></p>
                    <p><strong>Especialidad:</strong> <span id="detailSpecialty"></span></p>
                    <p><strong>Cupo:</strong> <span id="detailCapacity"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnCancelEnrollment">Cancelar inscripción</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const bookings = <?= json_encode($bookings) ?>;
    const APP_URL = '<?= rtrim(Env::get('APP_URL'), '/') ?>';
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';
    import { handleAlert } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/services/ui.js';
    import { dayMap, dayNames } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/dayConstants.js';

    let currentBookingId = null;
    let calendarInitialized = false;

    // Toggle entre vistas
    const btnTable = document.getElementById('btnViewTable');
    const btnCalendar = document.getElementById('btnViewCalendar');
    const tableView = document.getElementById('tableView');
    const calendarView = document.getElementById('calendarView');

    btnTable.addEventListener('click', () => {
        btnTable.classList.add('active');
        btnCalendar.classList.remove('active');
        tableView.style.display = '';
        calendarView.style.display = 'none';
    });

    btnCalendar.addEventListener('click', () => {
        btnCalendar.classList.add('active');
        btnTable.classList.remove('active');
        calendarView.style.display = '';
        tableView.style.display = 'none';

        if (!calendarInitialized) {
            calendarInitialized = true;
            initCalendar({
                data: bookings, dayMap, dayNames,
                cardButtonLabel: 'Cancelar inscripción',
                onCardClick: (dayIdx, booking) => {
                    currentBookingId = booking.booking_id;
                    const start = booking.start_time.substring(0, 5);
                    const end = booking.end_time.substring(0, 5);
                    const enrolled = booking.enrolled || 0;
                    document.getElementById('detailLevel').textContent = booking.level_name;
                    document.getElementById('detailSchedule').textContent =
                        dayNames[dayIdx] + ' ' + start + ' - ' + end;
                    document.getElementById('detailCoach').textContent =
                        (booking.coach_first_name || '') + ' ' + (booking.coach_last_name || '');
                    document.getElementById('detailSpecialty').textContent =
                        booking.specialty_name || 'Sin especialidad';
                    document.getElementById('detailCapacity').textContent =
                        enrolled + '/' + booking.capacity;
                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                }
            });
        }
    });

    // Cancelar desde tabla
    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', async function() {
            const bookingId = this.dataset.bookingId;
            const result = await Swal.fire({ icon: 'warning', title: '¿Cancelar inscripción?', showCancelButton: true });
            if (!result.isConfirmed) return;

            try {
                const formData = new FormData();
                formData.append('booking_id', bookingId);
                const res = await fetch(APP_URL + '/?url=swimmer-cancel-enrollment', {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();
                if (json.status === 'success') {
                    handleAlert(json.status, json.message, json.redirect);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    handleAlert(json.status, json.message);
                }
            } catch (err) {
                handleAlert('error', 'Error de conexión al servidor.');
            }
        });
    });

    // Cancelar desde calendario
    document.getElementById('btnCancelEnrollment').addEventListener('click', async () => {
        if (!currentBookingId) return;
        const result = await Swal.fire({
            title: '¿Cancelar inscripción?',
            text: '¿Estás seguro de que quieres cancelar esta inscripción?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, volver'
        });
        if (!result.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('booking_id', currentBookingId);
            const res = await fetch(APP_URL + '/?url=swimmer-cancel-enrollment', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.status === 'success') {
                handleAlert(json.status, json.message, json.redirect);
                bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                handleAlert(json.status, json.message);
            }
        } catch (err) {
            handleAlert('error', 'Error de conexión al servidor.');
        }
    });
    </script>

</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
