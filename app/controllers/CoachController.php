<?php

// Reune lo que usa el profesor en su panel.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Coach.php';
require_once __DIR__ . '/../models/Lesson.php';

class CoachController extends BaseController {

    private $coachModel;
    private $lessonModel;

    // Prepara los modelos que necesita el rol coach.
    public function __construct()
    {
        global $pdo;
        $this->coachModel = new Coach($pdo);
        $this->lessonModel = new Lesson($pdo);
    }

    // Carga una vez los datos base del coach logueado.
    private function getCoachContext(): array
    {
        $this->checkAuth();
        $this->checkRole(2);

        $userId = (int) $_SESSION['user_id'];
        $coachData = $this->coachModel->getCoachById($userId);

        return [
            'coach' => $coachData,
            'user_id' => $userId
            ];
    }

    // Muestra la portada del panel coach.
    public function showCoachHome()
    {
        $this->render(
            'users/coach/coach-home.view',
            array_merge($this->getCoachContext(), ['title' => 'Panel de Coach'])
        );
    }

    // Abre la gestion de clases del coach.
    public function showCoachLessons()
    {
        $this->render(
            'users/coach/coach-lessons.view',
            array_merge($this->getCoachContext(), ['title' => ' - Gestion de lecciones'])
        );
    }

    // Lleva las clases del coach a la vista calendario.
    public function showCoachCalendar()
    {
        $context = $this->getCoachContext();
        $coach = $context['coach'];
        $lessons = $this->lessonModel->getByCoachId((int) $coach['id']);

        $this->render(
            'users/coach/coach-calendar.view',
            array_merge($context, [
                'title'   => ' - Calendario',
                'lessons' => $lessons
            ])
        );
    }


// Carga la vista del admin con el catalogo de especialidades.
    public function showAllEspecialidades()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $editingSpecialty = null;

        $specialtyId = (int) ($_GET['id'] ?? 0);
        if ($specialtyId > 0) {
            $editingSpecialty = $this->coachModel->getSpecialtyById($specialtyId);
        }

        $this->renderManageSpecialties($editingSpecialty);
    }

    private function renderManageSpecialties($editingSpecialty = null, ?string $modalMessage = null)
    {
        $specialties = $this->coachModel->getAllSpecialties();

        $this->render('users/admin/admin-manage-specialties.view', [
            'title' => 'Administrar Especialidades',
            'specialties' => $specialties,
            'editingSpecialty' => $editingSpecialty,
            'modalMessage' => $modalMessage
        ]);
    }

    // Devuelve al listado de especialidades.
    private function redirectToManageSpecialties()
    {
        header('Location: ?url=admin-manage-specialties');
        exit;
    }




    public function getCoachStats()
{
    $context = $this->getCoachContext();
    $userId = $context['user_id'];

    
    $totalStudents = $this->coachModel->countStudentsByCoach($userId);    
    $totalClasses = $this->coachModel->countClassesByCoach($userId);
    $nextClass = $this->coachModel->getNextClassByCoach($userId);

    return $this->json('success', 'Stats obtenidas', null, [
        'students'   => $totalStudents,
        'classes'    => $totalClasses,
        'next_class' => $nextClass
    ]);
}

    // crear clase nueva para el coach logueado.
    public function createLesson()
    {
        return $this->json('error', 'No tenes permiso para crear clases. Solo el administrador puede gestionar clases.');
    }

    // Actualiza una clase existente del coach logueado.
    public function editLesson()
    {
        return $this->json('error', 'No tenes permiso para editar clases. Solo el administrador puede gestionar clases.');
    }

    // Elimina una clase del coach logueado si no tiene inscritos.
    public function deleteLesson()
    {
        return $this->json('error', 'No tenes permiso para eliminar clases. Solo el administrador puede gestionar clases.');
    }

    public function createSpecialty()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSpecialties();
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            return $this->redirectToManageSpecialties();
        }

        if ($this->coachModel->specialtyNameExists($name)) {
            return $this->renderManageSpecialties(null, 'Ya existe una especialidad con ese nombre');
        }

        $this->coachModel->createSpecialty($name);

        return $this->redirectToManageSpecialties();
    }
    
    public function updateSpecialty()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSpecialties();
        }

        $specialtyId = (int) ($_POST['specialty_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($specialtyId <= 0 || $name === '') {
            return $this->redirectToManageSpecialties();
        }

        if ($this->coachModel->specialtyNameExists($name, $specialtyId)) {
            $editingSpecialty = $this->coachModel->getSpecialtyById($specialtyId);
            return $this->renderManageSpecialties($editingSpecialty, 'Ya existe una especialidad con ese nombre');
        }

        $this->coachModel->updateSpecialty($specialtyId, $name);

        return $this->redirectToManageSpecialties();
    }

    // Elimina una especialidad libre de profesores asociados.
    public function deleteSpecialty()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSpecialties();
        }

        $specialtyId = (int) ($_POST['specialty_id'] ?? 0);
        if ($specialtyId <= 0) {
            return $this->redirectToManageSpecialties();
        }

        $this->coachModel->deleteSpecialty($specialtyId);

        return $this->redirectToManageSpecialties();
    }

    public function getSpecialtiesJSON()
{
    
    $this->checkAuth();
    $this->checkRole(2); 
    $coachId = $_SESSION['user_id'] ?? 0;
    
    $specialties = $this->coachModel->getSpecialtiesByCoachId($coachId);

    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => [
          'especialidades' => $specialties
        ]
    ]);
    exit;
}

public function getCoachStudents()
{
    
    $this->checkAuth();
    $this->checkRole(2);
    
    $coachId = $_SESSION['user_id'] ?? 0;
    
    $day = $_GET['day'] ?? '';
    $time = $_GET['time'] ?? '';
    $specialtyId = $_GET['specialty'] ? (int)$_GET['specialty'] : null;

    
    $students = $this->coachModel->getStudentsByFilters($coachId, $day, $time, $specialtyId);

   
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => [
            'students' => $students
        ]
    ]);
    exit;
}

}
