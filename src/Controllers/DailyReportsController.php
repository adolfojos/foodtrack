<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\DailyReport;
use App\Models\Institution;
use App\Models\Delivery;
use App\Models\Actor;
use Dompdf\Dompdf;
use Dompdf\Options;

class DailyReportsController extends Controller
{
    private $reportModel;
    private $institutionModel;
    private $deliveryModel;
    private $actorModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin', 'vocero_institucional']);
        $this->reportModel = new DailyReport();
        $this->institutionModel = new Institution();
        $this->deliveryModel = new Delivery();
        $this->actorModel = new Actor();
    }

    public function index()
    {
        $reports = $this->reportModel->findAllWithRelations();
        $this->render('dailyreports/list', [
            'title' => 'Reportes diarios de consumo',
            'reports' => $reports
        ]);
    }

    public function create()
    {
        $institutions = $this->institutionModel->findAll();
        $deliveries = $this->deliveryModel->findAllWithRelations();
        $spokespersons = $this->actorModel->findByRole('vocero_institucional');

        $this->render('dailyreports/form', [
            'title' => 'Nuevo reporte diario',
            'institutions' => $institutions,
            'deliveries' => $deliveries,
            'spokespersons' => $spokespersons
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'date' => $_POST['date'] ?? null,
                'institution_id' => $_POST['institution_id'] ?? null,
                'delivery_id' => $_POST['delivery_id'] ?? null,
                'spokesperson_id' => $_POST['spokesperson_id'] ?? null,
                'menu' => $_POST['menu'] ?? '',
                'used_groceries' => $_POST['used_groceries'] ?? 0,
                'used_proteins' => $_POST['used_proteins'] ?? 0,
                'used_fruits' => $_POST['used_fruits'] ?? 0,
                'used_vegetables' => $_POST['used_vegetables'] ?? 0,
                'students_served' => $_POST['students_served'] ?? 0
            ];

            if (!$data['date'] || !$data['institution_id'] || !$data['spokesperson_id']) {
                $this->setFlashMessage('error', 'Campos obligatorios faltantes.');
                header('Location: ' . BASE_URL . 'dailyreports/create');
                exit;
            }

            $this->reportModel->createReport($data);
            $this->setFlashMessage('success', 'Reporte diario registrado.');
            header('Location: ' . BASE_URL . 'dailyreports');
            exit;
        }
    }

    public function delete(int $id)
    {
        $this->reportModel->delete($id);
        $this->setFlashMessage('success', 'Reporte eliminado.');
        header('Location: ' . BASE_URL . 'dailyreports');
        exit;
    }
    
    public function consolidated()
    {
        $institutions = $this->institutionModel->findAll();
        $result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $institution_id = $_POST['institution_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;

            if ($institution_id && $start_date && $end_date) {
                $result = $this->reportModel->getConsolidatedByInstitution($institution_id, $start_date, $end_date);
            } else {
                $this->setFlashMessage('error', 'Debe seleccionar institución y rango de fechas.');
            }
        }

        $this->render('dailyreports/consolidated', [
            'title' => 'Consolidado de Reportes',
            'institutions' => $institutions,
            'result' => $result
        ]);
    }
    
    public function consolidatedPdf($institution_id, $start_date, $end_date)
    {
        $result = $this->reportModel->getConsolidatedByInstitution($institution_id, $start_date, $end_date);

        if (!$result) {
            $this->setFlashMessage('error', 'No se encontraron datos para el rango seleccionado.');
            header('Location: ' . BASE_URL . 'dailyreports/consolidated');
            exit;
        }

     // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include __DIR__ . '/../../Views/dailyreports/consolidated_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador
        $dompdf->stream("Consolidado_{$result->institution_name}_{$start_date}_{$end_date}.pdf", ["Attachment" => false]);
    }

}
