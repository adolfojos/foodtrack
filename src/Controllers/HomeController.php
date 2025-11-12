<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actor; // Asegúrate de que este importado
use App\Models\Soporte;

class HomeController extends Controller
{
    private $soporteModel;
    private $actorModel; // <-- Agregamos el ActorModel

    public function __construct()
    {
        parent::__construct();
        $this->actorModel = new Actor(); // <-- Inicializamos el ActorModel
        // $this->soporteModel = new Soporte(); // Si necesitas el SoporteModel, asegúrate de inicializarlo
    }

    /**
     * Muestra la página principal (Dashboard).
     * Acceso: /
     */
    public function index()
    {
        // 1. Obtener el conteo de actores por rol
        $actorRoleStats = $this->actorModel->countActorsByRole();
        
        // 2. Opcional: Procesar los datos a un formato más fácil de usar
        $actorStats = [];
        foreach ($actorRoleStats as $stat) {
            $actorStats[$stat->role] = $stat->count;
        }
        
        // Asumo que tu SoporteModel trae los stats de tickets en alguna parte
        // $stats = $this->soporteModel->getTicketStats(); // Ejemplo
        
        // Usaremos variables simuladas si no tienes SoporteModel, para que el código no falle:
        $stats = (object)[
            'total_pendientes' => 5,
            'total_en_proceso' => 12,
            'resueltos_hoy' => 3,
            'total_tickets' => 20
        ];
        
        $this->render('dashboard/index', [
            'title' => 'Dashboard - General Summary',
            'stats' => $stats, // Los stats existentes de tickets
            'actorStats' => $actorStats, // <-- Los nuevos stats de actores
            // ... otras variables como $ultimos_tickets y $ultimos_tickets_proceso
        ]);
    }
}