<?php
// ---- INICIO DE CÓDIGO DE DEPURACIÓN ----
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---- FIN DE CÓDIGO DE DEPURACIÓN ----
// Iniciar sesión
session_start();
require_once __DIR__ . '/../config/config.php';

// Cargar el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar un enrutador simple (lo crearemos a continuación)
// O podrías usar una librería como FastRoute si quieres algo más potente

use App\Core\Router; // (Suponiendo que creamos un Router)

// Obtener la URL solicitada
$url = $_GET['url'] ?? '/';

// (Aquí iría la lógica del enrutador)
// Por simplicidad, vamos a hacer un enrutador muy básico aquí mismo:

$urlParts = explode('/', trim($url, '/'));

// Controlador por defecto
$controllerName = $urlParts[0] ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$controllerFile = __DIR__ . '/../src/Controllers/' . $controllerName . '.php';

// Método por defecto
$methodName = $urlParts[1] ?? 'index';

// Parámetros
$params = array_slice($urlParts, 2);

// Construir el nombre completo de la clase
$controllerClass = 'App\\Controllers\\' . $controllerName;

if (file_exists($controllerFile)) {
    // Incluimos el autoloader, así que no necesitamos require
    // require_once $controllerFile;

    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();

        if (method_exists($controller, $methodName)) {
            // Llamar al método pasando los parámetros
            call_user_func_array([$controller, $methodName], $params);
        } else {
            // Error 404 - Método no encontrado
            echo "Error 404: Method not found.";
        }
    } else {
        // Error 404 - Clase no encontrada
        echo "Error 404: Controller class not found.";
    }
} else {
    // Error 404 - Archivo no encontrado
    echo "Error 404: Controller file not found.";
}
