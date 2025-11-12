<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use Dompdf\Dompdf;
use Dompdf\Options;

class DailyReport extends Model
{
    protected $table = 'daily_reports';

    public function createReport(array $data)
    {
        return $this->create($data);
    }

    public function findAllWithRelations()
    {
        $sql = "
            SELECT dr.*, 
                   i.name AS institution_name,
                   d.date AS delivery_date,
                   a.full_name AS spokesperson_name
            FROM daily_reports dr
            INNER JOIN institutions i ON dr.institution_id = i.id
            LEFT JOIN deliveries d ON dr.delivery_id = d.id
            INNER JOIN actors a ON dr.spokesperson_id = a.id
            ORDER BY dr.date DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByIdWithRelations(int $id)
    {
        $sql = "
            SELECT dr.*, 
                   i.name AS institution_name,
                   d.date AS delivery_date,
                   a.name AS spokesperson_name
            FROM daily_reports dr
            INNER JOIN institutions i ON dr.institution_id = i.id
            LEFT JOIN deliveries d ON dr.delivery_id = d.id
            INNER JOIN actors a ON dr.spokesperson_id = a.id
            WHERE dr.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function pdf(int $id)
    {
        $report = $this->reportModel->findByIdWithRelations($id);

        if (!$report) {
            $this->setFlashMessage('error', 'Reporte no encontrado.');
            header('Location: ' . BASE_URL . 'dailyreports');
            exit;
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizamos la vista como HTML
        ob_start();
        include __DIR__ . '/../../Views/dailyreports/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador (no descarga automática)
        $dompdf->stream("Reporte_Diario_{$report->id}.pdf", ["Attachment" => false]);
    }
    
    public function getConsolidatedByInstitution(int $institution_id, string $start_date, string $end_date)
    {
        $sql = "
            SELECT 
                i.name AS institution_name,
                SUM(dr.used_groceries) AS total_groceries,
                SUM(dr.used_proteins) AS total_proteins,
                SUM(dr.used_fruits) AS total_fruits,
                SUM(dr.used_vegetables) AS total_vegetables,
                SUM(dr.students_served) AS total_students
            FROM daily_reports dr
            INNER JOIN institutions i ON dr.institution_id = i.id
            WHERE dr.institution_id = ?
                AND dr.date BETWEEN ? AND ?
            GROUP BY i.name
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$institution_id, $start_date, $end_date]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
