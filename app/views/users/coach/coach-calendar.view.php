<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Calendario de clases</h2>
        </div>
        <div id="calendarContainer"></div>
    </div>

    <!-- Modal DETALLE clase (solo lectura) -->
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
                    <p><strong>Especialidad:</strong> <span id="detailSpecialty"></span></p>
                    <p><strong>Cupo:</strong> <span id="detailCapacity"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const lessons = <?= json_encode($lessons) ?>;
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';
    import { dayMap, dayNames } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/dayConstants.js';

    initCalendar({
        data: lessons, dayMap, dayNames,
        cardButtonLabel: 'Ver detalle',
        onCardClick: (dayIdx, lesson) => {
            const start = lesson.start_time.substring(0, 5);
            const end = lesson.end_time.substring(0, 5);
            const enrolled = lesson.enrolled || 0;
            document.getElementById('detailLevel').textContent = lesson.level_name;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + start + ' - ' + end;
            document.getElementById('detailSpecialty').textContent =
                lesson.specialty_name || 'Sin especialidad';
            document.getElementById('detailCapacity').textContent =
                enrolled + '/' + lesson.capacity;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    });
    </script>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
