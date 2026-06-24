<?php

// Resuelve credenciales, sesión y tokens de recuperación.

class Auth {
    private $db;

    // Toma la conexión compartida del proyecto.
    public function __construct( $pdo ) {
        $this->db = $pdo;
    }

    // --- SECCIÓN: BÚSQUEDA E IDENTIFICACIÓN ---

    /**
    * Busca un usuario por email.
    * @return array|bool Retorna los datos del usuario o false si no existe.
    */
    public function findByEmail( $email ) {
        $stmt = $this->db->prepare( 'SELECT * FROM auth WHERE email = ? AND deleted_at IS NULL LIMIT 1' );
        $stmt->execute( [ $email ] );
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    // --- SECCIÓN: GESTIÓN DE CUENTA ---

    /**
     * Crea las credenciales de acceso para un nuevo usuario.
     *
     * @param array $data [
     *     'email' => string,
     *     'password' => string,
     *     'role_id' => int
     * ]
     *
     * @return int|false
     */
    // Inserta el usuario base en auth.
    public function create(array $data)
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $roleId = $data['role_id'] ?? 3;

        $sql = "
            INSERT INTO auth (
                email,
                password,
                role_id,
                created_at,
                updated_at,
                deleted_at
            ) VALUES (
                ?,
                ?,
                ?,
                NOW(),
                NOW(),
                NULL
            )
        ";

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute([
            $data['email'],
            $hash,
            $roleId
        ])) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
    * Valida las credenciales en el inicio de sesión.
    */
    // Busca el usuario y valida la contraseña.
    public function login( $email, $password ) {
     $sql = "SELECT a.*, 
            COALESCE(p.first_name, 'Admin') AS first_name,
            COALESCE(p.profile_image, 'default-profile.png') AS profile_image
        FROM auth a
        LEFT JOIN perfil p ON a.id = p.user_id AND p.deleted_at IS NULL
        WHERE a.email = ? AND a.deleted_at IS NULL 
        LIMIT 1";

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [ $email ] );
        $user = $stmt->fetch( PDO::FETCH_ASSOC );

        if ( $user && password_verify( $password, $user[ 'password' ] ) ) {
            return $user;
        }

        return false;
    }

    /**
    * Actualiza la contraseña de un usuario mediante su email.
    */
    // Actualiza la contraseña usando el email como referencia.
    public function updatePasswordByEmail( $email, $hashedPassword ) {
        $stmt = $this->db->prepare( 'UPDATE auth SET password = ? WHERE email = ?' );
        return $stmt->execute( [ $hashedPassword, $email ] );
    }

    // --- SECCIÓN: RECUPERACIÓN DE CONTRASEÑA ( TOKENS ) ---

    /**
    * Guarda un token de recuperación, eliminando cualquier token previo del mismo email.
    */
    public function savePasswordToken( $email, $token, $expires ) {
        try {
            // 1. Limpiamos registros de recuperación antiguos para este usuario
            $stmtDel = $this->db->prepare( 'DELETE FROM password_resets WHERE email = ?' );
            $stmtDel->execute( [ $email ] );

            // 2. Insertamos el nuevo token de seguridad
            $stmtIns = $this->db->prepare( 'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)' );
            return $stmtIns->execute( [ $email, $token, $expires ] );

        } catch ( PDOException $e ) {
            error_log( 'Error en savePasswordToken: ' . $e->getMessage() );
            return false;
        }
    }

    /**
    * Valida si un token existe y no ha expirado.
    */
    // Verifica si el token todavía sirve.
    public function validateToken( $token ) {
        $stmt = $this->db->prepare( 'SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1' );
        $stmt->execute( [ $token ] );
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    /**
    * Elimina el token una vez que ya ha sido utilizado.
    */
    // Borra el token después de usarlo.
    public function deleteToken( $token ) {
        $stmt = $this->db->prepare( 'DELETE FROM password_resets WHERE token = ?' );
        return $stmt->execute( [ $token ] );
    }
}
