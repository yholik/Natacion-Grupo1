<?php

// Punto de entrada de la aplicación web.
session_start();
require_once __DIR__ . '/../app/core/Env.php';

// index.php
try {
    Env::load( __DIR__ . '/../.env' );
    date_default_timezone_set(Env::get('APP_TIMEZONE','UTC'));


} catch ( Exception $e ) {
    die( 'Error crítico: ' . $e->getMessage() );
}

// Ahora sí, cargamos la base de datos
require_once __DIR__ . '/../app/config/db.php';

// Punto de entrada único ( Single Entry Point )
require_once __DIR__ . '/router.php';
