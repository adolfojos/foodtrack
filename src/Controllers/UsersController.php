<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use PDO; // No es estrictamente necesario, pero buena práctica si el modelo lo usa

class UsersController extends Controller {

    private $userModel;
    // Definimos los roles permitidos en el sistema
    private $allowedRoles = ['admin', 'inspector', 'consultant']; 

    public function __construct() {
        parent::__construct(); 
        
        $this->userModel = new User();
        
        // **RESTRICCIÓN GLOBAL:** Solo el administrador puede gestionar users.
        $this->restrictTo(['admin']); 
    }

    /**
     * Muestra la lista de todos los users.
     * Acceso: /users
     */
    public function index() {
        // En el modelo base, findAll() devuelve todos los objetos.
        $users = $this->userModel->findAll(); 

        $this->render('users/list', [
            'title' => 'User and role management',
            'users' => $users
        ]);
    }

    /**
     * Muestra el formulario para create un nuevo user o edit uno existente.
     * Acceso: /users/create | /users/edit/{id}
     */
    public function create(int $id = null) {
        $user = null;
        if ($id) {
            $user = $this->userModel->findById($id);
            if (!$user) {
                die("User not found.");
            }
        }
        
        $this->render('users/form', [
            'title' => ($id ? 'Edit' : 'Create new') . ' User',
            'user' => $user,
            'allowedRoles' => $this->allowedRoles // Pasamos los roles al formulario
        ]);
    }
    
    // Simplificamos la edición llamando a create con ID
    public function edit(int $id) {
        $this->create($id);
    }

    /**
     * Procesa el formulario de creación/edición (POST).
     * Acceso: /users/save
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT); // Sin sanitizar, se va a hashear.
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($username) || !in_array($role, $this->allowedRoles)) {
                die("Error: Invalid username or role.");
            }

            $data = [
                'username' => $username,
                'role' => $role
            ];

            if ($id) {
                // ACTUALIZAR
                if (!empty($password)) {
                    // Solo actualizamos la contraseña si se proporciona una nueva
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $this->userModel->update($id, $data);
                
            } else {
                // CREAR
                if (empty($password)) {
                    die("Error: Password is required to create a new user.");
                }
                // Hashing de la contraseña antes de guardarla
                $data['password'] = password_hash($password, PASSWORD_DEFAULT); 
                $this->userModel->create($data);
            }
            
            header('Location: /foodtrack/public/users');
            exit;
        }
        header('Location: /foodtrack/public/users/create');
        exit;
    }

    /**
     * Elimina un user.
     * Acceso: /users/delete/{id}
     */
    public function delete(int $id) {
        // En un sistema real, se debería validar que el user no tenga tickets asignados.
        // Aquí solo evitamos que se elimine el user principal (ID 1), por seguridad mínima.
        if ($id == 1) { 
            die("Cannot delete the main administrator user (ID 1).");
        }
        
        $this->userModel->delete($id);
        
        header('Location: /foodtrack/public/users');
        exit;
    }
}
