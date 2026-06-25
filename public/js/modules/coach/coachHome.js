
/*
 * esta funcion me permite llenar los cuadros de estadisticas de la pagina home de un Coach
*/

export async function initCoachHome() {

      const welcome = document.getElementById('welcome-coach');
        if (!welcome) return;


    try {
        const response = await fetch('?url=coach-stats');
        const data = await response.json();

        if (data.status === 'success') {
            document.getElementById('statStudents').textContent = data.data.students || 'Sin alumnos';
            document.getElementById('statClasses').textContent  = data.data.classes || 'Sin clases';
            document.getElementById('statNextClass').textContent = 
            data.data.next_class ? data.data.next_class.day_of_week + ' ' + data.data.next_class.start_time
                : 'Sin clases';
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}