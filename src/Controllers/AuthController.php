<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Actor; // ¡Necesitas este modelo para las uniones!

class AuthController extends Controller
{
    private $userModel;
    private $actorModel; // Nuevo
    
    public function __construct()
    {
        // Nota: No llamamos a parent::__construct() si este tiene checkAuth()
        $this->userModel = new User();
        $this->actorModel = new Actor(); // Inicializar
        // Es una buena práctica iniciar sesión para poder usar las funciones setFlashMessage
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ==========================
    // VISTAS
    // ==========================

    /**
     * Muestra la vista de login.
     * Acceso: /auth/login
     */
    public function login()
    {
        $this->renderSimple('auth/login');
    }

    // ==========================
    // PROCESOS DE AUTENTICACIÓN
    // ==========================

    /**
     * Procesa el intento de login (POST).
     * Acceso: /auth/process
     */
    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // 1. Buscar al Usuario Y su Actor asociado (incluyendo el rol)
        // Usaremos un nuevo método que obtiene datos de ambas tablas (ver sección 4).
        $userWithActor = $this->userModel->findUserWithActor($username);

        // 2. Verificar existencia, contraseña y si tiene un actor asociado
        if ($userWithActor && password_verify($password, $userWithActor->password)) {
            
            // Éxito: iniciar sesión
            session_regenerate_id(true); // Previene fijación de sesión

            // Guardar datos clave en la sesión.
            // Nota: Estos datos provienen de la consulta JOIN.
            $_SESSION['user_id']    = $userWithActor->id;         // ID del LOGIN
            $_SESSION['username']   = $userWithActor->username;
            $_SESSION['actor_id']   = $userWithActor->actor_id;   // ID del ACTOR (Perfil)
            $_SESSION['full_name']  = $userWithActor->full_name;  // Nombre completo (Útil)
            $_SESSION['role']       = $userWithActor->role;       // ROL (CRÍTICO para restricciones)
            
            // Redirigir al dashboard
            header('Location: ' . BASE_URL . '');
            exit;

        } else {
            // Falla: user o contraseña incorrectos
            $this->setFlashMessage('error', 'Credenciales incorrectas o el usuario no tiene un perfil de actor asignado.');
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * Cierra la sesión del user.
     * Acceso: /auth/logout
     */
    public function logout()
    {
        session_unset();    // Libera todas las variables de sesión
        session_destroy();  // Destruye la sesión
        
        // Redirige al login
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}