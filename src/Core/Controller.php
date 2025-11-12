<?php
namespace App\Core;

abstract class Controller {

    public function __construct() {
        // ¡Magia! Verificamos la clase que se está instanciando
        // Si NO es el AuthController, entonces protegemos la ruta.
        if (get_class($this) !== 'App\Controllers\AuthController') {
            $this->checkAuth();
        }
    }

    /**
     * Verifica si el user está autenticado.
     * Si no, lo redirige al login.
     */
    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Si no hay sesión activa, redirigir al login
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * Restringe el acceso solo a ciertos niveles (roles).
     * @param array $roles Permitidos (ej: ['admin', 'inspector'])
     */
    protected function restrictTo(array $roles) {
        // checkAuth() ya debió ser llamado antes
        if (!in_array($_SESSION['role'], $roles)) {
            // Error 403 - Prohibido
            http_response_code(403);
            // Mostramos una vista de error simple
            $this->render('error/403', ['title' => 'Access Denied']);
            exit;
        }
    }
    
    /**
     * MÉTODO CLAVE: Renderiza una vista con el layout (header/footer).
     * Este es el que está faltando o mal nombrado.
     * @param string $view El nombre del archivo de la vista (ej: 'tickets/list')
     * @param array $data Datos para la vista
     */
    protected function render(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            // Asegúrate de que estos archivos de layout existen
            require_once __DIR__ . '/../Views/layout/header.php';
            require_once __DIR__ . '/../Views/layout/left-side-menu.php';
            require_once $viewFile;
            require_once __DIR__ . '/../Views/layout/right-side-menu.php';
            require_once __DIR__ . '/../Views/layout/footer.php';
        } else {
            die("Error: View '$view' not found.");
        }
    }
    
    /**
     * (Opcional) Renderiza una vista sin el layout (para el login).
     */
    protected function renderSimple(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Error: View '$view' not found.");
        }
    }
    
    /**
     * Establece un mensaje flash en la sesión.
     * @param string $type ('success', 'error', 'info')
     * @param string $message El texto a mostrar
     */
    protected function setFlashMessage(string $type, string $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}
