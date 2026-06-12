<?php include __DIR__ . '/../layout/header.php'; ?>
<main class="d-flex flex-column flex-lg-row w-100"> <!-- CONFG PARA QUE EL CONTENIDO APAREZCA A LA DERECHA DEL PANEL-->
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
            <tbody id="calendarBody">
                <!-- aca cargo las filas con js-->
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nivel:</strong> Nivel Inicial</p>
                <p><strong>Horario:</strong> Lunes 08:00 a 09:00</p>
                <hr>
                <h6>Alumnos inscriptos</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pablo</td>
                            <td>Pompin</td>
                            <td>1111111</td>
                        </tr>
                        <tr>
                            <td>Lucía</td>
                            <td>Fernández</td>
                            <td>1122334455</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

const calendarBody = document.getElementById('calendarBody');
const days = 6; 

for (let hour = 7; hour <= 22; hour++) {
    const time = String(hour).padStart(2, '0') + ':00';

    const row = document.createElement('tr');

    
    const timeCell = document.createElement('td');
    timeCell.className = 'fw-bold';
    timeCell.textContent = time;
    row.appendChild(timeCell);

    
    for (let d = 0; d < days; d++) {
        const cell = document.createElement('td');
        row.appendChild(cell);
    }

    calendarBody.appendChild(row);
}
</script>

</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>