<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega N° <?= str_pad($delivery->id, 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px 40px; color: #333; }
        
        /* Encabezado */
        .header-container { width: 100%; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { float: left; max-width: 100px; }
        .title-section { text-align: right; float: right; }
        .title-section h2 { margin: 0; font-size: 16px; text-transform: uppercase; color: #2c3e50; }
        .clearfix { clear: both; }

        /* Cajas de Info */
        .info-box { width: 100%; background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }
        .row { margin-bottom: 5px; }
        .label { font-weight: bold; width: 120px; display: inline-block; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: white; padding: 8px; text-align: center; font-size: 11px; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        .center { text-align: center; }
        .total-row { background-color: #e0f2f1; font-weight: bold; }

        /* Firmas */
        .signatures { margin-top: 60px; width: 100%; }
        .sig-box { width: 40%; display: inline-block; text-align: center; vertical-align: top; }
        .sig-line { border-top: 1px solid #000; margin: 0 20px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo">
             <img src="http://imgfz.com/i/u2oODP0.png" width="80" alt="Logo">
        </div>
        <div class="title-section">
            <h2>ACTA DE ENTREGA DE ALIMENTOS</h2>
            <p style="margin:0;">Control N° <?= str_pad($delivery->id, 6, '0', STR_PAD_LEFT) ?></p>
            <p style="margin:0; font-size:11px;">Fecha: <?= date('d/m/Y', strtotime($delivery->date)) ?></p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="info-box">
        <div class="row"><span class="label">Institución:</span> <?= htmlspecialchars($delivery->institution_name) ?></div>
        <div class="row"><span class="label">Parroquia:</span> <?= htmlspecialchars($delivery->parish ?? 'N/A') ?></div>
        <div class="row"><span class="label">Receptor:</span> <?= htmlspecialchars($delivery->receiver_name) ?></div>
        <hr style="border: 0; border-top: 1px solid #ddd; margin: 5px 0;">
        <div class="row">
            <span class="label">Tipo de Rubro:</span> 
            <?= htmlspecialchars($delivery->reception_type) ?>
        </div>
        <div class="row">
            <span class="label">Cant. Entregada:</span>
            <?php if ($delivery->reception_type === 'CLAP'): ?>
                <?= (int)$delivery->qty_groceries ?> Bolsas/Combos
            <?php elseif ($delivery->reception_type === 'PROTEINA'): ?>
                <?= number_format($delivery->qty_proteins, 2, ',', '.') ?> Kg
            <?php elseif ($delivery->reception_type === 'FRUVERT'): ?>
                <?= number_format($delivery->qty_fruits, 2, ',', '.') ?> Kg
            <?php endif; ?>
        </div>
    </div>

    <h3>DETALLE DE PRODUCTOS ENTREGADOS</h3>
    <table>
        <thead>
            <tr>
                <th width="60%">Producto / Alimento</th>
                <th width="20%">Cantidad</th>
                <th width="20%">Unidad</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->product_name) ?></td>
                        <td class="center"><?= number_format($item->quantity, 2, ',', '.') ?></td>
                        <td class="center"><?= htmlspecialchars($item->unit) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="center">No hay detalles registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="font-size: 10px; margin-top: 10px; color: #666;">
        * Nota: Esta acta certifica la recepción conforme de los alimentos arriba detallados para el beneficio de la matrícula escolar.
    </p>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line">ENTREGADO POR</div>
            <small>Responsable de Despacho</small>
        </div>
        <div class="sig-box" style="float: right;">
            <div class="sig-line">RECIBIDO POR</div>
            <small><?= htmlspecialchars($delivery->receiver_name) ?></small><br>
            <small>Sello de la Institución</small>
        </div>
    </div>
</body>
</html>