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
            return $this->json('error', 'M�todo no permitido.');
        }
        $lessonId = (int) ($_POST['lesson_id'] ?? 0);
        if ($lessonId <= 0) {
            return $this->json('error', 'Clase inv�lida.');
        }
        $userId = (int) $_SESSION['user_id'];
        $swimmer = $this->swimmerModel->getSwimmerById($userId);
        if (!$swimmer) {
            return $this->json('error', 'Perfil de nadador no encontrado.');
        }
        $swimmerId = (int) $swimmer['id'];
        if ($this->bookingModel->isEnrolled($swimmerId, $lessonId)) {
            return $this->json('warning', 'Ya est�s inscripto en esta clase.');
        }
        if ($this->bookingModel->create($swimmerId, $lessonId)) {
            return $this->json('success', 'Inscripci�n realizada con �xito.');
        }
        return $this->json('error', 'No se pudo completar la inscripci�n.');
    }

    // Cancela una inscripción confirmada.
    public function cancelEnrollment()
    {
        $this->checkAuth();
        $this->checkRole(3);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'M�todo no permitido.');
        }
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            return $this->json('error', 'Inscripci�n inv�lida.');
        }
        if ($this->bookingModel->cancel($bookingId)) {
            return $this->json('success', 'Inscripci�n cancelada.');
        }
        return $this->json('error', 'No se pudo cancelar la inscripci�n.');
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
