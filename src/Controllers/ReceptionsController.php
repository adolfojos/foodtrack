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

    /**
     * Listado de recepciones
     */
    public function index()
    {
        $receptions = $this->receptionModel->findAllWithActors();
        $this->render('receptions/list', [
            'title' => 'Recepciones mensuales',
            'receptions' => $receptions
        ]);
    }
    public function detail(int $id)
    {
        // 1. Instanciar el modelo de Recepción
        $receptionModel = new Reception();

        // 2. Obtener la cabecera de la recepción y los nombres de los actores
        $reception = $receptionModel->findByIdWithActors($id);

        // 3. Verificar si la recepción existe
        if (!$reception) {
            // Manejo de error: la recepción no fue encontrada. 
            // Podrías redirigir al listado o a una página 404.
            // Ejemplo de redirección:
            // header('Location: ' . BASE_URL . 'receptions');
            // exit;
            
            // Por ahora, solo puedes mostrar un mensaje de error si no existe.
            echo "Error: Recepción con ID {$id} no encontrada.";
            return; 
        }

        // 4. Obtener la lista de ítems (productos) asociados a esta recepción
        $items = $receptionModel->getItemsByReceptionId($id);

        // 5. Cargar la vista de detalle, pasando todos los datos
        $this->render('receptions/detail', [
            'reception' => $reception, // Contiene cabecera y nombres de actores
            'items' => $items,         // Contiene el detalle de productos
            'title' => 'Detalle de Recepción N° ' . $id
        ]);
    }
    /**
     * Formulario de creación
     */
    public function create()
    {
        $inspectors = $this->actorModel->findByRole('inspector');
        $spokespersons = $this->actorModel->findByRole('vocero_parroquial');
        
        // NOTA: Debes añadir 'CLAP', 'FRUVERT', 'PROTEINA' a tu vista 'receptions/form'
        $receptionTypes = ['CLAP', 'FRUVERT', 'PROTEINA']; 

        $this->render('receptions/form', [
            'title' => 'Nueva recepción',
            'inspectors' => $inspectors,
            'spokespersons' => $spokespersons,
            'receptionTypes' => $receptionTypes // Nuevo: Tipos de recepción
        ]);
    }

    /**
     * Guardar recepción (POST)
     * CORRECCIÓN PRINCIPAL AQUÍ:
     * 1. Recibe 'reception_type' y 'summary_quantity'.
     * 2. Elimina la referencia a 'total_bags'.
     * 3. Se asume que el input de ítems (product_name, quantity, unit) viene en un array POST (ej. $_POST['items']).
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Datos de la Cabecera (Maestro)
            $date = $_POST['date'] ?? null;
            $inspector_id = $_POST['inspector_id'] ?? null;
            $vocero_parroquial_id = $_POST['vocero_parroquial_id'] ?? null;
            
            // Nuevos campos
            $reception_type = $_POST['reception_type'] ?? null;
            // Usamos la nueva columna y la convertimos a INT (NULL si está vacía)
            $summary_quantity = (int)($_POST['summary_quantity'] ?? 0); 
            $notes = $_POST['notes'] ?? '';
            
            // Los ítems vienen como un array (Asumimos una estructura en la vista)
            $items = $_POST['items'] ?? []; 
            
            if (!$date || !$inspector_id || !$vocero_parroquial_id || !$reception_type || empty($items)) {
                $this->setFlashMessage('error', 'Faltan datos obligatorios para la recepción o los ítems.');
                header('Location: ' . BASE_URL . 'receptions/create');
                exit;
            }

            // --- INSERCIÓN DE LA CABECERA (MAESTRO) ---
            $receptionId = $this->receptionModel->createReception([
                'date' => $date,
                'inspector_id' => $inspector_id,
                'vocero_parroquial_id' => $vocero_parroquial_id,
                'reception_type' => $reception_type, // Nuevo campo
                // Si no es CLAP, summary_quantity será 0, lo cual se convierte a NULL en la DB
                // si definiste la columna como INT NULL. Si no, déjalo en 0.
                'summary_quantity' => $summary_quantity > 0 ? $summary_quantity : null,
                'notes' => $notes
            ]);
            
            // Si la inserción de la cabecera fue exitosa, procedemos con los ítems
            if ($receptionId) {
                // --- INSERCIÓN DE LOS DETALLES (ITEMS) ---
                foreach ($items as $item) {
                    if (!empty($item['product_name']) && !empty($item['quantity'])) {
                        $this->receptionModel->createReceptionItem($receptionId, $item);
                    }
                }
            }


            $this->setFlashMessage('success', 'Recepción y sus ítems registrados correctamente.');
            header('Location: ' . BASE_URL . 'receptions');
            exit;
        }
    }

    /**
     * Eliminar recepción
     * NOTA: Debido al ON DELETE CASCADE en la DB, al borrar la recepción,
     * todos sus items asociados en 'reception_items' se borrarán automáticamente.
     */
    public function delete(int $id)
    {
        $this->receptionModel->delete($id);
        $this->setFlashMessage('success', 'Recepción eliminada.');
        header('Location: ' . BASE_URL . 'receptions');
        exit;
    }

    /**
     * Generar PDF
     * CORRECCIÓN: Se debe obtener la lista de ítems para el PDF.
     */
    public function pdf(int $id)
    {
        $reception = $this->receptionModel->findByIdWithActors($id);
        $items = $this->receptionModel->getItemsByReceptionId($id); // ¡NUEVO!

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
        // Se pasa la variable $items a la vista pdf.php
        include __DIR__ . '/../Views/receptions/pdf.php'; 
        $html = ob_get_clean();

        // ... resto del código Dompdf ...
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Acta_Recepcion_{$reception->id}.pdf", ["Attachment" => false]);
    }
}