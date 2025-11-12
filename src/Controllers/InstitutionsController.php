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
        $institutions = $this->institutionModel->findAll();
        $this->render('institutions/list', [
            'title' => 'Gestión de Instituciones',
            'institutions' => $institutions
        ]);
    }
    public function detail(int $id)
{
    $institution = $this->institutionModel->findById($id);

    if (!$institution) {
        $this->setFlashMessage('error', 'Institución no encontrada.');
        header('Location: ' . BASE_URL . 'institutions');
        exit;
    }

    $this->render('institutions/detail', [
        'title' => 'Detalle de Institución',
        'institution' => $institution
    ]);
}


    /**
     * Formulario de creación/edición
     * Acceso: /institutions/create | /institutions/edit/{id}
     */
    public function create(int $id = null)
    {
        $institution = null;
        if ($id) {
            $institution = $this->institutionModel->findById($id);
            if (!$institution) {
                die("Institución no encontrada.");
            }
        }

        $this->render('institutions/form', [
            'title' => $id ? 'Editar Institución' : 'Nueva Institución',
            'institution' => $institution
        ]);
    }

    /**
     * Guardar institución (POST)
     * Acceso: /institutions/save
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'name' => $_POST['name'] ?? '',
                'parish' => $_POST['parish'] ?? '',
                'director' => $_POST['director'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            if ($id) {
                $this->institutionModel->updateInstitution($id, $data);
                $this->setFlashMessage('success', 'Institución actualizada.');
            } else {
                $this->institutionModel->createInstitution($data);
                $this->setFlashMessage('success', 'Institución creada.');
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
            $this->setFlashMessage('success', "institución ID #{$id} eliminado.");
        } else {
            $this->setFlashMessage('error', "No se pudo eliminar la institución ID #{$id} . ");
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

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include(__DIR__ . '/../Views/institutions/pdf.php');
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador
        $dompdf->stream("Ficha_Institucion_{$institution->id}.pdf", ["Attachment" => false]);
    }
}
