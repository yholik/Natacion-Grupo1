<?php

// Maneja acceso, registro y recuperación de cuenta.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Swimmer.php';
require_once __DIR__ . '/../services/MailService.php';

class AuthController extends BaseController
{
    private $authModel;
    private $swimmerModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->authModel = new Auth($pdo);
        $this->swimmerModel = new Swimmer($pdo);
    }

    // --- SECCIÓN: VISTAS DE AUTENTICACIÓN ---

    // Carga la pantalla de ingreso.
    public function showLogin()
    {
        $this->render('auth/login.view');
    }

    // Muestra el alta pública para nadadores.
    public function showRegister()
    {
        $this->render('auth/register.view', [
            'title' => 'Inscripción de Alumnos'
        ]);
    }

    // Abre el formulario para pedir el reset.
    public function forgotPassword()
    {
        $this->render('auth/forgot-password.view', [
            'title' => 'Recuperar Contraseña'
        ]);
    }

    // Carga la vista donde el usuario define su nueva clave.
    public function showResetForm()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            die('Error: El token de recuperación ha expirado o es inválido.');
        }

        $this->render('auth/reset-password.view', [
            'title' => 'Restablecer Contraseña',
            'token' => $token
        ]);
    }


    // --- SECCIÓN: REGISTRO ---

    // Valida el alta pública y arma auth + perfil.
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showRegister();
        }

        $fields = [
            'first_name'    => trim($_POST['nombre'] ?? ''),
            'last_name'     => trim($_POST['apellido'] ?? ''),
            'email'         => trim($_POST['email'] ?? ''),
            'password'      => $_POST['password'] ?? '',
            'phone'         => trim($_POST['telefono'] ?? ''),
            'profile_image' => 'default-profile.png'
        ];

        if ($this->hasEmptyFields($fields)) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        if (strlen($fields['password']) < 6) {
            return $this->json('warning', 'La contraseña es muy corta (mín. 6 caracteres).');
        }

        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($fields['password'] !== $confirmPassword) {
            return $this->json('warning', 'Las contraseñas no coinciden.');
        }

        $tempFile = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/img/uploads/profiles/swimmers/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($extension, $allowed)) {
                $initial = strtolower(substr($fields['first_name'], 0, 1));
                $lastName = strtolower(str_replace(' ', '', $fields['last_name']));
                $randomNumber = rand(1000, 9999);

                $newFileName = 'swimmer_' . $initial . $lastName . '_' . $randomNumber . '.' . $extension;
                $absolutePath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $absolutePath)) {
                    $fields['profile_image'] = $newFileName;
                    $tempFile = $absolutePath;
                }
            }
        }

        return $this->executeRegistration($fields, $tempFile);
    }

    // Cierra el registro dentro de una transacción.
    private function executeRegistration($f, $tempFile = null)
    {
        try {
            if ($this->authModel->findByEmail($f['email'])) {
                if ($tempFile && file_exists($tempFile)) {
                    unlink($tempFile);
                }
                return $this->json(
                    'user_exists',
                    'Ya tienes una cuenta registrada.',
                    Env::get('APP_URL') . '/?url=login'
                );
            }

            $this->pdo->beginTransaction();

            $userId = $this->authModel->create([
                'email'    => $f['email'],
                'password' => $f['password'],
                'role_id'  => 3
            ]);

            if (!$userId) {
                throw new Exception('Error al crear credenciales.');
            }

            $f['user_id'] = $userId;

            $this->swimmerModel->create($f);

            $this->pdo->commit();
            $_SESSION['user_id'] = $userId;
            $_SESSION['role_id'] = 3;
            $_SESSION['email'] = $f['email'];
            $_SESSION['first_name'] = $f['first_name'];
            $_SESSION['profile_image'] = $f['profile_image'] ?? 'default-profile.png';

            $redirectUrl = rtrim(Env::get('APP_URL'), '/') . '/?url=swimmer-classes-avaliable';

            return $this->json('success', '¡Registro completado!', $redirectUrl);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return $this->json('error', 'No se pudo completar: ' . $e->getMessage());
        }
    }

    // --- SECCIÓN: LOGIN / LOGOUT ---

    // Valida credenciales y arma la sesión.
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Acceso no permitido.');
        }

        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';

        $user = $this->authModel->login($email, $pass);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['profile_image'] = $user['profile_image'];


            //NOVEDAD: AGREGUE ESTE REDIRECT PARA PODER REDIRIGIR BASANDOME EN EL ROL DE USUARIO
            $redirectUrl = match((int)$user['role_id']) {
                1 => Env::get('APP_URL') . '/?url=admin',
                2 => Env::get('APP_URL') . '/?url=coach',
                3 => Env::get('APP_URL') . '/?url=swimmer-classes-avaliable',
                default => Env::get('APP_URL') . '/?url=home'
            };
            return $this->json('success','¡Bienvenido ' . $user['first_name'] . '!', $redirectUrl);
        }

        return $this->json('error', 'Credenciales incorrectas.');
    }

    // Baja la sesión actual y vuelve al login.
    public function logout()
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        header('Location: ' . Env::get('APP_URL') . '/?url=login');
        exit;
    }

    // --- SECCIÓN: RECUPERACIÓN DE CONTRASEÑA ---

    // Genera el token y envía el mail de recuperación.
    public function sendReset()
    {
        $email = $_POST['email'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'Email inválido.');
        }

        $user = $this->authModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->authModel->savePasswordToken($email, $token, $expires);

            $mailService = new MailService();

            $enviado = $mailService->sendEmailResetPassword($email, $token);

            if (!$enviado) {
                return $this->json('error', 'El servidor de correo falló.');
            }
        }

        return $this->json(
            'success',
            'Si el correo existe, recibirás un enlace de recuperación.',
            Env::get('APP_URL') . '/?url=login'
        );
    }

    // Cambia la contraseña si el token sigue válido.
    public function updatePassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($token) || strlen($password) < 6) {
            return $this->json('warning', 'La contraseña debe tener al menos 6 caracteres.');
        }

        $resetRequest = $this->authModel->validateToken($token);

        if ($resetRequest) {
            $email = $resetRequest['email'];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                $this->pdo->beginTransaction();

                $this->authModel->updatePasswordByEmail($email, $hashedPassword);
                $this->authModel->deleteToken($token);

                $this->pdo->commit();

                return $this->json(
                    'success',
                    '¡Contraseña actualizada con éxito!',
                    Env::get('APP_URL') . '/?url=login'
                );

            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                return $this->json('error', 'No se pudo actualizar la contraseña.');
            }
        }

        return $this->json('error', 'El enlace es inválido o ha expirado.');
    }

    // Revisa los obligatorios del registro.
    private function hasEmptyFields($f)
    {
        return empty($f['first_name'])
            || empty($f['last_name'])
            || empty($f['email'])
            || empty($f['password']);
    }

}

