<?php

// Centraliza "mi perfil" para coach y swimmer.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Swimmer.php';
require_once __DIR__ . '/../models/Coach.php';
require_once __DIR__ . '/../models/Auth.php';

class PerfilController extends BaseController
{
    private $swimmerModel;
    private $coachModel;

    private $authModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->swimmerModel = new Swimmer($pdo);
        $this->coachModel = new Coach($pdo);
        $this->authModel = new Auth($pdo);
    }

    // Muestra el perfil del usuario según su rol.
    public function showProfile()
    {
        $this->checkAuth();
        $roleId = (int) $_SESSION['role_id'];

        switch ($roleId) {
            case 1:
                header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-coaches');
                exit;

            case 2:
                $coach = $this->getCoachProfile();
                $this->render('users/coach/coach-profile.view', [
                    'title'  => 'Mi Perfil - Coach',
                    'coach'  => $coach,
                    'specialties' => $this->coachModel->getAllSpecialties()
                ]);
                break;

            case 3:
                $swimmer = $this->getSwimmerProfile();
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

    //Edicion de datos personales de un usuario
    public function updateProfile()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Método no permitido.');
        }

        $roleId = (int) $_SESSION['role_id'];
        $userId = (int) $_SESSION['user_id'];

        if ($roleId === 2) {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
                'specialty_ids'  => array_map('intval', $_POST['specialty_ids'] ?? [])
            ];

            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['specialty_ids'])) {
                return $this->json('warning', 'Nombre, apellido y especialidades son obligatorios.');
            }

            if ($this->coachModel->updateCoach($userId, $data)) {
                return $this->json('success', 'Perfil actualizado correctamente.');
            }

            return $this->json('error', 'Error al actualizar el perfil. Intente nuevamente.');
        }

        if ($roleId === 3) {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
                'birth_date' => $_POST['birth_date'] ?? null
            ];

            if (empty($data['first_name']) || empty($data['last_name'])) {
                return $this->json('warning', 'Nombre y apellido son obligatorios.');
            }

            $uploaded = $this->handleSwimmerImageUpload($data);

            if ($uploaded === false) {
                return;
            }

            if ($this->swimmerModel->updateSwimmer($userId, $data)) {
                return $this->json('success', 'Perfil actualizado correctamente.');
            }

            if ($uploaded && file_exists($uploaded)) {
                unlink($uploaded);
            }

            return $this->json('error', 'No se pudieron guardar los cambios.');
        }

        return $this->json('error', 'Rol no permitido.');
    }

    //edicion de password de un usuario
    public function updatePassword()
{
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->json('error', 'Método no permitido.');
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';


    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        return $this->json('warning', 'Debe completar todos los campos.');
    }


    if ($newPassword !== $confirmPassword) {
        return $this->json('warning', 'Las contraseñas nuevas no coinciden.');
    }


    $email = $_SESSION['email'];

    $auth = $this->authModel->getPasswordByEmail($email);


    if (!$auth || !password_verify($currentPassword, $auth['password'])) {
        return $this->json('error', 'La contraseña actual es incorrecta.');
    }


    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);


    if ($this->authModel->updatePasswordByEmail($email, $hashedPassword)) {

        return $this->json(
            'success',
            'Contraseña actualizada correctamente.'
        );

    }


    return $this->json(
        'error',
        'No se pudo actualizar la contraseña.'
    );
}





    // Busca el perfil coach de la sesión actual.
    private function getCoachProfile(): array
    {
        $userId = (int) $_SESSION['user_id'];
        $coach = $this->coachModel->getCoachById($userId);

        if (!$coach) {
            die('Error: Perfil de coach no encontrado.');
        }

        return $coach;
    }

    // Busca el perfil swimmer de la sesión actual.
    private function getSwimmerProfile(): array
    {
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);

        if (!$swimmer) {
            die('Error: Perfil de nadador no encontrado.');
        }

        return $swimmer;
    }

    // Procesa la foto del nadador antes de guardar.
    private function handleSwimmerImageUpload(array &$data)
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
            $this->json('warning', 'Formato de imagen no válido (jpg, png, gif).');
            return false;
        }

        $initial = strtolower(substr($data['first_name'], 0, 1));
        $lastName = strtolower(str_replace(' ', '', $data['last_name']));
        $randomNumber = rand(1000, 9999);
        $newFileName = 'swimmer_' . $initial . $lastName . '_' . $randomNumber . '.' . $extension;
        $absolutePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $absolutePath)) {
            $this->json('error', 'No se pudo guardar la imagen de perfil.');
            return false;
        }

        $data['profile_image'] = $newFileName;
        $_SESSION['profile_image'] = $newFileName;

        return $absolutePath;
    }
}
