<?php
// Detecta si estás en localhost o en un servidor
$isLocalhost = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

// Define la ruta base según el entorno
if ($isLocalhost) {
    define('BASE_URL', '/foodtrack/public/');
} else {
    // Ajusta esto según el dominio real en producción
    define('BASE_URL', '/');
}

// Ruta absoluta del sistema en el servidor
define('ROOT_PATH', realpath(dirname(__FILE__)) . '/');

// Puedes agregar otras constantes útiles aquí
define('ASSETS_PATH', BASE_URL . 'assets/');
define('CSS_PATH', BASE_URL . 'css/');
define('JS_PATH', BASE_URL . 'js/');
define('IMG_PATH', BASE_URL . 'images/');
define('VENDOR_PATH', BASE_URL . 'vendors/');
?>
