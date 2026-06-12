<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Coach.php';


class CoachController extends BaseController {

    private $coachModel;
    private $authModel;
    private $pdo;


    public function __construct()
    {
        global $pdo;        
        $this->pdo = $pdo;
        $this->authModel = new Auth($pdo);
        $this->coachModel = new Coach($pdo);
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
    $this->render('users/coach/coach-calendar.view', 
        array_merge($this->getCoachContext(), ['title' => ' - Calendario'])
    );
}
}

