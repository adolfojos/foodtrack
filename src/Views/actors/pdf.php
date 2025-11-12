<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha del Actor - <?php echo htmlspecialchars($actor->full_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Ficha del Actor</h1>
    <div class="section">
        <h2>Información Personal</h2>
        <p><span class="label">Nombre Completo:</span> <?php echo htmlspecialchars($actor->full_name); ?></p>
        <p><span class="label">Rol:</span> <?php echo htmlspecialchars($actor->role); ?></p>
        <p><span class="label">Activo:</span> <?php echo $actor->active ? 'Sí' : 'No'; ?></p>
    </div>
    <div class="section">
        <h2>Institución Vinculada</h2>
        <p><?php echo $actor->institution_name ? htmlspecialchars($actor->institution_name) : 'Ninguna'; ?></p>
    </div>
    <div class="section">
        <h2>Usuario Vinculado</h2>
        <p><?php echo $actor->user_name ? htmlspecialchars($actor->user_name) : 'Ninguno'; ?></p>
    </div>
</body>
</html>