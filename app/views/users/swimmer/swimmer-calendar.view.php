<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Mi Calendario de clases</h2>
        </div>
        <div id="calendarContainer"></div>
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
    const dayMap = { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 };
    const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const bookings = <?= json_encode($bookings) ?>;
    const APP_URL = '<?= rtrim(Env::get('ASSET_URL'), '/') ?>';
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';
    import { handleAlert } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/services/ui.js';

    let currentBookingId = null;

    initCalendar({
        data: bookings, dayMap, dayNames,
        onCardClick: (dayIdx, booking) => {
            currentBookingId = booking.booking_id;
            const start = booking.start_time.substring(0, 5);
            const end = booking.end_time.substring(0, 5);
            const enrolled = booking.enrolled || 0;
            document.getElementById('detailLevel').textContent = booking.level;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + start + ' - ' + end;
            document.getElementById('detailCoach').textContent =
                (booking.coach_first_name || '') + ' ' + (booking.coach_last_name || '');
            document.getElementById('detailSpecialty').textContent =
                booking.specialty || 'Sin especialidad';
            document.getElementById('detailCapacity').textContent =
                enrolled + '/' + booking.capacity;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    });

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
