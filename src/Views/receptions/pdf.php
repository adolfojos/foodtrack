<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Recepción N° <?= htmlspecialchars($reception->id) ?></title>
    <style>
        /* Usamos una fuente genérica compatible con Dompdf */
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 30px; }
        
        /* Encabezado */
        h2 { 
            text-align: center; 
            margin-bottom: 5px; 
            color: #333; 
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
        }
        h3 { 
            text-align: center; 
            margin-top: 0; 
            font-size: 14px;
            font-weight: normal;
        }
        
        /* Secciones de Datos Generales */
        .header-info, .actors-info {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            display: block; /* Asegura que ocupe todo el ancho */
        }
        .header-info strong { width: 150px; display: inline-block; }
        .actors-info strong { width: 180px; display: inline-block; }

        /* Tabla de Productos (Ítems) */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 25px; 
            font-size: 10px;
        }
        td, th { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left;
        }
        th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
        }

        /* Observaciones */
        .notes-section { margin-top: 20px; }
        .notes-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 50px;
        }

        /* Firmas */
        .signatures-table { 
            margin-top: 80px; /* Más espacio para las firmas */
            border: none; 
        }
        .signatures-table td {
            border: none;
            padding: 20px 0;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 40px; /* Línea de la firma */
            font-weight: bold;
        }
        .role {
            font-size: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h2>ACTA DE RECEPCIÓN DE VÍVERES</h2>
    <h3>Registro N° <?= htmlspecialchars($reception->id) ?></h3>

    <!-- 1. INFORMACIÓN DE LA RECEPCIÓN -->
    <div class="header-info">
        <strong>Fecha de Recepción:</strong> <?= date('d/m/Y', strtotime($reception->date)) ?><br>
        <strong>Tipo de Recepción:</strong> <?= htmlspecialchars($reception->reception_type) ?><br>
        <?php if ($reception->summary_quantity > 0): ?>
            <strong>Cantidad Resumen (Bolsas):</strong> <?= (int)$reception->summary_quantity ?><br>
        <?php endif; ?>
    </div>

    <!-- 2. ACTORES RESPONSABLES -->
    <div class="actors-info">
        <strong>Inspector Responsable:</strong> <?= htmlspecialchars($reception->inspector_name) ?><br>
        <strong>Vocero Parroquial:</strong> <?= htmlspecialchars($reception->vocero_parroquial_name) ?>
    </div>

    <!-- 3. DETALLE DE PRODUCTOS RECIBIDOS -->
    <table>
        <thead>
            <tr>
                <th colspan="4">DETALLE DE PRODUCTOS RECIBIDOS</th>
            </tr>
            <tr>
                <th style="width: 50%;">Producto</th>
                <th style="width: 20%;">Cantidad</th>
                <th style="width: 15%;">Unidad</th>
                <th style="width: 15%;">Observaciones Ítem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->product_name) ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($item->quantity) ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($item->unit) ?></td>
                        <td>
                            <?php 
                            // Asumiendo que hay una columna 'notes' en reception_items, si no, puedes eliminar esto
                            echo htmlspecialchars($item->notes ?? 'N/A'); 
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No se registraron productos en esta recepción.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 4. OBSERVACIONES GENERALES -->
    <div class="notes-section">
        <strong>Observaciones Generales:</strong>
        <div class="notes-box">
            <?= nl2br(htmlspecialchars($reception->notes)) ?>
        </div>
    </div>
    
    <!-- 5. FIRMAS -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">
                    <?= htmlspecialchars($reception->inspector_name) ?>
                </div>
                <div class="role">Inspector Responsable</div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">
                    <?= htmlspecialchars($reception->vocero_parroquial_name) ?>
                </div>
                <div class="role">Vocero Parroquial</div>
            </td>
        </tr>
    </table>

</body>
</html>