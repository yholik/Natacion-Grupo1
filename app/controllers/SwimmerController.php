<?php

// Agrupa las acciones del nadador fuera de su perfil.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Swimmer.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Booking.php';

class SwimmerController extends BaseController {
    private $swimmerModel;
    private $lessonModel;
    private $bookingModel;
    private $pdo;

    // Carga los modelos que usa el panel swimmer.
    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->swimmerModel = new Swimmer($pdo);
        $this->lessonModel = new Lesson($pdo);
        $this->bookingModel = new Booking($pdo);
    }

    // Inscribe al nadador en una clase.
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
        $lesson = $this->lessonModel->getById($lessonId);
        if (!$lesson) {
            return $this->json('error', 'Clase no encontrada.');
        }
        $enrolled = $this->bookingModel->countEnrolled($lessonId);
        if ($enrolled >= $lesson['capacity']) {
            return $this->json('error', 'La clase está llena. No hay cupos disponibles.');
        }
        if ($this->bookingModel->create($swimmerId, $lessonId)) {
            return $this->json('success', 'Inscripción realizada con éxito.');
        }
        return $this->json('error', 'No se pudo completar la inscripción.');
    }

    // Cancela una inscripción confirmada.
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

    // Lista las clases disponibles para inscribirse.
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
            'bookingsIds' => $bookingsIds,
            'swimmer'     => $swimmer
        ]);
    }

    // Muestra las reservas activas del nadador.
    public function showMyClasses()
    {
        $this->checkAuth();
        $this->checkRole(3);
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        $bookings = $this->bookingModel->getBySwimmer($swimmer['id']);
        $this->render('users/swimmer/swimmer-my-classes.view', [
            'title'    => 'Mis Clases',
            'bookings' => $bookings,
            'swimmer'  => $swimmer
        ]);
    }

    // Reutiliza las reservas para armar la agenda.
    public function showSwimmerCalendar()
    {
        $this->checkAuth();
        $this->checkRole(3);
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        $bookings = $this->bookingModel->getBySwimmer($swimmer['id']);
        $this->render('users/swimmer/swimmer-calendar.view', [
            'title'    => ' - Mi Calendario',
            'bookings' => $bookings,
            'swimmer'  => $swimmer
        ]);
    }
}
