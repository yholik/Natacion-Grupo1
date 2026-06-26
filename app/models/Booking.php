<?php

// Maneja las inscripciones entre nadadores y clases.

class Booking {
    private $db;

    // Guarda la conexión usada por las reservas.
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Trae las reservas del nadador con datos de la clase, coach, especialidad y cupo.
    public function getBySwimmer(int $swimmerId) {
        $sql = "SELECT b.id AS booking_id, l.id AS lesson_id, l.level, l.day_of_week,
                       l.start_time, l.end_time, l.capacity,
                       c.first_name AS coach_first_name, c.last_name AS coach_last_name,
                       (SELECT GROUP_CONCAT(s.name SEPARATOR ', ')
                        FROM perfil_specialty ps
                        INNER JOIN specialties s ON ps.specialty_id = s.id
                        WHERE ps.profile_id = c.id) AS specialty,
                       (SELECT COUNT(*) FROM bookings b2
                        WHERE b2.lesson_id = l.id AND b2.status = 'Confirmed') AS enrolled
                FROM bookings b
                INNER JOIN lessons l ON b.lesson_id = l.id
                INNER JOIN perfil c ON l.coach_id = c.id
                INNER JOIN auth a ON c.user_id = a.id
                WHERE b.swimmer_id = ? AND b.status = 'Confirmed'
                  AND a.role_id = 2
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$swimmerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cuenta inscriptos activos de una clase específica.
    public function countEnrolled(int $lessonId): int {
        $sql = "SELECT COUNT(*) FROM bookings 
                WHERE lesson_id = ? AND status = 'Confirmed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$lessonId]);
        return (int) $stmt->fetchColumn();
    }

    // Evita que el nadador se anote dos veces en la misma clase.
    public function isEnrolled(int $swimmerId, int $lessonId) {
        $sql = "SELECT COUNT(*) FROM bookings
                WHERE swimmer_id = ? AND lesson_id = ? AND status = 'Confirmed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$swimmerId, $lessonId]);
        return $stmt->fetchColumn() > 0;
    }

    // Registra una nueva inscripción confirmada.
    // Si ya existe una reserva cancelada para la misma dupla, la reactiva.
    public function create(int $swimmerId, int $lessonId) {
        $sql = "UPDATE bookings SET status = 'Confirmed'
                WHERE swimmer_id = ? AND lesson_id = ? AND status = 'Cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$swimmerId, $lessonId]);
        if ($stmt->rowCount() > 0) {
            return true;
        }
        $sql = "INSERT INTO bookings (swimmer_id, lesson_id, status) VALUES (?, ?, 'Confirmed')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$swimmerId, $lessonId]);
    }

    // Marca una reserva como cancelada.
    public function cancel(int $bookingId) {
        $sql = "UPDATE bookings SET status = 'Cancelled' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$bookingId]);
    }
}
