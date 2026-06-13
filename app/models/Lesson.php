<?php

class Lesson {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
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

    public function getAllWithCoach() {
        $sql = "SELECT l.*, c.first_name AS coach_first_name, c.last_name AS coach_last_name, c.specialty,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id AND b.status = 'Confirmed') AS enrolled
                FROM lessons l
                INNER JOIN coaches c ON l.coach_id = c.id
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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