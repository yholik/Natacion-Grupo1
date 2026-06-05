<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Swimmer.php';
require_once __DIR__ . '/../models/Coach.php';

class PerfilController extends BaseController
{
    private $swimmerModel;
    private $coachModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->swimmerModel = new Swimmer($pdo);
        $this->coachModel = new Coach($pdo);
    }

    public function show()
    {
        $this->checkAuth();
        $roleId = (int) $_SESSION['role_id'];
        $userId = (int) $_SESSION['user_id'];

        switch ($roleId) {
            case 1: // admin
                $this->render('users/admin/admin-home.view', [
                    'title' => 'Mi Perfil - Admin'
                ]);
                break;

            case 2: // coach
                $coach = $this->coachModel->getCoachById($userId);
                $this->render('users/coach/coach-profile.view', [
                    'title'  => 'Mi Perfil - Coach',
                    'coach'  => $coach
                ]);
                break;

            case 3: // swimmer
                $swimmer = $this->swimmerModel->getSwimmerById($userId);
                $this->render('users/swimmer/swimmer-profile.view', [
                    'title'   => 'Mi Perfil',
                    'swimmer' => $swimmer
                ]);
                break;

            default:
                header('Location: ?url=login');
                exit;
        }
    }
}