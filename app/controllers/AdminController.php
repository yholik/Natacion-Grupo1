<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Coach.php';

class AdminController extends BaseController
{
    private $adminModel;
    private $coachModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->adminModel = new Admin($pdo);
        $this->coachModel = new Coach($pdo);
    }

    public function showAdminHome()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $this->render('users/admin/admin-home.view', [
            'title' => 'Panel de Admin'
        ]);
    }

    public function showAdminManageCoaches()
    {
        $this->checkAuth();
        $this->checkRole(1);

        // false = trae activos y dados de baja
        $coaches = $this->coachModel->getAll(false);

        $this->render('users/admin/admin-manage-coaches.view', [
            'title' => 'Gestionar Profesores',
            'coaches' => $coaches
        ]);
    }

    public function showCreateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $this->render('users/admin/admin-create-coach.view', [
            'title' => 'Agregar Profesor'
        ]);
    }

    /**
     * Crea un profesor desde el panel administrador.
     *
     * Si la petición no es POST, muestra el formulario.
     * Si es POST, valida los datos y delega la creación al modelo Admin.
     */
    public function createCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateCoach();
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'specialty'  => trim($_POST['specialty'] ?? '')
        ];

        if ($this->hasEmptyCoachFields($fields)) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        $created = $this->adminModel->createCoach($fields);

        if (!$created) {
            return $this->json(
                'error',
                'No se pudo crear el profesor. Verificá que el email no esté registrado y que el correo funcione.'
            );
        }

        return $this->json(
            'success',
            'Profesor creado correctamente. Se enviaron las credenciales por email.',
            Env::get('APP_URL') . '/?url=admin-manage-coaches'
        );
    }

    private function hasEmptyCoachFields(array $fields)
    {
        return empty($fields['first_name'])
            || empty($fields['last_name'])
            || empty($fields['email'])
            || empty($fields['phone'])
            || empty($fields['specialty']);
    }
}