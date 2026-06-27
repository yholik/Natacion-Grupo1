export function initCoachStudents() {

    const form = document.getElementById("btnSearch");
    if (!form) return;

    const filterDay = document.getElementById("filterDay");
    const filterTime = document.getElementById("filterTime");
    const filterSpecialty = document.getElementById("filterSpecialty");
    const resultsContainer = document.getElementById("resultsContainer");

    generateOptionsTime();
    generateDayOptions();
    loadCoachSpecialties();


    //genero dinamico de los inputs de horarios
    function generateOptionsTime() {
        for (let i = 6; i <= 24; i++) {
            const actualHour = i === 24 ? 0 : i;
            const formatHour = String(actualHour).padStart(2, '0') + ":00";
            filterTime.innerHTML += `<option value="${formatHour}">${formatHour}</option>`;
        }

    }

    function generateDayOptions() {
        const labordays = [{ label: 'Lunes',     value: 'Monday' },
    { label: 'Martes',    value: 'Tuesday' },
    { label: 'Miércoles', value: 'Wednesday' },
    { label: 'Jueves',    value: 'Thursday' },
    { label: 'Viernes',   value: 'Friday' },
    { label: 'Sábado',    value: 'Saturday' }];

        labordays.forEach(dia => {          
            filterDay.innerHTML += `<option value="${dia.value}">${dia.label}</option>`;
        });
    }

    //cargo en el select solo las especialidades que tenga
    async function loadCoachSpecialties() {
        try {
            const resp = await fetch('?url=coach-get-specialties');
            const data = await resp.json();

            if (data.status === 'success') {
                data.data.especialidades.forEach(esp => {
                    filterSpecialty.innerHTML += `<option value="${esp.id}">${esp.name}</option>`;
                });
            }
        } catch (error) {
            console.error("Error al cargar las especialidades:", error);
        }
    }



    // 4. EVENTO DEL BOTÓN BUSCAR
    btnSearch.addEventListener('click', async () => {
        const params = new URLSearchParams({
            url: 'coach-get-students',
            day: filterDay.value,
            time: filterTime.value,
            specialty: filterSpecialty.value
        });

        // Spinner de carga
        resultsContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Buscando alumnos...</p>
            </div>
        `;

        await new Promise(resolve => setTimeout(resolve, 800));

        try {
            const resp = await fetch(`?${params.toString()}`);
            const data = await resp.json();

            if (data.status === 'success') {
                renderStudentsTable(data.data.students);
            } else {
                resultsContainer.innerHTML = `<div class="alert alert-danger m-3">${data.message || 'Error.'}</div>`;
            }
        } catch (error) {
            console.error("Error en la búsqueda:", error);
            resultsContainer.innerHTML = `<div class="alert alert-danger m-3">Error de conexión con el servidor.</div>`;
        }
    });

    // 5. RENDERIZADO DE LA TABLA DE RESULTADOS
    function renderStudentsTable(students) {
        if (!students || students.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-person-x fs-1"></i>
                    <p class="mt-2">No hay alumnos inscriptos para los filtros seleccionados.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive bg-white shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>                            
                            <th class="text-center">Actividad</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        students.forEach(student => {
            html += `
                <tr>
                    <td><strong>${student.first_name}</strong></td>
                    <td>${student.last_name}</td>                    
                    <td class="text-center">

                        <div class="d-flex flex-wrap gap-1">
                        ${student.specialty_names ? student.specialty_names.split(', ').map(actividad => `
                         <span class="badge bg-primary">${actividad}</span>
                         `).join('') : '<span class="badge bg-secondary">Sin actividad</span>'}
                        </div>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        resultsContainer.innerHTML = html;
    }


}