<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Institucional</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <h2>Ficha Institucional</h2>

    <table>
        <tr><th>Nombre</th><td><?= htmlspecialchars($institution->name) ?></td></tr>
        <tr><th>Codigo Plante</th><td><?= htmlspecialchars($institution->campus_code) ?></td></tr>
        <tr><th>Codigo Sica</th><td><?= htmlspecialchars($institution->sica_code) ?></td></tr>
        <tr><th>Municipio</th><td><?= htmlspecialchars($institution->municipality) ?></td></tr>
        <tr><th>Parroquia</th><td><?= htmlspecialchars($institution->parish) ?></td></tr>
        <tr><th>Director</th><td><?= htmlspecialchars($institution->director) ?></td></tr>
        <tr><th>Matricula</th><td><?= !empty($institution->total_enrollment) ? htmlspecialchars($institution->total_enrollment) : '-' ?></td></tr>
        <tr><th>Teléfono</th><td><?= htmlspecialchars($institution->phone) ?></td></tr>
        <tr><th>Estado</th><td><?= $institution->active ? 'Activa' : 'Inactiva' ?></td></tr>
    </table>

    <div class="section" style="margin-top:40px;">
        <em>Documento generado automáticamente por FoodTrack.</em>
    </div>
</body>
</html>
