<?php
// app/controllers/BaseController.php

class BaseController
{

    public function __construct()
    {
        // Iniciamos sesión en el constructor base para que esté disponible en todos lados
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function checkAuth()
    {
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
    protected function checkRole(int $roleId)
    {
        if ((int) $_SESSION['role_id'] != $roleId) {
            header('Location: ?url=login');
            exit;
        }
    }

    /**
     * @param string $view  Nombre del archivo ( ej: 'usuarios/register' )
     * @param array  $data  Diccionario de datos para la vista
    */
    protected function render($view, $data = [])
    {
        extract($data); // convierto las claves del array en variables

        $requestedRute = __DIR__ . '/../views/' . $view . '.php'; // armo la ruta completa de la vista solicitada


        // primero pregunto si existe la vista solicitada para evitar
        // que cargue vistas viejas

        if (file_exists(($requestedRute))) {
            
//DESCENTRALIZO ARCHIVO MAIN.PHP PORQUE NO SE ESTA UTILIZANDO DE LA MANERA ADECUADA
//NOS QUEDA HEADERS RIGIDOS SOLO PARA QUE EL LANDING SE VEA BIEN, NO ES FUNCIONAL
//ASI QUE INYECTAMOS LA VISTA SOLICITADA DIRECTAMENTE, SI SE QUIERE UN LAYOUT COMUN, 
// SE HACE DESDE LA VISTA MISMA CON UN INCLUDE
//EN CASO DE NO EXISTIR LA VISTA, MANEJAMOS EL CASO CON UN FALBACK SEGUN EL USUARIO LOGUEADO
            
            require_once $requestedRute;         

          
        } else {

            $this->handleFallback(); // Si la vista no existe, manejo el caso segun el usuario de la sesion
        }

    }




    protected function handleFallback() // necesitamos esto para que el fallback sea acertado segun el usuario
    {
       $userRole = $_SESSION['role_id'] ?? null; // capturo el tipo de rol

 
        
        switch ($userRole) {
            case 1: //aca va admin
                header('Location: ?url=admin-home');
                exit;
            case 2: //aca va coach
                header('Location: ?url=coach-home');
                exit;
            case 3: //swimmer
                header('Location: ?url=swimmer-my-classes');
                exit;
            default:
                header('Location: ?url=login');
                exit;
        }

    }


    protected function json($status, $message, $redirect = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'redirect' => $redirect ?? Env::get('APP_URL') // Sin redirect, va al home
        ]);
        exit;
        // Importante para cortar la ejecución aquí
    }
}