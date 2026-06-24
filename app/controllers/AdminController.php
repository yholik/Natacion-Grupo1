<?php
// app/controllers/AdminController.php

// Maneja el panel de administración.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Coach.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Swimmer.php';

class AdminController extends BaseController
{
    private $adminModel;
    private $coachModel;
    private $swimmerModel;
    private $pdo;
    private $lessonModel;

    // Carga los modelos que usa el admin.
    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->adminModel = new Admin($pdo);
        $this->coachModel = new Coach($pdo);
        $this->swimmerModel = new Swimmer($pdo);
        $this->lessonModel = new Lesson($pdo);
    }

    // El home admin quedó absorbido por profesores.
    public function showAdminHome()
    {
        $this->checkAuth();
        $this->checkRole(1);

        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-coaches');
        exit;
    }

    // Lista profesores activos y dados de baja.
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

    // Abre el formulario de alta de profesores.
    public function showCreateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $specialties = $this->coachModel->getAllSpecialties();

        $this->render('users/admin/admin-create-coach.view', [
            'title' => 'Agregar Profesor',
            'specialties' => $specialties
        ]);
    }

    /**
     * Crea un profesor desde el panel administrador.
     *
     * Si la petición no es POST, muestra el formulario.
     * Si es POST, valida los datos y delega la creación al modelo Admin.
     */
    // Procesa el alta de un profesor.
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
            'specialty_ids'  => array_map('intval', $_POST['specialty_ids'] ?? [])
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

    // Marca los obligatorios del formulario coach.
    private function hasEmptyCoachFields(array $fields)
    {
        return empty($fields['first_name'])
            || empty($fields['last_name'])
            || empty($fields['email'])
            || empty($fields['phone'])
            || empty($fields['specialty_ids']);
    }

    // Da de baja lógica a un profesor.
    public function deactivateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageCoaches();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageCoaches();
        }

        $this->adminModel->deactivateCoach($userId);

        return $this->redirectToManageCoaches();
    }

    // Revierte una baja de profesor.
    public function activateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageCoaches();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageCoaches();
        }

        $this->adminModel->activateCoach($userId);

        return $this->redirectToManageCoaches();
    }

    // Vuelve al listado de profesores.
    private function redirectToManageCoaches()
    {
        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-coaches');
        exit;
    }

    // Muestra o guarda la edición de un profesor.
    public function editCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $userId = (int) ($_POST['user_id'] ?? $_GET['id'] ?? 0);

        if ($userId <= 0) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->json('error', 'ID de profesor inválido.');
            }

            return $this->redirectToManageCoaches();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $coach = $this->coachModel->getCoachById($userId);

            if (!$coach) {
                return $this->redirectToManageCoaches();
            }

            return $this->render('users/admin/admin-create-coach.view', [
                'title' => 'Editar Profesor',
                'coach' => $coach,
                'specialties' => $this->coachModel->getAllSpecialties()
            ]);
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'specialty_ids'  => array_map('intval', $_POST['specialty_ids'] ?? [])
        ];

        if (
            empty($fields['first_name']) ||
            empty($fields['last_name']) ||
            empty($fields['phone']) ||
            empty($fields['specialty_ids'])
        ) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        $updated = $this->adminModel->updateCoach($userId, $fields);

        if (!$updated) {
            return $this->json('error', 'No se pudo actualizar el profesor.');
        }

        return $this->json(
            'success',
            'Profesor actualizado correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-coaches'
        );
    }

    // Carga el tablero de clases.
    public function showAdminManageLessons()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $lessons = $this->lessonModel->getAllWithCoach();

        $this->render('users/admin/admin-manage-lessons.view', [
            'title' => 'Gestionar Clases',
            'lessons' => $lessons
        ]);
    }

    // Lista nadadores activos y dados de baja.
    public function showAdminManageSwimmers()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $swimmers = $this->swimmerModel->getAll(false);

        $this->render('users/admin/admin-manage-swimmers.view', [
            'title' => 'Gestionar Nadadores',
            'swimmers' => $swimmers
        ]);
    }

    // Abre el formulario de alta de nadadores.
    public function showCreateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $this->render('users/admin/admin-create-swimmer.view', [
            'title' => 'Agregar Nadador'
        ]);
    }

    // Procesa el alta de un nadador.
    public function createSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateSwimmer();
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'profile_image' => 'default-profile.png'
        ];

        if ($this->hasEmptySwimmerFields($fields)) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        $uploadedFile = $this->handleSwimmerProfileImageUpload($fields);

        if ($uploadedFile === false) {
            return;
        }

        $created = $this->adminModel->createSwimmer($fields);

        if (!$created) {
            if ($uploadedFile && file_exists($uploadedFile)) {
                unlink($uploadedFile);
            }

            return $this->json(
                'error',
                'No se pudo crear el nadador. Verificá que el email no esté registrado y que el correo funcione.'
            );
        }

        return $this->json(
            'success',
            'Nadador creado correctamente. Se enviaron las credenciales por email.',
            Env::get('APP_URL') . '/?url=admin-manage-swimmers'
        );
    }

    // Marca los obligatorios del formulario swimmer.
    private function hasEmptySwimmerFields(array $fields)
    {
        return empty($fields['first_name'])
            || empty($fields['last_name'])
            || empty($fields['email']);
    }

    // Da de baja lógica a un nadador.
    public function deactivateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSwimmers();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageSwimmers();
        }

        $this->adminModel->deactivateSwimmer($userId);

        return $this->redirectToManageSwimmers();
    }

    // Revierte una baja de nadador.
    public function activateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSwimmers();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageSwimmers();
        }

        $this->adminModel->activateSwimmer($userId);

        return $this->redirectToManageSwimmers();
    }

    // Vuelve al listado de nadadores.
    private function redirectToManageSwimmers()
    {
        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-swimmers');
        exit;
    }

    // Muestra o guarda la edición de un nadador.
    public function editSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $userId = (int) ($_POST['user_id'] ?? $_GET['id'] ?? 0);

        if ($userId <= 0) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->json('error', 'ID de nadador inválido.');
            }

            return $this->redirectToManageSwimmers();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $swimmer = $this->swimmerModel->getSwimmerByUserId($userId, false);

            if (!$swimmer) {
                return $this->redirectToManageSwimmers();
            }

            return $this->render('users/admin/admin-create-swimmer.view', [
                'title' => 'Editar Nadador',
                'swimmer' => $swimmer
            ]);
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null
        ];

        if (empty($fields['first_name']) || empty($fields['last_name'])) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        $uploadedFile = $this->handleSwimmerProfileImageUpload($fields);

        if ($uploadedFile === false) {
            return;
        }

        $updated = $this->adminModel->updateSwimmer($userId, $fields);

        if (!$updated) {
            if ($uploadedFile && file_exists($uploadedFile)) {
                unlink($uploadedFile);
            }

            return $this->json('error', 'No se pudo actualizar el nadador.');
        }

        return $this->json(
            'success',
            'Nadador actualizado correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-swimmers'
        );
    }

    // Normaliza y guarda la foto subida desde admin.
    private function handleSwimmerProfileImageUpload(array &$fields)
    {
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/img/uploads/profiles/swimmers/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowed, true)) {
            $this->json('warning', 'Formato de imagen no válido (jpg, jpeg, png, gif).');
            return false;
        }

        $initial = strtolower(substr($fields['first_name'], 0, 1));
        $lastName = strtolower(str_replace(' ', '', $fields['last_name']));
        $randomNumber = rand(1000, 9999);
        $newFileName = 'swimmer_' . $initial . $lastName . '_' . $randomNumber . '.' . $extension;
        $absolutePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $absolutePath)) {
            $this->json('error', 'No se pudo guardar la imagen de perfil.');
            return false;
        }

        $fields['profile_image'] = $newFileName;

        return $absolutePath;
    }
}
