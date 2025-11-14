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
        /* Estilos para la lista de instituciones */
        .institution-list {
            list-style: disc;
            margin-left: 20px;
            padding-left: 0;
        }
        .institution-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Ficha del Actor</h1>
    <div class="section">
        <h2>Información Personal</h2>
        <p><span class="label">Nombre Completo:</span> <?php echo htmlspecialchars($actor->full_name); ?></p>
        <p><span class="label">Rol:</span> <?php 
            // Implementación simple del match si tu motor de PDF lo soporta, o usar if/else
            echo match ($actor->role ?? null) {
                'admin' => 'Administrador',
                'inspector' => 'Inspector',
                'vocero_parroquial' => 'Vocero Parroquial',
                'vocero_institucional' => 'Vocero Institucional',
                'director' => 'Director',
                'cocinero' => 'Cocinero/a',
                default => htmlspecialchars($actor->role ?? '-'),
            }; 
        ?></p>
        <p><span class="label">Activo:</span> <?php echo $actor->active ? 'Sí' : 'No'; ?></p>
    </div>
    
    <div class="section">
        <h2>Instituciones Vinculadas</h2>
        <?php if (!empty($institutions)): ?>
            <ul class="institution-list">
                <?php foreach ($institutions as $inst): ?>
                    <li>
                        <?php echo htmlspecialchars($inst->name); ?> (ID: <?php echo $inst->id; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Ninguna institución vinculada.</p>
        <?php endif; ?>
    </div>
    <div class="section">
        <h2>Usuario Vinculado</h2>
        <p><?php echo $actor->user_name ? htmlspecialchars($actor->user_name) : 'Ninguno'; ?></p>
    </div>
</body>
</html>