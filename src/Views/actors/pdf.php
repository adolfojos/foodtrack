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
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px; /* Ayuda a alinear las etiquetas */
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
    <h1>Ficha del Actor: <?php echo htmlspecialchars($actor->full_name); ?></h1>

    <div class="section">
        <h2>Información Personal</h2>
        <p><span class="label">ID Actor:</span> <?php echo htmlspecialchars($actor->id); ?></p>
        <p><span class="label">Nombre Completo:</span> <?php echo htmlspecialchars($actor->full_name); ?></p>
        <p><span class="label">Cédula:</span> <?php echo htmlspecialchars($actor->national_id ?? '-'); ?></p>
        <p><span class="label">Activo:</span> <?php echo $actor->active ? 'Sí' : 'No'; ?></p>
    </div>
    
    <div class="section">
        <h2>Información de Rol y Acceso</h2>
        <p><span class="label">Rol Asignado:</span> <?php 
            // Manejo del rol para la presentación final
            $role_display = match ($actor->role ?? null) {
                'admin' => 'Administrador',
                'inspector' => 'Inspector',
                'vocero_parroquial' => 'Vocero Parroquial',
                'vocero_institucional' => 'Vocero Institucional',
                'director' => 'Director',
                'cocinero' => 'Cocinero/a',
                default => htmlspecialchars($actor->role ?? 'No Definido'),
            };
            echo $role_display; 
        ?></p>
        <p><span class="label">Usuario de Acceso:</span> 
            <?php echo htmlspecialchars($actor->user_name ?? 'Ninguno (No tiene cuenta de login)'); ?>
        </p>
        <?php if ($actor->user_name): ?>
            <p><span class="label">ID de Usuario (Login):</span> <?php echo htmlspecialchars($actor->user_id ?? 'N/A'); ?></p>
        <?php endif; ?>
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
</body>
</html>