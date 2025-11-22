<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Actor;
use App\Models\Institution;
use Dompdf\Dompdf;
use Dompdf\Options;

class ActorsController extends Controller {

    private $userModel;
    private $actorModel;
    private $institutionModel;

    private $allowedRoles = [
        'admin','inspector','vocero_parroquial',
        'vocero_institucional','director','cocinero'
    ];

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->actorModel = new Actor();
        $this->institutionModel = new Institution();
        $this->restrictTo(['admin']);
    }

    // ------------------------------------------------------------------
    // LISTADO
    // ------------------------------------------------------------------
    public function index() {
        $actors = $this->actorModel->findAllWithRelations();
        $this->render('actors/list', [
            'title' => 'Gestión de Actores y Roles',
            'actors' => $actors
        ]);
    }

    // ------------------------------------------------------------------
    // FORMULARIO
    // ------------------------------------------------------------------
    public function create(int $id = null) {
        $actor = null;
        $institution_ids = [];
        $all_institutions = $this->institutionModel->findAll();

        if ($id) {
            $actor = $this->actorModel->findByIdWithRelations($id);
            if (!$actor) $this->redirectWithError('actors', 'Actor no encontrado.');
            $assigned = $this->actorModel->getInstitutionsForActor($id);
            $institution_ids = array_column($assigned, 'id');
        }

        $this->render('actors/form', [
            'title' => ($id ? 'Editar' : 'Crear nuevo') . ' Actor',
            'actor' => $actor,
            'all_institutions' => $all_institutions,
            'institution_ids' => $institution_ids,
            'allowedRoles' => $this->allowedRoles
        ]);
    }

    public function edit(int $id) { $this->create($id); }

    // ------------------------------------------------------------------
    // GUARDADO
    // ------------------------------------------------------------------
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('actors', 'Método no permitido.');
        }

        $actor_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $user_id = (int) filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
        $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $national_id = filter_input(INPUT_POST, 'national_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
        $institution_ids = $_POST['institution_ids'] ?? [];
        $active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);


        // Validación básica
        if (empty($full_name) || empty($national_id) || empty($username) || !in_array($role, $this->allowedRoles)) {
            $this->redirectWithError('actors/create', 'Campos obligatorios faltantes o rol inválido.');
        }

        // Validación de cédula duplicada
        $existingActor = $this->actorModel->findByNationalId($national_id);
        if ($existingActor && (!$actor_id || $existingActor->id != $actor_id)) {
            $this->redirectWithError('actors/create', "Error: La cédula {$national_id} ya está registrada.");
        }

        // Validaciones de rol por institución
        foreach ($institution_ids as $instId) {
            if ($role === 'director') {
                $exists = $this->actorModel->existsRoleInInstitution('director', (int)$instId, $actor_id ?: null);
                if ($exists) {
                    $this->redirectWithError('actors/create', "Ya existe un director en la institución seleccionada.");
                }
            }
            if ($role === 'vocero_institucional') {
                $exists = $this->actorModel->existsRoleInInstitution('vocero_institucional', (int)$instId, $actor_id ?: null);
                if ($exists) {
                    $this->redirectWithError('actors/create', "Ya existe un vocero institucional en la institución seleccionada.");
                }
            }
        }
        if ($role === 'vocero_institucional' && count($institution_ids) > 1) {
            $this->redirectWithError('actors/create', "Un vocero institucional solo puede estar asociado a una institución.");
        }

        // Datos actor
        $actorData = [
            'full_name' => $full_name,
            'national_id' => $national_id,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'user_id' => $user_id > 0 ? $user_id : null,
            'active' => $active !== null ? $active : true // por defecto TRUE
        ];

        // Actualizar
        if ($actor_id) {
            if ($user_id > 0) {
                $userUpdateData = ['username' => $username];
                if (!empty($password)) {
                    $userUpdateData['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $this->userModel->update($user_id, $userUpdateData);
            }
            $this->actorModel->updateActor($actor_id, $actorData);
            $this->actorModel->syncInstitutions($actor_id, $institution_ids);
            $this->redirectWithSuccess('actors', 'Actor actualizado exitosamente.');
        } else {
            // Crear
            if (empty($password)) {
                $this->redirectWithError('actors/create', 'La contraseña es obligatoria para un nuevo actor.');
            }
            $newUserId = $this->userModel->createUser($username, $password);
            if (!$newUserId) {
                $this->redirectWithError('actors/create', 'Error al crear login.');
            }
            $actorData['user_id'] = $newUserId;
            $newActorId = $this->actorModel->createActor($actorData);
            if (!$newActorId) {
                $this->userModel->delete($newUserId);
                $this->redirectWithError('actors/create', 'Error al crear actor.');
            }
            $this->actorModel->syncInstitutions($newActorId, $institution_ids);
            $this->redirectWithSuccess('actors', 'Actor creado exitosamente.');
        }
    }

    // ------------------------------------------------------------------
    // DETALLE
    // ------------------------------------------------------------------
    public function detail(int $id) {
        $actor = $this->actorModel->findByIdWithRelations($id);
        if (!$actor) $this->redirectWithError('actors', 'Actor no encontrado.');
        $institutions = $this->actorModel->getInstitutionsForActor($id);
        $this->render('actors/detail', [
            'title' => 'Detalle de Actor: ' . htmlspecialchars($actor->full_name),
            'actor' => $actor,
            'institutions' => $institutions
        ]);
    }

    // ------------------------------------------------------------------
    // ELIMINAR
    // ------------------------------------------------------------------
    public function delete(int $actor_id) {
        $actor = $this->actorModel->find($actor_id);
        if (!$actor) $this->redirectWithError('actors', 'Actor no encontrado.');
        if ($actor->user_id == 1) {
            $this->redirectWithError('actors', 'No se puede eliminar el administrador principal.');
        }
        if ($actor->user_id) $this->userModel->delete($actor->user_id);
        $this->actorModel->delete($actor_id);
        $this->redirectWithSuccess('actors', 'Actor eliminado.');
    }

    // ------------------------------------------------------------------
    // PDF
    // ------------------------------------------------------------------
    public function pdf(int $id) {
        $actor = $this->actorModel->findByIdWithRelations($id);
        if (!$actor) {
            $this->setFlashMessage('error', 'Actor no encontrado.');
            header('Location: ' . BASE_URL . 'actors');
            exit;
        }
        $institutions = $this->actorModel->getInstitutionsForActor($id);
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        ob_start();
        include(__DIR__ . '/../Views/actors/pdf.php');
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = "Ficha_Actor_{$actor->national_id}.pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
        exit;
    }
}
