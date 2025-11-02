<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        // Nota: No llamamos a parent::__construct() si este tiene checkAuth()
        // Este controlador debe ser la excepción para permitir acceso sin login.
        $this->userModel = new User();
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
        // Render simple: no incluye header/footer del layout principal
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
            header('Location: /foodtrack/public/auth/login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // 1. Buscar al user por username
        $user = $this->userModel->findByUsername($username);

        // 2. Verificar si el user existe y la contraseña coincide
        if ($user && password_verify($password, $user->password)) {
            
            // Éxito: iniciar sesión
            session_regenerate_id(true); // Previene fijación de sesión

            // Guardar datos clave en la sesión
            $_SESSION['user_id']  = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role']     = $user->role;
            
            // Redirigir al dashboard (o lista de soportes)
            header('Location: /foodtrack/public/');
            exit;

        } else {
            // Falla: user o contraseña incorrectos
            $this->setFlashMessage('error', 'Incorrect credentials. Please try again.');
            header('Location: /foodtrack/public/auth/login');
            exit;
        }
    }

    /**
     * Cierra la sesión del user.
     * Acceso: /auth/logout
     */
    public function logout()
    {
        session_unset();   // Libera todas las variables de sesión
        session_destroy(); // Destruye la sesión
        
        // Redirige al login
        header('Location: /foodtrack/public/auth/login');
        exit;
    }
}
