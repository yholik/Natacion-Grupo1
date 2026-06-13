<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Coach.php';
require_once __DIR__ . '/../models/Lesson.php';

class CoachController extends BaseController {

    private $coachModel;
    private $authModel;
    private $lessonModel;
    private $pdo;


    public function __construct()
    {
        global $pdo;        
        $this->pdo = $pdo;
        $this->authModel = new Auth($pdo);
        $this->coachModel = new Coach($pdo);
        $this->lessonModel = new Lesson($pdo);
    }


    public function updateProfileCoach()
        {
        $userId = (int) $_SESSION['user_id'];
        
        //compruebo que el metodo es valido
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Método no permitido.');
            }

            //capturo y valido datos del form
            $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name']  ?? ''),
            'phone'      => trim($_POST['phone']       ?? ''),
            'specialty'  => trim($_POST['specialty']   ?? '')
        ];

        
        if (empty($data['first_name']) || empty($data['last_name'])) {
                return $this->json('warning', 'Nombre y apellido son obligatorios.');
            }

            $updatedData = $this->coachModel->updateCoach($userId, $data);

            if($updatedData) {
                return $this->json('success', 'Perfil actualizado correctamente.');
            } else {
                return $this->json('error', 'Error al actualizar el perfil. Intente nuevamente.');
            }
        
        }


    private function getCoachContext(): array
        {
            $this->checkAuth();
            $this->checkRole(2);

            $userId = (int) $_SESSION['user_id'];
            $coachData = $this->coachModel->getCoachById($userId);

            return ['coach' => $coachData];
        }

    public function showCoachHome()
        {
            $this->render('users/coach/coach-home.view', 
                array_merge($this->getCoachContext(), ['title' => 'Panel de Coach'])
            );
        }

    public function showCoachProfile()
        {
            $this->render('users/coach/coach-profile.view', 
                array_merge($this->getCoachContext(), ['title' => ' - Gestion del perfil'])
            );
        }

    public function showCoachLessons()
        {
            $this->render('users/coach/coach-lessons.view', 
                array_merge($this->getCoachContext(), ['title' => ' - Gestion de lecciones'])
            );
        }

    public function showCoachCalendar()
        {
            $context = $this->getCoachContext();
            $coach = $context['coach'];
            $lessons = $this->lessonModel->getByCoachId((int) $coach['id']);
            $this->render('users/coach/coach-calendar.view', 
                array_merge($context, [
                    'title'   => ' - Calendario',
                    'lessons' => $lessons
                ])
            );
        }

    public function createLesson()
        {
            $this->checkAuth();
            $this->checkRole(2);

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json('error', 'Método no permitido.');
            }

            $userId = (int) $_SESSION['user_id'];
            $coach = $this->coachModel->getCoachById($userId);
            if (!$coach) {
                return $this->json('error', 'Perfil de coach no encontrado.');
            }

            $data = [
                'coach_id'    => (int) $coach['id'],
                'level'       => trim($_POST['level'] ?? ''),
                'day_of_week' => trim($_POST['day_of_week'] ?? ''),
                'start_time'  => trim($_POST['start_time'] ?? ''),
                'end_time'    => trim($_POST['end_time'] ?? ''),
                'capacity'    => (int) ($_POST['capacity'] ?? 0)
            ];

            if (empty($data['level']) || empty($data['day_of_week']) || empty($data['start_time']) || empty($data['end_time']) || $data['capacity'] <= 0) {
                return $this->json('warning', 'Todos los campos son obligatorios y la capacidad debe ser mayor a 0.');
            }

            if ($this->lessonModel->create($data)) {
                return $this->json('success', 'Clase creada correctamente.');
            }

            return $this->json('error', 'No se pudo crear la clase.');
        }

}

