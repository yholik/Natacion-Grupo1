<?php
session_start();
require_once __DIR__ . '/../app/core/Env.php';

// index.php
try {
    Env::load( __DIR__ . '/../.env' );

    // 'Punteamos' la variable del .env a una constante global de PHP
    // Esto asegura que BASE_URL NUNCA termine en barra, sin importar cómo esté en el .env
    $envUrl = Env::get( 'APP_URL' ) ?: 'http://localhost/gestion-natacion';

    // Definimos la constante asegurando que no tenga barra al final
    define( '_URL', rtrim( $envUrl, '/' ) );
    date_default_timezone_set(Env::get('APP_TIMEZONE','UTC'));


} catch ( Exception $e ) {
    die( 'Error crítico: ' . $e->getMessage() );
}

// Ahora sí, cargamos la base de datos
require_once __DIR__ . '/../app/config/db.php';

// Punto de entrada único ( Single Entry Point )
require_once __DIR__ . '/router.php';