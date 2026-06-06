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

    //compruebo que el metodo es valido
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->json('error', 'Método no permitido.');
        }

        //capturo y valido datos del form
        $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name']  ?? ''),
        'phone'      => trim($_POST['phone']       ?? ''),
        'specialty'  => trim($_POST['specialty']   ?? ''),
        'email'      => trim($_POST['email']       ?? '')
    ];

    
        if ($this->hasEmptyFields($data)) {
        return $this->json('warning', 'Todos los campos son obligatorios.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        $updatedData = $this->coachModel->updateCoach($_SESSION['user_id'], $data);

        if($updatedData) {
            error_log('Perfil actualizado con éxito');
            return $this->json('success', 'Perfil actualizado correctamente.');
        } else {
            return $this->json('error', 'Error al actualizar el perfil. Intente nuevamente.');
        }
    
    }
  private function hasEmptyFields($f)
    {
        return empty($f['first_name'])
            || empty($f['last_name'])
            || empty($f['phone'])
            || empty($f['specialty'])
            || empty($f['email']);
    }
    /*--- FUNCIONES DE VISTAS ---*/
public function showCoachHome()
    {
        //ACA IMPLEMENTO LA FUNCTION CHECKROLE PARA
        // EVITAR QUE UN USUARIO NO APTO INGRESE POR URL
        
        $this->checkAuth();
        $this->checkRole(2); 
        $this->render('users/coach/coach-home.view', [
            'title' => 'Panel de Coach'
        ]);
    }

    public function showCoachProfile()
    {
        $this->checkAuth();
        $this->checkRole(2);         
        $this->render('users/coach/coach-profile.view', [
            'title' => ' - Gestion del perfil'
        ]);
    }

}

