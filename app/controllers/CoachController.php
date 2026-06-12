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

    /*--- FUNCIONES DE VISTAS ---*/
public function showCoachHome()
    {
        $userId = (int) $_SESSION['user_id'];
        $coachData = $this->coachModel->getCoachById($userId);

        //ACA IMPLEMENTO LA FUNCTION CHECKROLE PARA
        // EVITAR QUE UN USUARIO NO APTO INGRESE POR URL
        
        $this->checkAuth();
        $this->checkRole(2); 
        $this->render('users/coach/coach-home.view', [
            'title' => 'Panel de Coach',
            'coach' => $coachData
        ]);
    }

    public function showCoachProfile()
    {
        $userId = (int) $_SESSION['user_id'];
        $coachData = $this->coachModel->getCoachById($userId);

        $this->checkAuth();
        $this->checkRole(2);         
        $this->render('users/coach/coach-profile.view', [
            'title' => ' - Gestion del perfil',
            'coach' => $coachData
        ]);
    }

}

