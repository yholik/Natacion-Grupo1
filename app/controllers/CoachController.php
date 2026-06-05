<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Coach.php';


class CoachController extends BaseController {

    private $coachModel;
    private $pdo;


    public function __construct()
    {
        global $pdo;        
        $this->pdo = $pdo;
        $this->coachModel = new Coach($pdo);
    }


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

