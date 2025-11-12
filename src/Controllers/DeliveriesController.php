<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Delivery;
use App\Models\Institution;
use App\Models\Reception;
use App\Models\Actor;
use Dompdf\Dompdf;
use Dompdf\Options;

class DeliveriesController extends Controller
{
    private $deliveryModel;
    private $institutionModel;
    private $receptionModel;
    private $actorModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin', 'inspector', 'vocero_parroquial']);
        $this->deliveryModel = new Delivery();
        $this->institutionModel = new Institution();
        $this->receptionModel = new Reception();
        $this->actorModel = new Actor();
    }

    /**
     * Listado de entregas
     * Acceso: /deliveries
     */
    public function index()
    {
        $deliveries = $this->deliveryModel->findAllWithRelations();
        $this->render('deliveries/list', [
            'title' => 'Historial de Entregas',
            'deliveries' => $deliveries
        ]);
    }

    /**
     * Formulario de creación
     * Acceso: /deliveries/create
     */
    public function create()
    {
        $institutions = $this->institutionModel->findAll();
        $receptions = $this->receptionModel->findAllWithActors();
        $receivers = $this->actorModel->findByRole('vocero_institucional');

        $this->render('deliveries/form', [
            'title' => 'Nueva Entrega',
            'institutions' => $institutions,
            'receptions' => $receptions,
            'receivers' => $receivers
        ]);
    }

    /**
     * Guardar entrega (POST)
     * Acceso: /deliveries/save
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'date' => $_POST['date'] ?? null,
                'institution_id' => $_POST['institution_id'] ?? null,
                'reception_id' => $_POST['reception_id'] ?? null,
                'receiver_id' => $_POST['receiver_id'] ?? null,
                'qty_groceries' => $_POST['qty_groceries'] ?? 0,
                'qty_proteins' => $_POST['qty_proteins'] ?? 0,
                'qty_fruits' => $_POST['qty_fruits'] ?? 0,
                'qty_vegetables' => $_POST['qty_vegetables'] ?? 0,
                'receiver_signature' => $_POST['receiver_signature'] ?? ''
            ];

            if (!$data['date'] || !$data['institution_id'] || !$data['receiver_id']) {
                $this->setFlashMessage('error', 'Todos los campos obligatorios deben completarse.');
                header('Location: ' . BASE_URL . 'deliveries/create');
                exit;
            }

            $this->deliveryModel->createDelivery($data);

            $this->setFlashMessage('success', 'Entrega registrada correctamente.');
            header('Location: ' . BASE_URL . 'deliveries');
            exit;
        }
    }

    /**
     * Eliminar entrega
     * Acceso: /deliveries/delete/{id}
     */
    public function delete(int $id)
    {
        $this->deliveryModel->delete($id);
        $this->setFlashMessage('success', 'Entrega eliminada.');
        header('Location: ' . BASE_URL . 'deliveries');
        exit;
    }
    public function pdf(int $id)
    {
        $delivery = $this->deliveryModel->findByIdWithRelations($id);

        if (!$delivery) {
            $this->setFlashMessage('error', 'Entrega no encontrada.');
            header('Location: ' . BASE_URL . 'deliveries');
            exit;
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include __DIR__ . '/../Views/deliveries/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador (no descarga automática)
        $dompdf->stream("Nota_Entrega_{$delivery->id}.pdf", ["Attachment" => false]);
    }
    
}
