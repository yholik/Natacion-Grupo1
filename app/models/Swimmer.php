<?php

class Swimmer {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todos los nadadores con sus correos electrónicos e imagen de perfil.
     */
    public function getAll() {
        // Agregamos s.profile_image a la consulta
        $sql = "SELECT s.*, u.email 
                FROM swimmers s 
                INNER JOIN auth u ON s.user_id = u.id 
                WHERE s.deleted_at IS NULL";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta los datos personales vinculados a un user_id, incluyendo la imagen.
     * @param array $data ['user_id', 'first_name', 'last_name', 'phone', 'profile_image']
     */
    public function create(array $data) {
        // Agregamos profile_image al INSERT
        $sql = "INSERT INTO swimmers (user_id, first_name, last_name, phone, profile_image) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['user_id'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            // Si no viene imagen, podemos pasar un null o el nombre por defecto
            $data['profile_image'] ?? 'default-profile.png' 
        ]);
    }

    public function getSwimmerById(int $user_id) {

        $sql = "SELECT s.*, u.email 
                    FROM swimmers s 
                    INNER JOIN auth u ON s.user_id = u.id 
                    WHERE s.user_id = ? AND s.deleted_at IS NULL 
                    LIMIT 1"
        ;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSwimmer(int $userId, array $data) {
        $fields = [];
        $values = [];
        foreach (['first_name', 'last_name', 'phone', 'birth_date', 'profile_image'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $values[] = $data[$col];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $values[] = $userId;
        $sql = "UPDATE swimmers SET " . implode(', ', $fields) . " WHERE user_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
}