<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Recepción</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        .section { margin-bottom: 20px; }
        .signature { margin-top: 50px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 6px; }
    </style>
</head>
<body>
    <h2>Acta de Recepción de Víveres</h2>

    <div class="section">
        <strong>Fecha:</strong> <?= htmlspecialchars($reception->date) ?><br>
        <strong>Total de bolsas:</strong> <?= (int)$reception->total_bags ?><br>
    </div>

    <div class="section">
        <strong>Inspector responsable:</strong> <?= htmlspecialchars($reception->inspector_name) ?><br>
        <strong>Vocero parroquial:</strong> <?= htmlspecialchars($reception->vocero_parroquial_name) ?><br>
    </div>

    <div class="section">
        <strong>Observaciones:</strong><br>
        <p><?= nl2br(htmlspecialchars($reception->notes)) ?></p>
    </div>

    <table>
        <tr>
            <th>Inspector</th>
            <th>Vocero Parroquial</th>
        </tr>
        <tr>
            <td class="signature">_________________________<br><?= htmlspecialchars($reception->inspector_name) ?></td>
            <td class="signature">_________________________<br><?= htmlspecialchars($reception->vocero_parroquial_name) ?></td>
        </tr>
    </table>
</body>
</html>
