<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Swimmer.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Booking.php';

class SwimmerController extends BaseController {
    private $swimmerModel;
    private $lessonModel;
    private $bookingModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->swimmerModel = new Swimmer($pdo);
        $this->lessonModel = new Lesson($pdo);
        $this->bookingModel = new Booking($pdo);
    }

     public function updateProfile()
    {
        $this->checkAuth();
        $this->checkRole(3);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Método no permitido.');
        }
        $userId = (int) $_SESSION['user_id'];
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? null
        ];
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return $this->json('warning', 'Nombre y apellido son obligatorios.');
        }
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/img/uploads/profiles/swimmers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowed)) {
                $initial = strtolower(substr($data['first_name'], 0, 1));
                $lastName = strtolower(str_replace(' ', '', $data['last_name']));
                $randomNumber = rand(1000, 9999);
                $newFileName = 'swimmer_' . $initial . $lastName . '_' . $randomNumber . '.' . $extension;
                $absolutePath = $uploadDir . $newFileName;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $absolutePath)) {
                    $data['profile_image'] = $newFileName;
                    $_SESSION['profile_image'] = $newFileName;
                }
            } else {
                return $this->json('warning', 'Formato de imagen no válido (jpg, png, gif).');
            }
        }
        if ($this->swimmerModel->updateSwimmer($userId, $data)) {
            return $this->json('success', 'Perfil actualizado correctamente.');
        }
        return $this->json('error', 'No se pudieron guardar los cambios.');
    }

     public function enroll()
    {
        $this->checkAuth();
        $this->checkRole(3);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Método no permitido.');
        }
        $lessonId = (int) ($_POST['lesson_id'] ?? 0);
        if ($lessonId <= 0) {
            return $this->json('error', 'Clase inválida.');
        }
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        if (!$swimmer) {
            return $this->json('error', 'Perfil de nadador no encontrado.');
        }
        $swimmerId = (int) $swimmer['id'];
        if ($this->bookingModel->isEnrolled($swimmerId, $lessonId)) {
            return $this->json('warning', 'Ya estás inscripto en esta clase.');
        }
        if ($this->bookingModel->create($swimmerId, $lessonId)) {
            return $this->json('success', 'Inscripción realizada con éxito.');
        }
        return $this->json('error', 'No se pudo completar la inscripción.');
    }

     public function cancelEnrollment()
    {
        $this->checkAuth();
        $this->checkRole(3);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Método no permitido.');
        }
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            return $this->json('error', 'Inscripción inválida.');
        }
        if ($this->bookingModel->cancel($bookingId)) {
            return $this->json('success', 'Inscripción cancelada.');
        }
        return $this->json('error', 'No se pudo cancelar la inscripción.');
    }

    /* VISTAS */
    public function showProfile()
    {
        $this->checkAuth();
        $this->checkRole(3);
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        if (!$swimmer) {
            die('Error: Perfil de nadador no encontrado.');
        }
        $this->render('users/swimmer/swimmer-profile.view', [
            'title'   => 'Mi Perfil',
            'swimmer' => $swimmer
        ]);
    }

    public function showAvaliableClasses()
    {
        $this->checkAuth();
        $this->checkRole(3);
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        $lessons = $this->lessonModel->getAllWithCoach();
        $bookings = $this->bookingModel->getBySwimmer($swimmer['id']);
        $bookingsIds = array_column($bookings, 'lesson_id');
        $this->render('users/swimmer/swimmer-classes-avaliable.view', [
            'title'       => 'Clases Disponibles',
            'lessons'     => $lessons,
            'bookingsIds' => $bookingsIds
        ]);
    }
    
    public function showMyClasses()
    {
        $this->checkAuth();
        $this->checkRole(3);
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        $bookings = $this->bookingModel->getBySwimmer($swimmer['id']);
        $this->render('users/swimmer/swimmer-my-classes.view', [
            'title'    => 'Mis Clases',
            'bookings' => $bookings
        ]);
    }

}