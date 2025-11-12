<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actor;
use App\Models\Institution;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;

class ActorsController extends Controller
{
    private $actorModel;
    private $institutionModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin']);
        $this->actorModel = new Actor();
        $this->institutionModel = new Institution();
        $this->userModel = new User();
    }

    public function index()
    {
        $actors = $this->actorModel->findAllWithRelations();
        $this->render('actors/list', [
            'title' => 'Gestión de Actores',
            'actors' => $actors
        ]);
    }


public function detail(int $id)
{
    $actor = $this->actorModel->findByIdWithRelations($id);

    if (!$actor) {
        $this->setFlashMessage('error', 'Actor no encontrado.');
        header('Location: ' . BASE_URL . 'actors');
        exit;
    }

    $this->render('actors/detail', [
        'title' => 'Detalle de Actor',
        'actor' => $actor
    ]);
}
    public function create(int $id = null)
    {
        $actor = null;
        if ($id) {
            $actor = $this->actorModel->findByIdWithRelations($id);
            if (!$actor) {
                die("Actor no encontrado.");
            }
        }

        $institutions = $this->institutionModel->findAllActive();

        // <-- CORRECCIÓN: Obtenemos el user_id de forma segura sin asumir que $actor existe
        $currentUserId = $actor ? $actor->user_id : null;
        $users = $this->userModel->findUnassignedUsers($currentUserId);

        $this->render('actors/form', [
            'title' => $id ? 'Editar Actor' : 'Nuevo Actor',
            'actor' => $actor,
            'institutions' => $institutions,
            'users' => $users
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            // <-- SUGERENCIA: Bloque de validación en el servidor
            $errors = [];
            if (empty($_POST['full_name'])) {
                $errors[] = 'El nombre del actor es obligatorio.';
            }
            if (empty($_POST['role'])) {
                $errors[] = 'El rol es obligatorio.';
            }

            // Validar nuevo usuario SÓLO si se llenaron los campos
            $isCreatingNewUser = !$id && empty($_POST['user_id']) && (!empty($_POST['new_username']) || !empty($_POST['new_password']));
            
            if ($isCreatingNewUser) {
                if (empty($_POST['new_username'])) {
                    $errors[] = 'El nombre de usuario es obligatorio para crear un nuevo usuario.';
                }
                if (empty($_POST['new_password'])) {
                    $errors[] = 'La contraseña es obligatoria para crear un nuevo usuario.';
                }
            }

            // Si hay errores, redirigir de vuelta al formulario
            if (!empty($errors)) {
                $this->setFlashMessage('error', implode('<br>', $errors));
                $redirectUrl = $id ? BASE_URL . 'actors/create/' . $id : BASE_URL . 'actors/create';
                header('Location: ' . $redirectUrl);
                exit;
            }
            // --- FIN DE VALIDACIÓN ---


            // Normalizar user_id vacío
            if (isset($_POST['user_id']) && $_POST['user_id'] === '') {
                $_POST['user_id'] = null;
            }

            // Usamos la variable de validación que ya calculamos
            if ($isCreatingNewUser) {
                $existingUser = $this->userModel->findByUsername($_POST['new_username']);
                if ($existingUser) {
                    $this->setFlashMessage('error', 'El nombre de usuario ya está registrado.');
                    header('Location: ' . BASE_URL . 'actors/create');
                    exit;
                }

                $actorRole = $_POST['role']; // Ya validamos que no esté vacío
                $newUserId = $this->userModel->createUser(
                    $_POST['new_username'],
                    $_POST['new_password'],
                    $actorRole
                );

                if (!$newUserId || !is_numeric($newUserId)) {
                    $this->setFlashMessage('error', 'No se pudo crear el usuario.');
                    header('Location: ' . BASE_URL . 'actors/create');
                    exit;
                }

                $_POST['user_id'] = (int)$newUserId;
            }

            $data = [
                'full_name' => $_POST['full_name'], // Ya validamos
                'role' => $_POST['role'], // Ya validamos
                'institution_id' => $_POST['institution_id'] ?: null,
                'user_id' => $_POST['user_id'] ?? null,
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            if ($id) {
                $this->actorModel->update($id, $data);
                $this->setFlashMessage('success', 'Actor actualizado.');
            } else {
                $this->actorModel->createActor($data);
                $this->setFlashMessage('success', 'Actor creado.');
            }

            header('Location: ' . BASE_URL . 'actors');
            exit;
        }
    }


    public function delete(int $id)
    {
        // <-- SUGERENCIA: Implementación robusta con transacciones y chequeo de último admin
        
        // 1. Iniciar la transacción
        $this->actorModel->beginTransaction();

        try {
            // 2. Buscar al actor
            $actor = $this->actorModel->findByIdWithRelations($id);

            if (!$actor) {
                $this->setFlashMessage('error', 'Actor no encontrado.');
                header('Location: ' . BASE_URL . 'actors');
                exit;
            }

            // 3. Si tiene un usuario, verificar si es el último admin
            if (!empty($actor->user_id)) {
                
                // (Asumiendo que UserModel->find() existe - ver archivo 4)
                $user = $this->userModel->find($actor->user_id);

                if ($user && $user->role === 'admin') {
                    // (Asumiendo que UserModel->countByRole() existe - ver archivo 4)
                    $adminCount = $this->userModel->countByRole('admin');

                    if ($adminCount <= 1) {
                        // Si es el último admin, detenemos todo
                        $this->actorModel->rollBack(); // Cancelamos la transacción
                        $this->setFlashMessage('error', 'No se puede eliminar al último administrador del sistema.');
                        header('Location: ' . BASE_URL . 'actors');
                        exit;
                    }
                }

                // Si no es el último admin, proceder a eliminar el usuario
                $this->userModel->delete($actor->user_id);
            }

            // 4. Eliminar el actor
            $this->actorModel->delete($id);

            // 5. Si todo salió bien, confirmar la transacción
            $this->actorModel->commit();

            $this->setFlashMessage('success', 'Actor y usuario vinculado eliminados correctamente.');
        
        } catch (\Exception $e) {
            // 6. Si algo falló, revertir todo
            $this->actorModel->rollBack();  
            $this->setFlashMessage('error', 'Error al eliminar el actor. Es posible que tenga registros vinculados.');
                error_log($e->getMessage()); // Descomentar para registrar el error
        }

        // Redirigir al final
        header('Location: ' . BASE_URL . 'actors');
        exit;
    }

    public function pdf(int $id)
    {
        $actor = $this->actorModel->findByIdWithRelations($id);

        if (!$actor) {
            $this->setFlashMessage('error', 'Actor no encontrado.');
            header('Location: ' . BASE_URL . 'actors');
            exit;
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include __DIR__ . '/../Views/actors/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador
        $dompdf->stream("Ficha_Actor_{$actor->id}.pdf", ["Attachment" => false]);
    }
}