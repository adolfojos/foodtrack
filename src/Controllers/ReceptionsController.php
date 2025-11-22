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
        $this->actorModel = new Actor();
    }

    public function index()
    {
        $receptions = $this->receptionModel->findAllWithActors();
        $this->render('receptions/list', [
            'title' => 'Recepciones Mensuales',
            'receptions' => $receptions
        ]);
    }

public function create()
    {
        $inspectors = $this->actorModel->findByRole('inspector');
        $spokespersons = $this->actorModel->findByRole('vocero_parroquial');
        
        // NUEVO: Obtenemos la lista de productos
        $productsList = $this->receptionModel->getAllProductNames();

        $receptionTypes = ['CLAP', 'FRUVERT', 'PROTEINA', 'OTRO']; 

        $this->render('receptions/form', [
            'title' => 'Nueva Recepción',
            'inspectors' => $inspectors,
            'spokespersons' => $spokespersons,
            'receptionTypes' => $receptionTypes,
            'productsList' => $productsList // <--- Pasamos la variable aquí
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Recoger datos
            $date = $_POST['date'] ?? null;
            $inspector_id = $_POST['inspector_id'] ?? null;
            $vocero_parroquial_id = $_POST['vocero_parroquial_id'] ?? null;
            $reception_type = $_POST['reception_type'] ?? null;
            $summary_quantity = (int)($_POST['summary_quantity'] ?? 0);
            $notes = $_POST['notes'] ?? '';
            $items = $_POST['items'] ?? []; // Array de items

            // 2. Validación
            if (!$date || !$inspector_id || !$vocero_parroquial_id || !$reception_type || empty($items)) {
                $this->setFlashMessage('error', 'Faltan datos obligatorios.');
                header('Location: ' . BASE_URL . 'receptions/create');
                exit;
            }

            // 3. Crear Cabecera
            $receptionId = $this->receptionModel->createReception([
                'date' => $date,
                'inspector_id' => $inspector_id,
                'vocero_parroquial_id' => $vocero_parroquial_id,
                'reception_type' => $reception_type,
                'summary_quantity' => $summary_quantity,
                'notes' => $notes
            ]);

            if ($receptionId) {
                
                // --- LÓGICA DE MULTIPLICACIÓN ---
                // Si es CLAP y hay X bolsas, multiplicamos el contenido por X.
                // Si es otra cosa, multiplicamos por 1 (se queda igual).
                $multiplier = 1;
                if ($reception_type === 'CLAP' && $summary_quantity > 0) {
                    $multiplier = $summary_quantity;
                }

                // 4. Procesar Items
                foreach ($items as $item) {
                    $name = trim($item['product_name'] ?? '');
                    $qtyPerUnit = (float)($item['quantity'] ?? 0); // Cantidad unitaria (ej: 3 arroz por bolsa)
                    $unit = trim($item['unit'] ?? 'unidad');

                    if (!empty($name) && $qtyPerUnit > 0) {
                        
                        // A. Obtener ID del producto (o crearlo)
                        $productId = $this->receptionModel->getOrCreateProduct($name, $unit);

                        if ($productId) {
                            // B. Calcular TOTAL REAL para inventario
                            // Ejemplo: 3 arroz * 386 bolsas = 1158 arroz total
                            $totalRealQty = $qtyPerUnit * $multiplier;

                            // C. Guardar Item
                            $this->receptionModel->createReceptionItem(
                                $receptionId, 
                                $productId, 
                                $totalRealQty, 
                                $unit
                            );
                        }
                    }
                }

                $msg = "Recepción guardada.";
                if ($multiplier > 1) {
                    $msg .= " Se calculó el inventario total multiplicando por $multiplier bolsas.";
                }

                $this->setFlashMessage('success', $msg);
                header('Location: ' . BASE_URL . 'receptions');
                exit;
            } else {
                $this->setFlashMessage('error', 'Error al guardar en base de datos.');
                header('Location: ' . BASE_URL . 'receptions/create');
                exit;
            }
        }
    }
/**
     * Formulario de EDICIÓN
     */
    public function edit(int $id)
    {
        // 1. Obtener datos básicos
        $reception = $this->receptionModel->findByIdWithActors($id);
        if (!$reception) {
            $this->setFlashMessage('error', 'Recepción no encontrada.');
            header('Location: ' . BASE_URL . 'receptions');
            exit;
        }

        // 2. Obtener items
        $items = $this->receptionModel->getItemsByReceptionId($id);

        // 3. LOGICA INVERSA PARA CLAP:
        // Si es CLAP, en la BD está el TOTAL (ej: 1000kg). 
        // Pero en el form queremos mostrar "por bolsa" (ej: 1kg) para que el usuario pueda editar.
        if ($reception->reception_type === 'CLAP' && $reception->summary_quantity > 0) {
            foreach ($items as $item) {
                // Revertimos la multiplicación para mostrar la unidad original
                $item->quantity = $item->quantity / $reception->summary_quantity;
            }
        }

        // 4. Cargar datos auxiliares
        $inspectors = $this->actorModel->findByRole('inspector');
        $spokespersons = $this->actorModel->findByRole('vocero_parroquial');
        $productsList = $this->receptionModel->getAllProductNames(); // Autocompletado
        $receptionTypes = ['CLAP', 'FRUVERT', 'PROTEINA', 'OTRO'];

        // 5. Renderizar vista (usamos el mismo form.php pero con datos)
        $this->render('receptions/form', [
            'title' => 'Editar Recepción #' . $id,
            'reception' => $reception, // Variable clave para saber que es edición
            'items' => $items,
            'inspectors' => $inspectors,
            'spokespersons' => $spokespersons,
            'receptionTypes' => $receptionTypes,
            'productsList' => $productsList
        ]);
    }

    /**
     * Procesar la ACTUALIZACIÓN
     */
    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos (Igual que en save)
            $date = $_POST['date'] ?? null;
            $inspector_id = $_POST['inspector_id'] ?? null;
            $vocero_parroquial_id = $_POST['vocero_parroquial_id'] ?? null;
            $reception_type = $_POST['reception_type'] ?? null;
            $summary_quantity = (int)($_POST['summary_quantity'] ?? 0);
            $notes = $_POST['notes'] ?? '';
            $items = $_POST['items'] ?? [];

            // Actualizar Cabecera
            $this->receptionModel->updateReception($id, [
                'date' => $date,
                'inspector_id' => $inspector_id,
                'vocero_parroquial_id' => $vocero_parroquial_id,
                'reception_type' => $reception_type,
                'summary_quantity' => $summary_quantity,
                'notes' => $notes
            ]);

            // --- TRATAMIENTO DE ITEMS ---
            // 1. Borramos los items viejos (es más seguro que intentar actualizarlos uno por uno)
            $this->receptionModel->deleteAllItemsByReceptionId($id);

            // 2. Calculamos multiplicador (Igual que en save)
            $multiplier = 1;
            if ($reception_type === 'CLAP' && $summary_quantity > 0) {
                $multiplier = $summary_quantity;
            }

            // 3. Re-insertamos los items nuevos/editados
            foreach ($items as $item) {
                $name = trim($item['product_name'] ?? '');
                $qtyPerUnit = (float)($item['quantity'] ?? 0);
                $unit = trim($item['unit'] ?? 'unidad');

                if (!empty($name) && $qtyPerUnit > 0) {
                    $productId = $this->receptionModel->getOrCreateProduct($name, $unit);
                    if ($productId) {
                        $totalRealQty = $qtyPerUnit * $multiplier;
                        $this->receptionModel->createReceptionItem($id, $productId, $totalRealQty, $unit);
                    }
                }
            }

            $this->setFlashMessage('success', 'Recepción actualizada correctamente.');
            header('Location: ' . BASE_URL . 'receptions');
            exit;
        }
    }
    
    public function detail(int $id)
    {
        $reception = $this->receptionModel->findByIdWithActors($id);
        if (!$reception) {
            echo "Recepción no encontrada."; return;
        }
        $items = $this->receptionModel->getItemsByReceptionId($id);

        $this->render('receptions/detail', [
            'reception' => $reception,
            'items' => $items,
            'title' => 'Detalle de Recepción #' . $id
        ]);
    }

    public function pdf(int $id)
    {
        // ... (Tu código de PDF se mantiene igual, solo asegúrate de cargar los items)
        $reception = $this->receptionModel->findByIdWithActors($id);
        $items = $this->receptionModel->getItemsByReceptionId($id);

        if (!$reception) { header('Location:'.BASE_URL.'receptions'); exit; }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        include __DIR__ . '/../Views/receptions/pdf.php'; 
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Acta_$id.pdf", ["Attachment" => false]);
    }
    
public function delete(int $id) {
        $receptionModel = new Reception();
        
        // Intentar eliminar la recepción y sus ítems
        if ($receptionModel->deleteReception($id)) {
            // Éxito: Redirigir al listado con un mensaje de éxito
            $this->setFlashMessage('success', "La recepción N° $id y sus ítems asociados fueron eliminados correctamente.");
        } else {
            // Error: Redirigir al listado con un mensaje de error
            $this->setFlashMessage('error', "Error al intentar eliminar la recepción N° $id. Por favor, intente de nuevo.");
        }
        
        // Redirigir a la vista de listado

        header('Location: ' . BASE_URL . 'receptions');
    }
}