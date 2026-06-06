<?php

class Coach {
    private $db;

    public function __construct($pdo){
        $this->db = $pdo;
    }

    /**
     * Obtiene profesores.
     *
     * @param bool $onlyActive
     * true  = trae solo profesores activos
     * false = trae activos y dados de baja
     */
    public function getAll(bool $onlyActive = false){
        $sql = "
            SELECT 
                c.*, 
                a.email
            FROM coaches c
            INNER JOIN auth a 
                ON c.user_id = a.id
            WHERE a.deleted_at IS NULL
        ";

        // Si solo se quieren activos, se filtran los que no tienen fecha de baja.
        if ($onlyActive) {
            $sql .= " AND c.deleted_at IS NULL";
        }

        $sql .= " ORDER BY c.id DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function createCoach(array $data){

$sql = "INSERT INTO coaches (user_id, first_name, last_name, phone, specialty)
VALUES (?, ?, ?, ?, ?)";

$stmt = $this->db->prepare($sql);
return $stmt -> execute([
$data['user_id'],
$data['first_name'],
$data['last_name'],
$data['phone'],
$data['specialty']
]);
}
   
    public function getCoachById(int $user_id){
        $sql = "
            SELECT 
                c.*, 
                a.email 
            FROM coaches c 
            INNER JOIN auth a 
                ON c.user_id = a.id 
            WHERE c.user_id = ? 
              AND a.deleted_at IS NULL
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function updateCoach(int $user_id, array $data){
        $sql = "
            UPDATE coaches 
            SET 
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                specialty = ?,
                updated_at = NOW()
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['specialty'],
            $user_id
        ]);
    }
}