<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Consumo</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 6px; text-align: center; }
    </style>
</head>
<body>
    <h2>Reporte Diario de Consumo</h2>

    <div class="section">
        <strong>Fecha:</strong> <?= htmlspecialchars($report->date) ?><br>
        <strong>Institución:</strong> <?= htmlspecialchars($report->institution_name) ?><br>
        <strong>Vocero:</strong> <?= htmlspecialchars($report->spokesperson_name) ?><br>
        <strong>Entrega asociada:</strong> <?= htmlspecialchars($report->delivery_date ?? 'N/A') ?><br>
    </div>

    <div class="section">
        <strong>Menú preparado:</strong><br>
        <p><?= nl2br(htmlspecialchars($report->menu)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Víveres usados</th>
                <th>Proteínas usadas</th>
                <th>Frutas usadas</th>
                <th>Vegetales usados</th>
                <th>Alumnos atendidos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= (int)$report->used_groceries ?></td>
                <td><?= (int)$report->used_proteins ?></td>
                <td><?= (int)$report->used_fruits ?></td>
                <td><?= (int)$report->used_vegetables ?></td>
                <td><?= (int)$report->students_served ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
