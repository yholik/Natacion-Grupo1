<?php

// Expone las consultas de perfil para usuarios con rol coach.

class Coach
{
    private $db;

    // Toma la conexion compartida.
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Obtiene profesores.
     *
     * @param bool $onlyActive
     * true  = trae solo profesores activos
     * false = trae activos y dados de baja
     */
    public function getAll(bool $onlyActive = false)
    {
        $sql = "
            SELECT
                c.*,
                a.email,
                (
                    SELECT GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ')
                    FROM perfil_specialty ps
                    INNER JOIN specialties s
                        ON ps.specialty_id = s.id
                    WHERE ps.profile_id = c.id
                ) AS specialty_names,
                (
                    SELECT GROUP_CONCAT(ps.specialty_id ORDER BY ps.specialty_id SEPARATOR ',')
                    FROM perfil_specialty ps
                    WHERE ps.profile_id = c.id
                ) AS specialty_ids_csv
            FROM perfil c
            INNER JOIN auth a
                ON c.user_id = a.id
            WHERE a.deleted_at IS NULL
              AND a.role_id = 2
        ";

        if ($onlyActive) {
            $sql .= " AND c.deleted_at IS NULL";
        }

        $sql .= " ORDER BY c.id DESC";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapCoachSpecialties'], $rows);
    }

    // Inserta el perfil coach dentro de la tabla unificada.
    public function createCoach(array $data)
    {
        $startedTransaction = !$this->db->inTransaction();

        try {
            if ($startedTransaction) {
                $this->db->beginTransaction();
            }

            $sql = "INSERT INTO perfil (user_id, first_name, last_name, phone, profile_image)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $created = $stmt->execute([
                $data['user_id'],
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                'default-profile.png'
            ]);

            if (!$created) {
                throw new RuntimeException('No se pudo crear el perfil del coach.');
            }

            $profileId = (int) $this->db->lastInsertId();

            if (!$this->syncCoachSpecialties($profileId, $data['specialty_ids'] ?? [])) {
                throw new RuntimeException('No se pudieron guardar las especialidades.');
            }

            if ($startedTransaction) {
                $this->db->commit();
            }

            return true;
        } catch (Throwable $e) {
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    // Busca el perfil coach asociado al auth logueado.
    public function getCoachById(int $userId)
    {
        $sql = "
            SELECT
                c.*,
                a.email,
                (
                    SELECT GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ')
                    FROM perfil_specialty ps
                    INNER JOIN specialties s
                        ON ps.specialty_id = s.id
                    WHERE ps.profile_id = c.id
                ) AS specialty_names,
                (
                    SELECT GROUP_CONCAT(ps.specialty_id ORDER BY ps.specialty_id SEPARATOR ',')
                    FROM perfil_specialty ps
                    WHERE ps.profile_id = c.id
                ) AS specialty_ids_csv
            FROM perfil c
            INNER JOIN auth a
                ON c.user_id = a.id
            WHERE c.user_id = ?
              AND a.deleted_at IS NULL
              AND a.role_id = 2
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $coach = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coach) {
            return false;
        }

        return $this->mapCoachSpecialties($coach);
    }

    // Guarda cambios del perfil coach.
    public function updateCoach(int $userId, array $data)
    {
        $startedTransaction = !$this->db->inTransaction();

        try {
            if ($startedTransaction) {
                $this->db->beginTransaction();
            }

            $sql = "
                UPDATE perfil c
                INNER JOIN auth a
                    ON c.user_id = a.id
                SET
                    c.first_name = ?,
                    c.last_name = ?,
                    c.phone = ?,
                    c.updated_at = NOW()
                WHERE c.user_id = ?
                  AND a.role_id = 2
            ";

            $stmt = $this->db->prepare($sql);
            $updated = $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $userId
            ]);

            if (!$updated) {
                throw new RuntimeException('No se pudo actualizar el coach.');
            }

            $profileId = $this->getCoachProfileIdByUserId($userId);
            if ($profileId <= 0) {
                throw new RuntimeException('No se encontro el perfil del coach.');
            }

            if (!$this->syncCoachSpecialties($profileId, $data['specialty_ids'] ?? [])) {
                throw new RuntimeException('No se pudieron actualizar las especialidades.');
            }

            if ($startedTransaction) {
                $this->db->commit();
            }

            return true;
        } catch (Throwable $e) {
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    // Lista las especialidades disponibles para usar en combos y vistas.
    public function getAllSpecialties(): array
    {
        $sql = "
            SELECT
                s.id,
                s.name,
                COUNT(DISTINCT a.id) AS coaches_count
            FROM specialties s
            LEFT JOIN perfil_specialty ps
                ON ps.specialty_id = s.id
            LEFT JOIN perfil c
                ON ps.profile_id = c.id
               AND c.deleted_at IS NULL
            LEFT JOIN auth a
                ON c.user_id = a.id
               AND a.role_id = 2
               AND a.deleted_at IS NULL
            GROUP BY s.id, s.name
            ORDER BY s.name ASC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae una especialidad puntual para editarla.
    public function getSpecialtyById(int $specialtyId)
    {
        $stmt = $this->db->prepare("SELECT id, name FROM specialties WHERE id = ? LIMIT 1");
        $stmt->execute([$specialtyId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Guarda una nueva especialidad.
    public function createSpecialty(string $name): bool
    {
        $stmt = $this->db->prepare("INSERT INTO specialties (name) VALUES (?)");

        return $stmt->execute([$name]);
    }

    // Revisa si ya existe una especialidad con ese nombre.
    public function specialtyNameExists(string $name, int $excludeId = 0): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM specialties
            WHERE LOWER(name) = LOWER(?)
        ";

        $params = [$name];

        if ($excludeId > 0) {
            $sql .= " AND id <> ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Actualiza el nombre de una especialidad.
    public function updateSpecialty(int $specialtyId, string $name): bool
    {
        $stmt = $this->db->prepare("UPDATE specialties SET name = ? WHERE id = ?");

        return $stmt->execute([$name, $specialtyId]);
    }

    // Borra una especialidad si no tiene profesores activos usando esa referencia.
    public function deleteSpecialty(int $specialtyId): bool
    {
        if ($this->countCoachesBySpecialty($specialtyId) > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM specialties WHERE id = ?");

        return $stmt->execute([$specialtyId]);
    }

    // Cuenta profesores activos ligados a una especialidad.
    public function countCoachesBySpecialty(int $specialtyId): int
    {
        $sql = "
            SELECT COUNT(DISTINCT a.id)
            FROM perfil_specialty ps
            INNER JOIN perfil c
                ON ps.profile_id = c.id
            INNER JOIN auth a
                ON c.user_id = a.id
            WHERE ps.specialty_id = ?
              AND c.deleted_at IS NULL
              AND a.deleted_at IS NULL
              AND a.role_id = 2
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$specialtyId]);

        return (int) $stmt->fetchColumn();
    }

    // Aplica baja logica sobre un coach.
    public function deactivateByUserId(int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            $profileId = $this->getCoachProfileIdByUserId($userId);
            if ($profileId <= 0) {
                throw new RuntimeException('No se encontro el perfil del coach.');
            }

            $deleteSpecialties = $this->db->prepare("DELETE FROM perfil_specialty WHERE profile_id = ?");
            if (!$deleteSpecialties->execute([$profileId])) {
                throw new RuntimeException('No se pudieron quitar las especialidades del coach.');
            }

            $sql = "
                UPDATE perfil c
                INNER JOIN auth a
                    ON c.user_id = a.id
                SET
                    c.deleted_at = NOW(),
                    c.updated_at = NOW()
                WHERE c.user_id = :user_id
                  AND c.deleted_at IS NULL
                  AND a.role_id = 2
            ";

            $stmt = $this->db->prepare($sql);
            $updated = $stmt->execute([
                ':user_id' => $userId
            ]);

            if (!$updated) {
                throw new RuntimeException('No se pudo dar de baja el coach.');
            }

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    // Revierte la baja logica de un coach.
    public function activateByUserId(int $userId): bool
    {
        try {
            $sql = "
                UPDATE perfil c
                INNER JOIN auth a
                    ON c.user_id = a.id
                SET
                    c.deleted_at = NULL,
                    c.updated_at = NOW()
                WHERE c.user_id = :user_id
                  AND c.deleted_at IS NOT NULL
                  AND a.role_id = 2
            ";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':user_id' => $userId
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    // Normaliza el resultado de especialidades para usarlo en vistas.
    private function mapCoachSpecialties(array $coach): array
    {
        $idsCsv = $coach['specialty_ids_csv'] ?? '';
        $coach['specialty_ids'] = $idsCsv !== ''
            ? array_map('intval', explode(',', $idsCsv))
            : [];

        $coach['specialty_names'] = $coach['specialty_names'] ?? '';

        return $coach;
    }

    // Busca el id de perfil interno del coach a partir del auth id.
    private function getCoachProfileIdByUserId(int $userId): int
    {
        $sql = "
            SELECT c.id
            FROM perfil c
            INNER JOIN auth a
                ON c.user_id = a.id
            WHERE c.user_id = ?
              AND a.role_id = 2
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    // Reemplaza las especialidades asignadas al coach por el nuevo set.
    private function syncCoachSpecialties(int $profileId, array $specialtyIds): bool
    {
        $specialtyIds = array_values(array_unique(array_filter(array_map('intval', $specialtyIds))));

        $deleteStmt = $this->db->prepare("DELETE FROM perfil_specialty WHERE profile_id = ?");
        if (!$deleteStmt->execute([$profileId])) {
            return false;
        }

        if (empty($specialtyIds)) {
            return true;
        }

        $insertStmt = $this->db->prepare("INSERT INTO perfil_specialty (profile_id, specialty_id) VALUES (?, ?)");

        foreach ($specialtyIds as $specialtyId) {
            if (!$insertStmt->execute([$profileId, $specialtyId])) {
                return false;
            }
        }

        return true;
    }
}
