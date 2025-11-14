<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Institution;
use Dompdf\Dompdf;
use Dompdf\Options;

class InstitutionsController extends Controller
{
    private $institutionModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin']); // Solo admin gestiona instituciones
        $this->institutionModel = new Institution();
    }

    /**
 * Listado de instituciones
 * Acceso: /institutions
 */
public function index()
{
    // Cambiar findAll() por el nuevo método que incluye los directores
    // Asumiendo que has modificado el método findAll() para que use la consulta con GROUP_CONCAT
    $institutions = $this->institutionModel->findAllWithDirectors(); 
    
    $this->render('institutions/list', [
        'title' => 'Gestión de Instituciones',
        'institutions' => $institutions
    ]);
}

    /**
     * Detalle de institución (incluye directores)
     */
    public function detail(int $id)
    {
        $institution = $this->institutionModel->findById($id);

        if (!$institution) {
            $this->setFlashMessage('error', 'Institución no encontrada.');
            header('Location: ' . BASE_URL . 'institutions');
            exit;
        }
        
        // NUEVO: Obtener los directores asociados
        $directors = $this->institutionModel->getDirectors($id);

        $this->render('institutions/detail', [
            'title' => 'Detalle de Institución',
            'institution' => $institution,
            'directors' => $directors // Pasar los directores a la vista
        ]);
    }

    /**
     * Formulario de creación/edición (incluye selección de director)
     * Acceso: /institutions/create | /institutions/edit/{id}
     */
    public function create(int $id = null)
    {
        $institution = null;
        $currentDirectors = []; // Directores actualmente asociados
        $currentDirectorIds = []; // Solo los IDs para marcar en el select
        
        // NUEVO: Obtener todos los directores disponibles para el selector
        $allDirectors = $this->institutionModel->getAllActorsByRoleForSelection('director'); 

        if ($id) {
            $institution = $this->institutionModel->findById($id);
            if (!$institution) {
                die("Institución no encontrada.");
            }
            // NUEVO: Obtener los directores ya asociados a esta institución
            $currentDirectors = $this->institutionModel->getDirectors($id);
            $currentDirectorIds = array_column($currentDirectors, 'id');
        }

        $this->render('institutions/form', [
            'title' => $id ? 'Editar Institución' : 'Nueva Institución',
            'institution' => $institution,
            'allDirectors' => $allDirectors,
            'currentDirectorIds' => $currentDirectorIds
        ]);
    }

    /**
     * Guardar institución (POST) y sincronizar directores
     * Acceso: /institutions/save
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            // NUEVO: Capturar el array de IDs de directores seleccionados
            $selectedDirectorIds = $_POST['director_ids'] ?? []; 
            // Asegurarse de que sea un array y limpiar cualquier valor vacío/cero
            $selectedDirectorIds = array_filter((array)$selectedDirectorIds, 'is_numeric');

            // Preparar data de la tabla institutions (quitando 'director' VARCHAR)
            $data = [
                'name' => $_POST['name'] ?? '',
                'parish' => $_POST['parish'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'campus_code' => $_POST['campus_code'] ?? null,
                'sica_code' => $_POST['sica_code'] ?? null,
                'municipality' => $_POST['municipality'] ?? null,
                'total_enrollment' => $_POST['total_enrollment'] ?? 0,
                'active' => isset($_POST['active']) ? 1 : 0
            ];
            
            $institutionId = null;
            
            if ($id) {
                $this->institutionModel->updateInstitution($id, $data);
                $institutionId = $id;
                $this->setFlashMessage('success', 'Institución actualizada.');
            } else {
                $institutionId = $this->institutionModel->createInstitution($data);
                $this->setFlashMessage('success', 'Institución creada.');
            }

            // NUEVO: Sincronizar los directores seleccionados (M:M)
            if ($institutionId) {
                $this->institutionModel->syncActors($institutionId, $selectedDirectorIds, 'director');
            }

            header('Location: ' . BASE_URL . 'institutions');
            exit;
        }
    }

    /**
     * block institución (soft block)
     * Acceso: /institutions/block/{id}
     */
    public function block(int $id)
    {
        $this->institutionModel->deactivate($id);
        $this->setFlashMessage('success', 'Institución desactivada.');
        header('Location: ' . BASE_URL . 'institutions');
        exit;
    }


    /**
     * Elimina una institutions.
     * Acceso: /institutions/delete/{id}
     */
    public function delete(int $id) {
        if ($this->institutionModel->delete($id)) {
            $this->setFlashMessage('success', "Institución ID #{$id} eliminada.");
        } else {
            $this->setFlashMessage('error', "No se pudo eliminar la institución ID #{$id}. (Posiblemente tiene dependencias).");
        }
            header('Location: ' . BASE_URL . 'institutions');
        exit;
    }
    
    /**
     * Generar PDF de ficha de institución
     * Acceso: /institutions/pdf/{id}
     */
    public function pdf(int $id)
    {
        $institution = $this->institutionModel->findById($id);

        if (!$institution) {
            $this->setFlashMessage('error', 'Institución no encontrada.');
            header('Location: ' . BASE_URL . 'institutions');
            exit;
        }
        
        // NUEVO: Obtener los directores asociados para el PDF
        $directors = $this->institutionModel->getDirectors($id);

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        // Asegúrate de que 'institutions/pdf.php' usa $institution y $directors
        include(__DIR__ . '/../Views/institutions/pdf.php'); 
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador
        $dompdf->stream("Ficha_Institucion_{$institution->id}.pdf", ["Attachment" => false]);
    }
}