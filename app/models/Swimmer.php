<?php

// Expone las consultas de perfil para usuarios con rol swimmer.

class Swimmer {
    private $db;

    // Toma la conexión compartida.
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todos los nadadores con sus correos electrónicos e imagen de perfil.
     */
    public function getAll(bool $onlyActive = true) {
        $sql = "SELECT s.*, u.email 
                FROM perfil s 
                INNER JOIN auth u ON s.user_id = u.id 
                WHERE u.deleted_at IS NULL
                  AND u.role_id = 3";

        if ($onlyActive) {
            $sql .= " AND s.deleted_at IS NULL";
        }

        $sql .= " ORDER BY s.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta los datos personales vinculados a un user_id, incluyendo la imagen.
     * @param array $data ['user_id', 'first_name', 'last_name', 'phone', 'profile_image']
     */
    public function create(array $data) {
        $sql = "INSERT INTO perfil (user_id, first_name, last_name, phone, birth_date, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['user_id'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['birth_date'] ?? null,
            // Si no viene imagen, podemos pasar un null o el nombre por defecto
            $data['profile_image'] ?? 'default-profile.png' 
        ]);
    }

    // Busca el perfil swimmer asociado al auth logueado.
    public function getSwimmerById(int $user_id) {

        $sql = "SELECT s.*, u.email 
                    FROM perfil s 
                    INNER JOIN auth u ON s.user_id = u.id 
                    WHERE s.user_id = ? AND s.deleted_at IS NULL 
                    AND u.role_id = 3
                    LIMIT 1"
        ;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Busca un nadador por auth id para el panel admin.
    public function getSwimmerByUserId(int $userId, bool $onlyActive = true) {
        $sql = "SELECT s.*, u.email 
                FROM perfil s 
                INNER JOIN auth u ON s.user_id = u.id 
                WHERE s.user_id = ? 
                  AND u.deleted_at IS NULL
                  AND u.role_id = 3";

        if ($onlyActive) {
            $sql .= " AND s.deleted_at IS NULL";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Guarda cambios del perfil propio del nadador.
    public function updateSwimmer(int $userId, array $data) {
        $fields = [];
        $values = [];
        foreach (['first_name', 'last_name', 'phone', 'birth_date', 'profile_image'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "s.$col = ?";
                $values[] = $data[$col];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $values[] = $userId;
        $sql = "UPDATE perfil s
                INNER JOIN auth u ON s.user_id = u.id
                SET " . implode(', ', $fields) . "
                WHERE s.user_id = ?
                  AND s.deleted_at IS NULL
                  AND u.role_id = 3";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    // Guarda cambios del perfil swimmer desde admin.
    public function updateAdminSwimmer(int $userId, array $data) {
        $fields = [];
        $values = [];

        foreach (['first_name', 'last_name', 'phone', 'birth_date', 'profile_image'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "s.$col = ?";
                $values[] = $data[$col];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = "s.updated_at = NOW()";
        $values[] = $userId;

        $sql = "UPDATE perfil s
                INNER JOIN auth u ON s.user_id = u.id
                SET " . implode(', ', $fields) . "
                WHERE s.user_id = ?
                  AND u.role_id = 3";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($values);
    }

    // Aplica baja lógica sobre un nadador.
    public function deactivateByUserId(int $userId): bool
    {
        try {
            $sql = "
                UPDATE perfil s
                INNER JOIN auth u ON s.user_id = u.id
                SET 
                    s.deleted_at = NOW(),
                    s.updated_at = NOW()
                WHERE s.user_id = :user_id
                AND s.deleted_at IS NULL
                AND u.role_id = 3
            ";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':user_id' => $userId
            ]);

        } catch (Throwable $e) {
            return false;
        }
    }

    // Revierte la baja lógica de un nadador.
    public function activateByUserId(int $userId): bool
    {
        try {
            $sql = "
                UPDATE perfil s
                INNER JOIN auth u ON s.user_id = u.id
                SET 
                    s.deleted_at = NULL,
                    s.updated_at = NOW()
                WHERE s.user_id = :user_id
                AND s.deleted_at IS NOT NULL
                AND u.role_id = 3
            ";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':user_id' => $userId
            ]);

        } catch (Throwable $e) {
            return false;
        }
    }
}
