<?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>

<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="container-fluid p-4">
        <h2 class="mb-4">Calendario de clases</h2>
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

    <!-- Modal CREAR clase -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="formCrearClase" method="POST" action="?url=coach-create-lesson" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="day_of_week" id="createDay">
                    <input type="hidden" name="start_time" id="createTime">
                    <p><strong>Día:</strong> <span id="createDayLabel"></span></p>
                    <p><strong>Horario:</strong> <span id="createTimeLabel"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Nivel</label>
                        <input type="text" name="level" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horario fin</label>
                        <select name="end_time" class="form-select" required>
                            <?php for ($h = 8; $h <= 23; $h++): ?>
                            <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cupo máximo</label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnCrearClase">
                        Crear clase
                    </button>
                </div>
            </form>
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
                    <p><strong>Cupo:</strong> <span id="detailCapacity"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    const dayMap = { 'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3, 'Friday': 4, 'Saturday': 5 };
    const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const dayKeys = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const lessons = <?= json_encode($lessons) ?>;
    </script>
    <script type="module">
    import { initCalendar } from '<?= rtrim(Env::get('ASSET_URL'), '/') ?>/js/modules/calendar.js';

    initCalendar({
        data: lessons, dayMap, dayNames,
        onCellClick: (dayIdx, hour, lesson) => {
            document.getElementById('detailLevel').textContent = lesson.level;
            document.getElementById('detailSchedule').textContent =
                dayNames[dayIdx] + ' ' + String(hour).padStart(2, '0') + ':00 - ' + lesson.end_time.substring(0, 5);
            document.getElementById('detailCapacity').textContent =
                (lesson.enrolled || 0) + '/' + lesson.capacity;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        },
        onEmptyClick: (dayIdx, hour) => {
            const time = String(hour).padStart(2, '0') + ':00';
            document.getElementById('createDay').value = dayKeys[dayIdx];
            document.getElementById('createDayLabel').textContent = dayNames[dayIdx];
            document.getElementById('createTime').value = time + ':00';
            document.getElementById('createTimeLabel').textContent = time;
            new bootstrap.Modal(document.getElementById('createModal')).show();
        }
    });
    const formCrearClase = document.getElementById('formCrearClase');
const btnCrearClase = document.getElementById('btnCrearClase');
const createModalElement = document.getElementById('createModal');

if (formCrearClase && btnCrearClase) {
    formCrearClase.addEventListener('submit', async (event) => {
        event.preventDefault();

        btnCrearClase.disabled = true;
        btnCrearClase.textContent = 'Creando...';

        try {
            const formData = new FormData(formCrearClase);

            const response = await fetch(formCrearClase.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            const rawResponse = await response.text();

            console.log('Respuesta cruda:', rawResponse);

            if (!contentType.includes('application/json')) {
                console.error('Respuesta no JSON del servidor:', rawResponse);

                await window.mostrarConfirmacion(
                    'No se pudo crear la clase. El servidor devolvió una respuesta inválida.',
                    {
                        titulo: 'Error',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

                return;
            }

            const result = JSON.parse(rawResponse);

            const status = result.status || result.type || '';
            const message = result.message || 'Operación finalizada.';
            const redirectUrl = result.redirect || result.url || null;

            if (status === 'success') {
                const createModal = bootstrap.Modal.getInstance(createModalElement);

                if (createModal) {
                    createModal.hide();
                }

                await window.mostrarConfirmacion(
                    message,
                    {
                        titulo: 'Clase creada',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-success',
                        mostrarCancelar: false
                    }
                );

                if (redirectUrl) {
                    window.location.href = redirectUrl;
                    return;
                }

                window.location.reload();
                return;
            }

            await window.mostrarConfirmacion(
                message || 'No se pudo crear la clase.',
                {
                    titulo: 'No se pudo crear',
                    textoAceptar: 'Aceptar',
                    claseAceptar: 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } catch (error) {
            console.error('Error capturado:', error);

            await window.mostrarConfirmacion(
                'No se pudo crear la clase. Revisá la conexión, la ruta o el error del servidor.',
                {
                    titulo: 'Error',
                    textoAceptar: 'Aceptar',
                    claseAceptar: 'btn-danger',
                    mostrarCancelar: false
                }
            );

        } finally {
            btnCrearClase.disabled = false;
            btnCrearClase.textContent = 'Crear clase';
        }
    });
}

    </script>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>