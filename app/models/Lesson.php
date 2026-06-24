<?php

// Resuelve las clases que crean y consumen los usuarios.

class Lesson {
    private $db;

    // Guarda la conexión para operar sobre lessons.
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    // Inserta una clase nueva para un coach.
    public function create(array $data) {
        $sql = "INSERT INTO lessons (coach_id, level, day_of_week, start_time, end_time, capacity)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['coach_id'],
            $data['level'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['capacity']
        ]);
    }

    // Trae las clases junto al nombre del coach.
    public function getAllWithCoach() {
        $sql = "SELECT l.*, c.first_name AS coach_first_name, c.last_name AS coach_last_name,
                       (
                           SELECT GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ')
                           FROM perfil_specialty ps
                           INNER JOIN specialties s ON ps.specialty_id = s.id
                           WHERE ps.profile_id = c.id
                       ) AS specialty,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id AND b.status = 'Confirmed') AS enrolled
                FROM lessons l
                INNER JOIN perfil c ON l.coach_id = c.id
                INNER JOIN auth a ON c.user_id = a.id
                WHERE a.role_id = 2
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filtra las clases pertenecientes a un coach.
    public function getByCoachId(int $coachId) {
        $sql = "SELECT l.*,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id AND b.status = 'Confirmed') AS enrolled
                FROM lessons l
                WHERE l.coach_id = ?
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
