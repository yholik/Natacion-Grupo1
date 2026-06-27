<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/Env.php';
require_once __DIR__ . '/../app/core/BaseController.php';


$route = $_GET[ 'url' ] ?? 'landing';

switch ( $route ) {

/*====================================*/
/*           RUTAS PUBLICAS           */
/*====================================*/
    case 'landing':        
        require_once __DIR__ .'/../app/controllers/HomeController.php';
        ( new HomeController() )->landing(); //
        break;
    
    case 'home':    
    require_once __DIR__ . '/../app/controllers/HomeController.php';
    ( new HomeController() )->index();
    break;


/*====================================*/
/*           AUTH & USERS          */
/*====================================*/   
    case 'login':
    case 'authenticate':
    case 'register':
    case 'forgot-password':
    case 'send-reset':
    case 'reset-password':
    case 'update-password':
    require_once __DIR__ . '/../app/controllers/AuthController.php';
    $controller = new AuthController();

    if ( $route === 'login' )           $controller->showLogin();
    if ( $route === 'authenticate' )    $controller->authenticate();
    if ( $route === 'register' )        $controller->register();
    if ( $route === 'forgot-password' ) $controller->forgotPassword();
    if ( $route === 'send-reset' )      $controller->sendReset();
    if ( $route === 'reset-password' )  $controller->showResetForm();
    if ( $route === 'update-password' ) $controller->updatePassword();
    break;


/*====================================*/
/*           RUTAS DE PROFILE         */
/*====================================*/
    case 'profile':
    case 'profile-update':
    case 'coach-profile':
    case 'coach-update-profile':
    case 'swimmer-profile':
    case 'swimmer-update-profile':
    case 'update-profile-credentials':
        require_once __DIR__ . '/../app/controllers/PerfilController.php';
        $controller = new PerfilController();

        if ($route === 'profile' || $route === 'coach-profile' || $route === 'swimmer-profile') $controller->showProfile();
        if ($route === 'profile-update' || $route === 'coach-update-profile' || $route === 'swimmer-update-profile') $controller->updateProfile();
        if($route === 'update-profile-credentials') $controller->updatePassword();
        break;

    

/*====================================*/
/*           RUTAS DE COACH           */
/*====================================*/
    case 'coach':   
    case 'coach-home':
    case 'coach-lessons':
    case 'coach-calendar':
    case 'admin-manage-specialties':
    case 'admin-create-specialty':
    case 'admin-edit-specialty':
    case 'admin-delete-specialty':
    case 'coach-stats':
    case 'coach-get-specialties':
    case 'coach-get-students':
    
        require_once __DIR__ . '/../app/controllers/CoachController.php';
        $controller = new CoachController();         
        if ( $route === 'coach' )   $controller->showCoachHome();
        if ( $route === 'coach-home' ) $controller->showCoachHome();
        if ( $route === 'coach-lessons' ) $controller->showCoachLessons();
        if ( $route === 'coach-calendar' ) $controller->showCoachCalendar();
        if($route === 'coach-stats' ) $controller->getCoachStats();
        if($route === 'coach-get-specialties') $controller->getSpecialtiesJSON();
        if($route === 'coach-get-students' ) $controller->getCoachStudents();
        

        //especialidades
        if ( $route === 'admin-manage-specialties' ) $controller->showAllEspecialidades();
        if ( $route === 'admin-create-specialty' ) $controller->createSpecialty();
        if ( $route === 'admin-edit-specialty' ) $controller->updateSpecialty();
        if ( $route === 'admin-delete-specialty' ) $controller->deleteSpecialty();
        break;

  

/*====================================*/
/*           RUTAS DE SWIMMER         */
/*====================================*/
    case 'swimmer':  
    case 'swimmer-classes-avaliable':
    case 'swimmer-my-classes':
    case 'swimmer-enroll':
    case 'swimmer-cancel-enrollment':      

        require_once __DIR__ . '/../app/controllers/SwimmerController.php';
        $controller = new SwimmerController();         

        if ( $route === 'swimmer' )                     $controller->showAvaliableClasses();
        if ( $route === 'swimmer-classes-avaliable' )   $controller->showAvaliableClasses();
        if ( $route === 'swimmer-my-classes' )          $controller->showMyClasses();
        if ( $route === 'swimmer-enroll' )              $controller->enroll();
        if ( $route === 'swimmer-cancel-enrollment' )   $controller->cancelEnrollment();
    break;
case 'swimmer-calendar':
        header('Location: ?url=swimmer-my-classes');
        exit;
  

/*====================================*/
/*           RUTAS DE ADMIN           */
/*====================================*/

    case 'admin':
    case 'admin-home':
    case 'admin-manage-coaches':
    case 'admin-manage-swimmers':
    case 'admin-edit-coach':
    case 'admin-edit-swimmer':
    case 'admin-create-coach':
    case 'admin-create-swimmer':
    case 'admin-deactivate-coach':
    case 'admin-deactivate-swimmer':
    case 'admin-activate-coach':
    case 'admin-activate-swimmer':
    case 'admin-manage-lessons':
    case 'admin-create-lesson':
    case 'admin-edit-lesson':
    case 'admin-delete-lesson':
    case 'admin-get-coach-specialties':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        $controller = new AdminController();

        if ($route === 'admin') { $controller->showAdminHome(); }
        if ($route === 'admin-home') { $controller->showAdminHome(); }
        if ($route === 'admin-manage-coaches') { $controller->showAdminManageCoaches(); }
        if ($route === 'admin-manage-swimmers') { $controller->showAdminManageSwimmers(); }
        if ($route === 'admin-create-coach') { $controller->createCoach(); }
        if ($route === 'admin-create-swimmer') { $controller->createSwimmer(); }
        if ($route === 'admin-edit-coach') { $controller->editCoach(); }
        if ($route === 'admin-edit-swimmer') { $controller->editSwimmer(); }
        if ($route === 'admin-deactivate-coach') { $controller->deactivateCoach(); }
        if ($route === 'admin-deactivate-swimmer') { $controller->deactivateSwimmer(); }
        if ($route === 'admin-activate-coach') { $controller->activateCoach(); }
        if ($route === 'admin-activate-swimmer') { $controller->activateSwimmer(); }
        if($route === 'admin-manage-lessons') { $controller->showAdminManageLessons(); }
        if($route === 'admin-create-lesson') { $controller->createLesson(); }
        if($route === 'admin-edit-lesson') { $controller->editLesson(); }
        if($route === 'admin-delete-lesson') { $controller->deleteLesson(); }
        if($route === 'admin-get-coach-specialties') { $controller->getCoachSpecialties(); }

        break;



/*====================================*/
/*          SECURITY/LOGOUT           */
/*====================================*/
    case 'logout':

    $_SESSION = []; 
    session_destroy(); 
    header( 'Location: ?url=login' );
    exit;

    default:
    http_response_code( 404 );
    echo 'Error 404: La página "' . htmlspecialchars( $route ) . '" no existe en este sistema.';
    break;
}
