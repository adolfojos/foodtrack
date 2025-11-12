<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reception;
use App\Models\Actor;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReceptionsController extends Controller
{
    private $receptionModel;
    private $actorModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin', 'inspector', 'vocero_parroquial']);
        $this->receptionModel = new Reception();
        $this->actorModel = new Actor(); // Para listar inspectores y voceros
    }

    /**
     * Listado de recepciones
     * Acceso: /receptions
     */
    public function index()
    {
        $receptions = $this->receptionModel->findAllWithActors();
        $this->render('receptions/list', [
            'title' => 'Recepciones mensuales',
            'receptions' => $receptions
        ]);
    }

    /**
     * Formulario de creación
     * Acceso: /receptions/create
     */
    public function create()
    {
        $inspectors = $this->actorModel->findByRole('inspector');
        $spokespersons = $this->actorModel->findByRole('vocero_parroquial');

        $this->render('receptions/form', [
            'title' => 'Nueva recepción',
            'inspectors' => $inspectors,
            'spokespersons' => $spokespersons
        ]);
    }

    /**
     * Guardar recepción (POST)
     * Acceso: /receptions/save
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['date'] ?? null;
            $inspector_id = $_POST['inspector_id'] ?? null;
            $vocero_parroquial_id = $_POST['vocero_parroquial_id'] ?? null;
            $total_bags = $_POST['total_bags'] ?? 0;
            $notes = $_POST['notes'] ?? '';

            if (!$date || !$inspector_id || !$vocero_parroquial_id) {
                $this->setFlashMessage('error', 'Todos los campos obligatorios deben completarse.');
                header('Location: ' . BASE_URL . 'receptions/create');
                exit;
            }

            $this->receptionModel->createReception([
                'date' => $date,
                'inspector_id' => $inspector_id,
                'vocero_parroquial_id' => $vocero_parroquial_id,
                'total_bags' => $total_bags,
                'notes' => $notes
            ]);

            $this->setFlashMessage('success', 'Recepción registrada correctamente.');
            header('Location: ' . BASE_URL . 'receptions');
            exit;
        }
    }

    /**
     * Eliminar recepción
     * Acceso: /receptions/delete/{id}
     */
    public function delete(int $id)
    {
        $this->receptionModel->delete($id);
        $this->setFlashMessage('success', 'Recepción eliminada.');
        header('Location: ' . BASE_URL . 'receptions');
        exit;
    }

    public function pdf(int $id)
    {
        $reception = $this->receptionModel->findByIdWithActors($id);

        if (!$reception) {
            $this->setFlashMessage('error', 'Recepción no encontrada.');
            header('Location: ' . BASE_URL . 'receptions');
            exit;
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include __DIR__ . '/../Views/receptions/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar directamente: Attachment" => true
        // Abre el PDF en el navegador Attachment" => false
        $dompdf->stream("Acta_Recepcion_{$reception->id}.pdf", ["Attachment" => false]);

    }
}
