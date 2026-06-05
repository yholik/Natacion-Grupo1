<?php

class Booking {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getBySwimmer(int $swimmerId) {
        $sql = "SELECT b.id AS booking_id, l.level, l.day_of_week, l.start_time, l.end_time,
                       c.first_name AS coach_first_name, c.last_name AS coach_last_name
                FROM bookings b
                INNER JOIN lessons l ON b.lesson_id = l.id
                INNER JOIN coaches c ON l.coach_id = c.id
                WHERE b.swimmer_id = ? AND b.status = 'Confirmed'
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$swimmerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isEnrolled(int $swimmerId, int $lessonId) {
        $sql = "SELECT COUNT(*) FROM bookings
                WHERE swimmer_id = ? AND lesson_id = ? AND status = 'Confirmed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$swimmerId, $lessonId]);
        return $stmt->fetchColumn() > 0;
    }

    public function create(int $swimmerId, int $lessonId) {
        $sql = "INSERT INTO bookings (swimmer_id, lesson_id, status) VALUES (?, ?, 'Confirmed')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$swimmerId, $lessonId]);
    }

    public function cancel(int $bookingId) {
        $sql = "UPDATE bookings SET status = 'Cancelled' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$bookingId]);
    }
}