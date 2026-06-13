<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="container-fluid p-4">
        <h2 class="mb-4">Mi Calendario de clases</h2>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Horario</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Sábado</th>
                    </tr>
                </thead>
                <tbody id="calendarBody"></tbody>
            </table>
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
                </div>
            </div>
        </div>
    </div>

    <script>
    const dayMap = { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 };
    const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const bookings = <?= json_encode($bookings) ?>;
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';

    initCalendar({
        data: bookings, dayMap, dayNames,
        onCellClick: (dayIdx, hour, booking) => {
            document.getElementById('detailLevel').textContent = booking.level;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + String(hour).padStart(2, '0') + ':00 - ' + booking.end_time.substring(0, 5);
            document.getElementById('detailCoach').textContent =
                (booking.coach_first_name || '') + ' ' + (booking.coach_last_name || '');
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    });
    </script>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>