<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Entrega</title>
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
    <h2>Nota de Entrega de Víveres</h2>

    <div class="section">
        <strong>Fecha:</strong> <?= htmlspecialchars($delivery->date) ?><br>
        <strong>Institución:</strong> <?= htmlspecialchars($delivery->institution_name) ?><br>
        <strong>Recepción asociada:</strong> <?= htmlspecialchars($delivery->reception_date ?? 'N/A') ?><br>
        <strong>Receptor:</strong> <?= htmlspecialchars($delivery->receiver_name) ?><br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Víveres</th>
                <th>Proteínas</th>
                <th>Frutas</th>
                <th>Vegetales</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= (int)$delivery->qty_groceries ?></td>
                <td><?= (int)$delivery->qty_proteins ?></td>
                <td><?= (int)$delivery->qty_fruits ?></td>
                <td><?= (int)$delivery->qty_vegetables ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section">
        <strong>Firma del receptor:</strong><br>
        <?= htmlspecialchars($delivery->receiver_signature ?: '_________________________') ?>
    </div>
</body>
</html>
