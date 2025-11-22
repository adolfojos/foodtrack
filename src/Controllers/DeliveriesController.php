<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Delivery;
use App\Models\Reception;
use App\Models\Actor;
use Dompdf\Dompdf;
use Dompdf\Options;

class DeliveriesController extends Controller
{
    private $deliveryModel;
    private $receptionModel;
    private $actorModel;

    public function __construct()
    {
        parent::__construct();
        $this->restrictTo(['admin', 'inspector', 'vocero_institucional']);
        $this->deliveryModel = new Delivery();
        $this->receptionModel = new Reception();
        $this->actorModel = new Actor();
    }

    public function index()
    {
        $deliveries = $this->deliveryModel->findAllWithDetails();
        $this->render('deliveries/list', [
            'title' => 'Historial de Entregas',
            'deliveries' => $deliveries
        ]);
    }

    public function create()
    {
        // 1. Cargar Recepciones (Traemos TODAS para poder filtrar en la vista)
        $receptions = $this->receptionModel->findAllWithActors();
        
        // 2. Cargar Instituciones activas
        $institutions = $this->deliveryModel->getActiveInstitutions();

        // 3. Cargar Responsables
        $receivers = $this->actorModel->findByRole('director'); 

        $this->render('deliveries/form', [
            'title' => 'Nueva Entrega',
            'receptions' => $receptions,
            'institutions' => $institutions,
            'receivers' => $receivers
        ]);
    }

    /**
     * AJAX MEJORADO: Devuelve sugerencia y el TIPO de recepción
     */
    public function getSuggestedAllocation()
    {
        header('Content-Type: application/json');
        
        $schoolEnrollment = (int)($_GET['enrollment'] ?? 0);
        $receptionId = (int)($_GET['reception_id'] ?? 0);

        if ($schoolEnrollment <= 0 || $receptionId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']); return;
        }

        // 1. Datos Recepción
        $sourceData = $this->deliveryModel->getSourceReceptionData($receptionId);
        if (!$sourceData) {
            echo json_encode(['success' => false]); return;
        }
        
        $header = $sourceData['header'];
        $totalReceivedQty = (float)$header->summary_quantity; // Total Bolsas o Total Kilos

        // 2. Universo Total
        $totalUniverse = $this->deliveryModel->getTotalActiveEnrollment();
        if ($totalUniverse <= 0) {
             echo json_encode(['success' => false, 'message' => 'Matrícula total es 0']); return;
        }

        // 3. Cálculo del Factor (Porcentaje de matrícula que representa la escuela)
        // Ejemplo: Escuela 100 alumnos / Universo 1000 = 0.1 (10%)
        $shareFactor = $schoolEnrollment / $totalUniverse;
        
        // 4. Cantidad Sugerida
        // Si es CLAP: 0.1 * 500 bolsas = 50 bolsas.
        // Si es FRUVER: 0.1 * 140 kg = 14 kg.
        $suggestedQty = floor($schoolEnrollment * ($totalReceivedQty / $totalUniverse));

        echo json_encode([
            'success' => true,
            'reception_type' => $header->reception_type, // IMPORTANTE: Enviamos el tipo
            'school_enrollment' => $schoolEnrollment,
            'total_universe' => $totalUniverse,
            'received_qty' => $totalReceivedQty,
            'suggested_qty' => $suggestedQty,
            'unit_label' => ($header->reception_type === 'CLAP') ? 'Bolsas' : 'Kg/Und Total'
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['date'];
            $institution_id = $_POST['institution_id'];
            $reception_id = $_POST['reception_id'];
            $receiver_id = $_POST['receiver_id'];
            
            // Recogemos los valores (vendrán en 0 si están ocultos)
            $qty_groceries = (float)($_POST['qty_groceries'] ?? 0);
            $qty_proteins = (float)($_POST['qty_proteins'] ?? 0);
            $qty_fruits = (float)($_POST['qty_fruits'] ?? 0);

            // Validar tipo de recepción para saber cuál valor usar
            $sourceData = $this->deliveryModel->getSourceReceptionData($reception_id);
            $type = $sourceData['header']->reception_type;

            // Guardar Cabecera
            $deliveryId = $this->deliveryModel->createDelivery([
                'date' => $date,
                'institution_id' => $institution_id,
                'reception_id' => $reception_id,
                'receiver_id' => $receiver_id,
                'qty_groceries' => $qty_groceries,
                'qty_proteins' => $qty_proteins,
                'qty_fruits' => $qty_fruits
            ]);

            if ($deliveryId) {
                // --- LÓGICA DE DISTRIBUCIÓN SEGÚN TIPO ---
                
                if ($type === 'CLAP' && $qty_groceries > 0) {
                    // Estrategia: Multiplicar contenido de bolsa
                    $this->processClapItems($deliveryId, $reception_id, $qty_groceries);
                } 
                elseif (($type === 'FRUVERT' && $qty_fruits > 0) || ($type === 'PROTEINA' && $qty_proteins > 0)) {
                    // Estrategia: Proporción del total
                    // Usamos la cantidad ingresada (ej. 14kg)
                    $amountToDeliver = ($type === 'FRUVERT') ? $qty_fruits : $qty_proteins;
                    $this->processProportionalItems($deliveryId, $sourceData, $amountToDeliver);
                }

                $this->setFlashMessage('success', 'Entrega registrada correctamente.');
                header('Location: ' . BASE_URL . 'deliveries');
            } else {
                $this->setFlashMessage('error', 'Error al guardar.');
                header('Location: ' . BASE_URL . 'deliveries/create');
            }
        }
    }

    /**
     * Lógica CLAP: Multiplica contenido de 1 bolsa por N bolsas
     */
    private function processClapItems($deliveryId, $receptionId, $bagsDelivered)
    {
        $sourceData = $this->deliveryModel->getSourceReceptionData($receptionId);
        $header = $sourceData['header'];
        $items = $sourceData['items'];

        if ($header->summary_quantity <= 0) return;

        foreach ($items as $item) {
            $qtyPerBag = $item->total_qty / $header->summary_quantity;
            $totalToDeliver = $qtyPerBag * $bagsDelivered;
            
            $this->deliveryModel->createDeliveryItem($deliveryId, $item->product_id, $totalToDeliver, $item->unit);
        }
    }

    /**
     * NUEVO: Lógica Fruver/Proteína: Calcula la proporción
     * Si llegaron 140kg en total y entregamos 14kg, estamos entregando el 10% de TODO.
     */
    private function processProportionalItems($deliveryId, $sourceData, $amountToDeliver)
    {
        $header = $sourceData['header'];
        $items = $sourceData['items'];
        
        $totalReceived = $header->summary_quantity; // Ej: 140 (kg totales sumados)

        if ($totalReceived <= 0) return;

        // Factor de entrega (Ej: 14 / 140 = 0.1)
        $ratio = $amountToDeliver / $totalReceived;

        foreach ($items as $item) {
            // Si había 100kg de Patilla => 100 * 0.1 = 10kg de Patilla a entregar
            $itemDeliverQty = $item->total_qty * $ratio;
            
            $this->deliveryModel->createDeliveryItem($deliveryId, $item->product_id, $itemDeliverQty, $item->unit);
        }
    }
    
    // ... (detail, delete, etc. se mantienen igual)
    public function delete(int $id) {
         if($this->deliveryModel->deleteDelivery($id)) {
             $this->setFlashMessage('success', 'Entrega eliminada.');
         } else {
             $this->setFlashMessage('error', 'No se pudo eliminar.');
         }
         header('Location: ' . BASE_URL . 'deliveries');
    }
    
    public function detail(int $id) {
        $delivery = $this->deliveryModel->findByIdWithDetails($id);
        if (!$delivery) { header('Location:'.BASE_URL.'deliveries'); exit; }
        $items = $this->deliveryModel->getItemsByDeliveryId($id);
        $this->render('deliveries/detail', ['title' => 'Detalle', 'delivery' => $delivery, 'items' => $items]);
    }
 


// ... dentro de la clase DeliveriesController ...

    /**
     * Formulario de Edición
     */
    public function edit(int $id)
    {
        // 1. Obtener datos de la entrega existente
        $delivery = $this->deliveryModel->findByIdWithDetails($id);
        
        if (!$delivery) {
            $this->setFlashMessage('error', 'Entrega no encontrada.');
            header('Location: ' . BASE_URL . 'deliveries');
            exit;
        }

        // 2. Cargar listas para los select (Igual que en create)
        $receptions = $this->receptionModel->findAllWithActors();
        $institutions = $this->deliveryModel->getActiveInstitutions();
        $receivers = $this->actorModel->findByRole('director'); 

        $this->render('deliveries/form', [
            'title' => 'Editar Entrega #' . $id,
            'delivery' => $delivery, // Variable clave para saber que es edición
            'receptions' => $receptions,
            'institutions' => $institutions,
            'receivers' => $receivers
        ]);
    }

    /**
     * Procesar la Actualización
     */
    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deliveryModel = new Delivery();

            // 1. Recoger datos básicos
            $data = [
                'date' => $_POST['date'],
                'institution_id' => $_POST['institution_id'],
                'reception_id' => $_POST['reception_id'],
                'receiver_id' => $_POST['receiver_id'],
                'qty_groceries' => (float)($_POST['qty_groceries'] ?? 0),
                'qty_proteins' => (float)($_POST['qty_proteins'] ?? 0),
                'qty_fruits' => (float)($_POST['qty_fruits'] ?? 0),
            ];

            // 2. Actualizar Cabecera (Usamos SQL directo o un método updateDelivery si lo tienes, aquí usaremos query simple por brevedad o adaptar create)
            // Para mantener el patrón, asumimos que tienes un método update o lo hacemos manual:
            $sql = "UPDATE deliveries SET date=?, institution_id=?, reception_id=?, receiver_id=?, qty_groceries=?, qty_proteins=?, qty_fruits=? WHERE id=?";
            $stmt = $deliveryModel->getPdo()->prepare($sql);
            
            if ($stmt->execute([...array_values($data), $id])) {
                
                // 3. RECALCULAR INVENTARIO
                // A. Borramos los items viejos
                $deliveryModel->clearItems($id);

                // B. Obtenemos datos de la fuente para recalcular
                $sourceData = $deliveryModel->getSourceReceptionData($data['reception_id']);
                $type = $sourceData['header']->reception_type;

                // C. Insertamos los nuevos items según la lógica
                if ($type === 'CLAP' && $data['qty_groceries'] > 0) {
                    $this->processClapItems($id, $data['reception_id'], $data['qty_groceries']);
                } 
                elseif (($type === 'FRUVERT' && $data['qty_fruits'] > 0) || ($type === 'PROTEINA' && $data['qty_proteins'] > 0)) {
                    $amountToDeliver = ($type === 'FRUVERT') ? $data['qty_fruits'] : $data['qty_proteins'];
                    $this->processProportionalItems($id, $sourceData, $amountToDeliver);
                }

                $this->setFlashMessage('success', 'Entrega actualizada y stock recalculado.');
                header('Location: ' . BASE_URL . 'deliveries');
                exit;
            } else {
                $this->setFlashMessage('error', 'Error al actualizar la entrega.');
                header('Location: ' . BASE_URL . 'deliveries/edit/' . $id);
                exit;
            }
        }
    }

    /**
     * Generar PDF del Acta de Entrega
     */
    public function pdf(int $id)
    {
        $delivery = $this->deliveryModel->findByIdWithDetails($id);
        $items = $this->deliveryModel->getItemsByDeliveryId($id);

        if (!$delivery) { header('Location:'.BASE_URL.'deliveries'); exit; }

        // Configuración Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizar vista
        ob_start();
        include __DIR__ . '/../Views/deliveries/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Nombre del archivo: Acta_Entrega_ID_FECHA.pdf
        $fileName = "Acta_Entrega_{$id}_" . date('dmY', strtotime($delivery->date)) . ".pdf";
        $dompdf->stream($fileName, ["Attachment" => false]);
    }
}