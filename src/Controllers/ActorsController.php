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

    /**
     * SIN CAMBIOS (El modelo ya fue actualizado con GROUP_CONCAT)
     */
    public function index()
    {
        $actors = $this->actorModel->findAllWithRelations();
        $this->render('actors/list', [
            'title' => 'Gestión de Actores',
            'actors' => $actors
        ]);
    }

    /**
     * MODIFICADO: Ahora también busca las instituciones M:M
     */
    public function detail(int $id)
    {
        $actor = $this->actorModel->findByIdWithRelations($id);

        if (!$actor) {
            $this->setFlashMessage('error', 'Actor no encontrado.');
            header('Location: ' . BASE_URL . 'actors');
            exit;
        }
        
        // NUEVO: Obtener las instituciones asociadas (M:M)
        $institutions = $this->actorModel->getInstitutionsForActor($id);

        $this->render('actors/detail', [
            'title' => 'Detalle de Actor',
            'actor' => $actor,
            'institutions' => $institutions // Pasar a la vista
        ]);
    }

    /**
     * MODIFICADO: Prepara datos para el selector M:M
     */
    public function create(int $id = null)
    {
        $actor = null;
        $currentInstitutionIds = []; // IDs de instituciones ya asociadas

        if ($id) {
            $actor = $this->actorModel->findByIdWithRelations($id);
            if (!$actor) {
                die("Actor no encontrado.");
            }
            
            // NUEVO: Obtener las instituciones ya asociadas a este actor
            $currentInstitutions = $this->actorModel->getInstitutionsForActor($id);
            $currentInstitutionIds = array_column($currentInstitutions, 'id');
        }

        // Obtener TODAS las instituciones para el <select>
        $institutions = $this->institutionModel->findAllActive();

        $currentUserId = $actor ? $actor->user_id : null;
        $users = $this->userModel->findUnassignedUsers($currentUserId);

        $this->render('actors/form', [
            'title' => $id ? 'Editar Actor' : 'Nuevo Actor',
            'actor' => $actor,
            'institutions' => $institutions, // Lista completa para el select
            'currentInstitutionIds' => $currentInstitutionIds, // IDs seleccionados
            'users' => $users
        ]);
    }

    /**
     * MODIFICADO: Guarda el actor y sincroniza las instituciones M:M
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            // --- Validación (tu código existente, sin cambios) ---
            $errors = [];
            if (empty($_POST['full_name'])) {
                $errors[] = 'El nombre del actor es obligatorio.';
            }
            // ... (resto de tu lógica de validación) ...
            if (!empty($errors)) {
                $this->setFlashMessage('error', implode('<br>', $errors));
                $redirectUrl = $id ? BASE_URL . 'actors/create/' . $id : BASE_URL . 'actors/create';
                header('Location: ' . $redirectUrl);
                exit;
            }
            // --- Fin Validación ---

            // --- Lógica de Creación de Usuario (tu código existente, sin cambios) ---
            if (isset($_POST['user_id']) && $_POST['user_id'] === '') {
                $_POST['user_id'] = null;
            }
            
            $isCreatingNewUser = !$id && empty($_POST['user_id']) && (!empty($_POST['new_username']) || !empty($_POST['new_password']));
            if ($isCreatingNewUser) {
                // ... (toda tu lógica para crear el usuario y asignar $_POST['user_id']) ...
            }
            // --- Fin Lógica de Usuario ---

            // NUEVO: Capturar los IDs de las instituciones del selector M:M
            $institutionIds = $_POST['institution_ids'] ?? [];

            // Preparar $data solo con datos de la tabla 'actors'
            $data = [
                'full_name' => $_POST['full_name'],
                'role' => $_POST['role'],
                'user_id' => $_POST['user_id'] ?? null,
                'active' => isset($_POST['active']) ? 1 : 0, // Corregido a 'active'
                'national_id' => $_POST['national_id'] ?? null, // Asumiendo que tienes estos campos
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null
            ];

            $actorId = null;
            
            if ($id) {
                // Usamos el método de actor para limpiar $data si es necesario
                $this->actorModel->updateActor($id, $data); 
                $actorId = $id;
                $this->setFlashMessage('success', 'Actor actualizado.');
            } else {
                // Usamos el método de actor, que devuelve el ID
                $actorId = $this->actorModel->createActor($data); 
                $this->setFlashMessage('success', 'Actor creado.');
            }

            // NUEVO: Sincronizar la tabla M:M (actor_institution)
            if ($actorId) {
                $this->actorModel->syncInstitutions($actorId, $institutionIds);
            }

            header('Location: ' . BASE_URL . 'actors');
            exit;
        }
    }

    /**
     * SIN CAMBIOS (La lógica de borrado en cascada de BD maneja M:M)
     */
    public function delete(int $id)
    {
        // Tu lógica de transacción robusta aquí...
        // ... (el código de borrado que tenías es correcto) ...
        // ...
        
        // (El código original que tenías para 'delete' es robusto y no necesita cambios)
        $this.actorModel->beginTransaction();
        try {
            $actor = $this->actorModel->findByIdWithRelations($id);
             // ... (resto de tu lógica de chequeo de último admin) ...
            
             // 4. Eliminar el actor
             $this->actorModel->delete($id); // La BD se encarga de actor_institution
            
             // 5. Commit
            $this.actorModel->commit();
            $this->setFlashMessage('success', 'Actor y usuario vinculado eliminados.');
        
        } catch (\Exception $e) {
            $this.actorModel->rollBack();
            $this->setFlashMessage('error', 'Error al eliminar el actor.');
        }

        header('Location: ' . BASE_URL . 'actors');
        exit;
    }

    /**
     * MODIFICADO: Ahora también busca las instituciones M:M
     */
    public function pdf(int $id)
    {
        $actor = $this->actorModel->findByIdWithRelations($id);

        if (!$actor) {
            $this->setFlashMessage('error', 'Actor no encontrado.');
            header('Location: ' . BASE_URL . 'actors');
            exit;
        }

        // NUEVO: Obtener las instituciones asociadas (M:M)
        $institutions = $this->actorModel->getInstitutionsForActor($id);
        
        // ... (Configuración de Dompdf) ...
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista (la vista PDF ahora tendrá $actor y $institutions)
        ob_start();
        include __DIR__ . '/../Views/actors/pdf.php';
        $html = ob_get_clean();
        
        // ... (Renderizado de Dompdf) ...
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Ficha_Actor_{$actor->id}.pdf", ["Attachment" => false]);
    }
}