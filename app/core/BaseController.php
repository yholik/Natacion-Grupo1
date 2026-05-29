<?php
// app/controllers/BaseController.php

class BaseController {

    public function __construct() {
        // Iniciamos sesión en el constructor base para que esté disponible en todos lados
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }
    }

    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Si es una petición Fetch, mandamos JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->json('error', 'Sesión expirada', '?url=login');
            } else {
                // Si es carga de página normal, redirección directa
                header('Location: ?url=login');
                exit;
            }
        }
    }


    //NOVEDAD: Porque sino el usuario podria ingresar mediante URL
    // a rutas de admin o de coach
    protected function checkRole(int $roleId){
        if((int)$_SESSION['role_id'] != $roleId){
            header('Location: ?url=login');
            exit;
        }
    }
    
    /**
    * @param string $view  Nombre del archivo ( ej: 'usuarios/register' )
    * @param array  $data  Diccionario de datos para la vista
    */
    protected function render( $view, $data = [] ){
        extract($data);

        //Al realizar este cambio, tanto en swimmer.home-view,php como coach.home-view.php
        // deberíamos quitar los include al header y footer.
        //Capturamos la vista
        ob_start();
        require_once __DIR__ . '/../views/'. $view . '.php';
        $contenido = ob_get_clean();
        //Inyectamos en layout (main)
        require_once __DIR__ . '/../views/users/layout/main.php';
    }


    protected function json( $status, $message, $redirect = null ) {
        header( 'Content-Type: application/json' );
        echo json_encode( [
            'status'   => $status,
            'message'  => $message,
            'redirect' => $redirect ?? Env::get('APP_URL') // Sin redirect, va al home
        ] );
        exit;
        // Importante para cortar la ejecución aquí
    }
}