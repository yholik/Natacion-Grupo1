export async function initCoachHome() {

    //tomo un elemento de la vista para que esta funcion se ejecute solo si existe dicho elemento
      const welcome = document.getElementById('welcome-coach');
        if (!welcome) return;

        

    try {
        const response = await fetch('?url=coach-stats');
        const data = await response.json();

        if (data.status === 'success') {
            const spanishDays = {
                'Monday': 'Lunes', 'Tuesday': 'Martes', 'Wednesday': 'Miércoles', 'Thursday': 'Jueves',
                'Friday': 'Viernes', 'Saturday': 'Sábado', 'Sunday': 'Domingo',
                'Mon': 'Lunes', 'Tue': 'Martes', 'Wed': 'Miércoles', 'Thu': 'Jueves', 'Fri': 'Viernes' // Por si vienen cortos
            };

            const nextClass = data.data.next_class;
            const classesInfo = nextClass
                ? `${spanishDays[nextClass.day_of_week] || nextClass.day_of_week} ${nextClass.start_time.slice(0, 5)}hs`
                : 'Sin clases';


            document.getElementById('statStudents').textContent = data.data.students || 'Sin alumnos';
            document.getElementById('statClasses').textContent  = data.data.classes || 'Sin clases';
            document.getElementById('statNextClass').textContent = classesInfo;
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}