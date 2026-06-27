<?php

// Resuelve las clases que crean y consumen los usuarios.

class Lesson {
    private $db;

    // Guarda la conexión para operar sobre lessons.
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Verifica si existe superposición de horarios para un coach en un día.
    public function hasOverlap(int $coachId, string $day, string $start, string $end, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM lessons
                WHERE coach_id = ? AND day_of_week = ?
                  AND start_time < ? AND end_time > ?";
        $params = [$coachId, $day, $end, $start];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
    
    // Inserta una clase nueva para un coach.
    public function create(array $data) {
        if ($this->hasOverlap($data['coach_id'], $data['day_of_week'], $data['start_time'], $data['end_time'])) {
            return false;
        }

        $sql = "INSERT INTO lessons (coach_id, specialty_id, level_id, day_of_week, start_time, end_time, capacity)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['coach_id'],
            $data['specialty_id'],
            $data['level_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['capacity']
        ]);
    }

    // Trae las clases junto al nombre del coach, especialidad y nivel.
    public function getAllWithCoach() {
        $sql = "SELECT l.*, c.first_name AS coach_first_name, c.last_name AS coach_last_name,
                       s.name AS specialty_name,
                       lv.name AS level_name,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id AND b.status = 'Confirmed') AS enrolled,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id) AS total_bookings
                FROM lessons l
                INNER JOIN perfil c ON l.coach_id = c.id
                INNER JOIN auth a ON c.user_id = a.id
                INNER JOIN specialties s ON l.specialty_id = s.id
                INNER JOIN levels lv ON l.level_id = lv.id
                WHERE a.role_id = 2
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca una clase por su ID.
    public function getById(int $id) {
        $sql = "SELECT l.*, s.name AS specialty_name, lv.name AS level_name
                FROM lessons l
                INNER JOIN specialties s ON l.specialty_id = s.id
                INNER JOIN levels lv ON l.level_id = lv.id
                WHERE l.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Filtra las clases pertenecientes a un coach.
    public function getByCoachId(int $coachId) {
        $sql = "SELECT l.*,
                       s.name AS specialty_name,
                       lv.name AS level_name,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id AND b.status = 'Confirmed') AS enrolled,
                       (SELECT COUNT(*) FROM bookings b WHERE b.lesson_id = l.id) AS total_bookings
                FROM lessons l
                INNER JOIN specialties s ON l.specialty_id = s.id
                INNER JOIN levels lv ON l.level_id = lv.id
                WHERE l.coach_id = ?
                ORDER BY l.day_of_week, l.start_time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza una clase existente.
    public function update(int $lessonId, array $data): bool
    {
        if ($this->hasOverlap($data['coach_id'], $data['day_of_week'], $data['start_time'], $data['end_time'], $lessonId)) {
            return false;
        }

        $sql = "UPDATE lessons
                SET coach_id = ?, specialty_id = ?, level_id = ?, day_of_week = ?, start_time = ?, end_time = ?, capacity = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['coach_id'],
            $data['specialty_id'],
            $data['level_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['capacity'],
            $lessonId
        ]);
    }

    // Elimina una clase por su ID solo si no tiene inscritos confirmados.
    public function delete(int $lessonId): bool
    {
        if ($this->countEnrolled($lessonId) > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM lessons WHERE id = ?");
        return $stmt->execute([$lessonId]);
    }

    // Cuenta los inscritos confirmados de una clase.
    public function countEnrolled(int $lessonId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM bookings WHERE lesson_id = ? AND status = 'Confirmed'"
        );
        $stmt->execute([$lessonId]);
        return (int) $stmt->fetchColumn();
    }

    // Cuenta todas las reservas de una clase (cualquier estado).
    public function countBookings(int $lessonId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
        return (int) $stmt->fetchColumn();
    }

    // Elimina todas las reservas de una clase (confirmadas y canceladas).
    public function deleteBookingsByLesson(int $lessonId): void
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
    }

    // Devuelve todos los niveles disponibles.
    public function getAllLevels(): array
    {
        $stmt = $this->db->query("SELECT id, name FROM levels ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve todas las especialidades disponibles.
    public function getAllSpecialties(): array
    {
        $stmt = $this->db->query("SELECT id, name FROM specialties ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
