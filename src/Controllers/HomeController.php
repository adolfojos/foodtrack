<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Soporte;

class HomeController extends Controller
{
    private $soporteModel;

    public function __construct()
    {
        parent::__construct(); // Ejecuta el checkAuth() (requiere login)
    }

    /**
     * Muestra la página principal (Dashboard).
     * Acceso: /
     */
    public function index()
    {

    $this->render('dashboard/index', [
        'title'          => 'Dashboard - General Summary',

    ]);
    }
}
