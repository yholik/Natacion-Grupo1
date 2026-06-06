<?php

/**
* EL ENRUTADOR ( ROUTER ) - Front Controller Pattern
* * Este archivo es el único punto de entrada a la lógica del servidor.
* Su función es leer la intención del usuario ( vía URL ) y delegar el
* trabajo al controlador correspondiente.
*/

// Cargamos el núcleo del sistema una sola vez
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/Env.php';
require_once __DIR__ . '/../app/core/BaseController.php';

/**
* 1. CAPTURA DE LA INTENCIÓN
* Usamos el parámetro 'url' definido en el .htaccess o pasado por GET.
* Si no hay ruta ( página de inicio ), por defecto vamos a 'home'.
*/
$route = $_GET[ 'url' ] ?? 'landing';

/**
* 2. DESPACHO DE RUTAS ( DISPATCHER )
* El switch actúa como una tabla de decisiones.
*/
switch ( $route ) {

    // --- VISTA PÚBLICA DE BIENVENIDA ---
    case 'landing':
        //Aquí mostramos el landing previo al inicio de sesión
        require_once __DIR__ .'/../app/controllers/HomeController.php';
        ( new HomeController() )->landing(); //
        break;

    // --- VISTA PRINCIPAL ---
    case 'home':
    // Aquí mostramos el panel principal ( dashboard ) con la lista de nadadores
    require_once __DIR__ . '/../app/controllers/HomeController.php';
    ( new HomeController() )->index();
    break;

    // --- MÓDULO DE USUARIOS Y AUTENTICACIÓN ---
    // Agrupamos rutas relacionadas para evitar repetir el require_once
    case 'login':
    case 'authenticate':
    case 'register':
    case 'forgot-password':
    case 'send-reset':
    case 'reset-password':
    case 'update-password':
    require_once __DIR__ . '/../app/controllers/AuthController.php';
    $controller = new AuthController();

    /**
    * Ejecución del método según la acción solicitada.
    * Separamos la visualización ( GET ) de la lógica de procesamiento ( POST ).
    */
    if ( $route === 'login' )           $controller->showLogin();
    if ( $route === 'authenticate' )    $controller->authenticate();
    if ( $route === 'register' )        $controller->register();
    if ( $route === 'forgot-password' ) $controller->forgotPassword();
    if ( $route === 'send-reset' )      $controller->sendReset();
    if ( $route === 'reset-password' )  $controller->showResetForm();
    if ( $route === 'update-password' ) $controller->updatePassword();
    break;

    // --- COACH ---
    case 'coach':   
    case 'coach-home':
    case 'coach-profile':
    case 'coach-update-profile':
    
        require_once __DIR__ . '/../app/controllers/CoachController.php';
        $controller = new CoachController();         
        if ( $route === 'coach' )   $controller->showCoachHome();
        if ( $route === 'coach-home' ) $controller->showCoachHome();
        if ( $route === 'coach-profile' ) $controller->showCoachProfile();
        if ( $route === 'coach-update-profile' ) $controller->updateProfileCoach();
        break;

    /// --- SWIMMER ---
    case 'swimmer':  
    case 'swimmer-classes-avaliable':
    case 'swimmer-my-classes':
    case 'swimmer-profile':
    case 'swimmer-update-profile':
    case 'swimmer-enroll':
    case 'swimmer-cancel-enrollment':
        require_once __DIR__ . '/../app/controllers/SwimmerController.php';
        $controller = new SwimmerController();         

        if ( $route === 'swimmer' )                     $controller->showAvaliableClasses();
        if ( $route === 'swimmer-classes-avaliable' )   $controller->showAvaliableClasses();
        if ( $route === 'swimmer-my-classes' )          $controller->showMyClasses();
        if ( $route === 'swimmer-profile' )             $controller->showProfile();
        if ( $route === 'swimmer-update-profile' )      $controller->updateProfile();
        if ( $route === 'swimmer-enroll' )              $controller->enroll();
        if ( $route === 'swimmer-cancel-enrollment' )   $controller->cancelEnrollment();
    break;

    // --- ADMIN ---
    case 'admin':   
    case 'admin-home':
    case 'admin-manage-coaches':
    case 'admin-create-coach':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();         
        if ( $route === 'admin' )   $controller->showAdminHome();
        if( $route === 'admin-home' ) $controller->showAdminHome();
        if( $route === 'admin-manage-coaches' ) $controller->showAdminManageCoaches();
        if( $route === 'admin-create-coach' ) $controller->createCoach();
    break;



    // --- SEGURIDAD: CIERRE DE SESIÓN ---
    case 'logout':
    /**
    * Para destruir una sesión, primero debemos estar seguros de que
    * el sistema sabe de su existencia ( iniciada previamente en index.php ).
    */
    $_SESSION = [];
    // Vaciamos el array de sesión por seguridad
    session_destroy();
    // Eliminamos el archivo de sesión en el servidor

    // Redirigimos al Login para forzar una nueva autenticación
    header( 'Location: ?url=login' );
    exit;
    // Detenemos el script para asegurar la redirección

    // --- MANEJO DE ERRORES ---
    default:
    /**
    * Si el usuario intenta acceder a una ruta que no definimos arriba,
    * devolvemos un código de estado 404 ( Not Found ).
    */
    http_response_code( 404 );
    echo 'Error 404: La página "' . htmlspecialchars( $route ) . '" no existe en este sistema.';
    break;
}