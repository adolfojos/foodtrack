<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Recepción N° <?= htmlspecialchars($reception->id) ?></title>
    <style>
        /* Estilos generales compatibles con Dompdf */
        body { font-family: sans-serif; font-size: 12px; margin: 20px 40px; color: #333; }
        
        /* Encabezado con Logo y Título */
        .header-container { width: 100%; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width:100%; float: left; }
        .logo img { max-width: 100px; }
        .title-section { width: 80%; float: right; text-align: right; }
        .title-section h2 { margin: 0; font-size: 18px; text-transform: uppercase; color: #2c3e50; }
        .title-section h3 { margin: 5px 0 0; font-size: 14px; font-weight: normal; color: #7f8c8d; }
        
        /* Limpiar flotados */
        .clearfix { clear: both; }

        /* Cajas de Información */
        .info-box {
            width: 100%;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .row { width: 100%; display: block; margin-bottom: 5px; }
        .label { font-weight: bold; width: 180px; display: inline-block; color: #555; }
        .value { color: #000; font-weight: bold; }

        /* Tabla de Productos */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: #fff; padding: 8px; text-align: center; font-size: 11px; text-transform: uppercase; }
        td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; color: #444; }
        td.center { text-align: center; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        /* Notas y Firmas */
        .notes-section { margin-top: 20px; border: 1px solid #ccc; padding: 10px; min-height: 60px; font-style: italic; font-size: 11px; }
        .signatures { margin-top: 60px; width: 100%; }
        .sig-box { width: 45%; display: inline-block; text-align: center; vertical-align: top; }
        .sig-line { border-top: 1px solid #000; margin: 0 20px; padding-top: 5px; font-weight: bold; }
        .sig-role { font-size: 10px; color: #666; margin-top: 2px; }

        /* Destacado para CLAP */
        .highlight-clap { color: #e74c3c; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="logo">
            <img src="http://imgfz.com/i/u2oODP0.png" alt="Logo">
        </div>
        <div class="title-section">
            <h2>ACTA DE RECEPCIÓN - <?= htmlspecialchars($reception->reception_type) ?></h2>
            <h3>Control N° <?= str_pad($reception->id, 6, '0', STR_PAD_LEFT) ?></h3>
            <p style="margin: 5px 0 0; font-size: 11px;">Fecha: <?= date('d/m/Y', strtotime($reception->date)) ?></p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="info-box">
        <div class="row">
            <span class="label">Tipo de Recepción:</span>
            <span class="value"><?= htmlspecialchars($reception->reception_type) ?></span>
        </div>

        <?php if ($reception->reception_type === 'CLAP' && $reception->summary_quantity > 0): ?>
            <div class="row">
                <span class="label">Total Bolsas/Combos:</span>
                <span class="value highlight-clap"><?= number_format($reception->summary_quantity, 0, ',', '.') ?> UNIDADES</span>
            </div>
        <?php elseif ($reception->summary_quantity > 0): ?>
            <div class="row">
                <span class="label">Cantidad Bultos (Ref):</span>
                <span class="value"><?= $reception->summary_quantity ?></span>
            </div>
        <?php endif; ?>

        <div class="row">
            <span class="label">Inspector Responsable:</span>
            <span class="value"><?= htmlspecialchars($reception->inspector_name) ?></span>
        </div>
        <div class="row">
            <span class="label">Vocero Parroquial:</span>
            <span class="value"><?= htmlspecialchars($reception->vocero_parroquial_name) ?></span>
        </div>
    </div>

    <h3 style="border-bottom: 2px solid #ddd; padding-bottom: 5px; color: #444;">DETALLE DEL INVENTARIO INGRESADO</h3>
    <table>
        <thead>
            <tr>
                <th width="50%">Producto</th>
                <th width="25%">Cantidad Total (Kg/Und)</th>
                <th width="25%">Unidad de Medida</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->product_name) ?></td>
                        <td class="center"><strong><?= number_format($item->quantity, 1, ',', '.') ?></strong></td>
                        <td class="center"><?= htmlspecialchars($item->unit) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="center">No hay productos registrados en esta recepción.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($reception->reception_type === 'CLAP' && $reception->summary_quantity > 0): ?>
        <p style="font-size: 10px; color: #666; margin-top: 5px;">
            * Nota: Las cantidades totales reflejadas arriba corresponden a la multiplicación del contenido unitario por las 
            <strong><?= $reception->summary_quantity ?></strong> bolsas recibidas.
        </p>
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <strong>Observaciones:</strong>
        <div class="notes-section">
            <?= !empty($reception->notes) ? nl2br(htmlspecialchars($reception->notes)) : 'Sin observaciones adicionales.' ?>
        </div>
    </div>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"><?= htmlspecialchars($reception->inspector_name) ?></div>
            <div class="sig-role">Inspector (Firma y Sello)</div>
        </div>
        <div class="sig-box" style="float: right;">
            <div class="sig-line"><?= htmlspecialchars($reception->vocero_parroquial_name) ?></div>
            <div class="sig-role">Vocero Parroquial (Firma y Sello)</div>
        </div>
    </div>

</body>
</html>