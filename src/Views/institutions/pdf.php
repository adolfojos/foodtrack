<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Institucional - <?= htmlspecialchars($institution->name ?? 'N/A') ?></title>
    <style>
        /* La fuente Dejavu Sans es necesaria para que Dompdf maneje caracteres UTF-8 (acentos, etc.) */
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; border-bottom: 2px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #f5f5f5; 
            width: 25%; 
            font-weight: bold;
            color: #555;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Ficha Institucional</h2>

    <table>
        <tr>
            <th>Nombre de la Institución</th>
            <td><?= htmlspecialchars($institution->name ?? '-') ?></td>
        </tr>
        <tr>
            <th>Código Plantel</th>
            <td><?= htmlspecialchars($institution->campus_code ?? '-') ?></td>
        </tr>
        <tr>
            <th>Código SICA</th>
            <td><?= htmlspecialchars($institution->sica_code ?? '-') ?></td>
        </tr>
        <tr>
            <th>Municipio</th>
            <td><?= htmlspecialchars($institution->municipality ?? '-') ?></td>
        </tr>
        <tr>
            <th>Parroquia</th>
            <td><?= htmlspecialchars($institution->parish ?? '-') ?></td>
        </tr>
        
        <tr>
            <th>Director(es)</th>
            <td>
                <?php if (!empty($directors)): ?>
                    <?php 
                    $directorInfo = [];
                    foreach ($directors as $director) {
                        // Concatenar nombre completo y cédula para una entrada
                        $directorInfo[] = htmlspecialchars($director->full_name) . 
                                          ' (C.I. ' . htmlspecialchars($director->national_id) . ')';
                    }
                    // Unir las entradas con saltos de línea HTML para el PDF
                    echo implode('<br>', $directorInfo);
                    ?>
                <?php else: ?>
                    - N/A
                <?php endif; ?>
            </td>
        </tr>
        
        <tr>
            <th>Matrícula Total</th>
            <td><?= htmlspecialchars(number_format($institution->total_enrollment ?? 0, 0, '', '.')) ?></td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td><?= htmlspecialchars($institution->phone ?? '-') ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>
                <?php 
                    // Asegurar que 'active' es tratado como booleano o 1/0
                    echo ($institution->active ?? 0) ? 'Activa' : 'Inactiva'; 
                ?>
            </td>
        </tr>
    </table>

    <div class="footer">
        <em>Documento generado automáticamente por FoodTrack el <?= date('d/m/Y H:i:s') ?>.</em>
    </div>
</body>
</html>