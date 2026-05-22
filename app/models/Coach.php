<?php

class Coach {
    private $db;

    public function __construct($pdo){
        this->db = $pdo;

    }


public function getAllCoaches(){
    $sql = "SELECT c.* , u.email
    FROM coaches c
    INNER JOIN users u ON c.user_id = c.id
    WHERE c.deleted_at IS NULL";

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

$sql = "SELECT c.*, u.email 
             FROM coaches c 
             INNER JOIN users u ON c.user_id = u.id 
             WHERE c.user_id = ? AND c.deleted_at IS NULL 
             LIMIT 1"
;

$stmt = $this->db->prepare($sql);
$stmt->execute([$user_id]);
return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function updateCoach(int $user_id, array $data){
    $sql = "UPDATE coaches SET first_name = ?, last_name = ?, phone = ?, specialty = ? WHERE user_id = ? AND deleted_at IS NULL";
   
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