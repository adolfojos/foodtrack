<?php
require 'vendor/autoload.php';

// Necesitarás ambos modelos para que esto funcione
use App\Models\User;
use App\Models\Actor;

$userModel = new User();
$actorModel = new Actor();

// Definimos los perfiles completos.
// Necesitamos 'full_name' y 'national_id' porque tu tabla 'actors'
// los marca como NOT NULL. Usaremos datos de relleno.
$initial_actors = [
    'admin' => [
        'password' => 'admin123',
        'full_name' => 'Administrador del Sistema',
        'national_id' => 'V-00000001' // ID único de relleno
    ],
    'inspector' => [
        'password' => 'insp123',
        'full_name' => 'Inspector General',
        'national_id' => 'V-00000002' // ID único de relleno
    ],
    'vocero_parroquial' => [
        'password' => 'vp123',
        'full_name' => 'Vocero Parroquial (Carga)',
        'national_id' => 'V-00000003' // ID único de relleno
    ],
    'vocero_institucional' => [
        'password' => 'vi123',
        'full_name' => 'Vocero Institucional (Ejemplo)',
        'national_id' => 'V-00000004' // ID único de relleno
    ],
    'director' => [
        'password' => 'dir123',
        'full_name' => 'Director (Ejemplo)',
        'national_id' => 'V-00000005' // ID único de relleno
    ],
    'cocinero' => [
        'password' => 'cocina123',
        'full_name' => 'Cocinero (Ejemplo)',
        'national_id' => 'V-00000006' // ID único de relleno
    ]
];

echo "Iniciando creación de usuarios y actores...<br>";
echo "================================================<br>";

// Iteramos sobre la lista de perfiles
// El 'rol' (ej: 'admin') se usará también como 'username'
foreach ($initial_actors as $role_name => $data) {
    
    $username = $role_name; // 'admin', 'inspector', etc.
    $password = $data['password'];
    $full_name = $data['full_name'];
    $national_id = $data['national_id'];

    echo "Procesando: <strong>$username</strong>...<br>";

    try {
        // --- PASO 1: Crear el Usuario (Login) ---
        // Asumimos que $userModel->createUser() hashea el password
        // y devuelve el ID del usuario insertado.
        
        $newUserId = $userModel->createUser($username, $password);

        if (!$newUserId) {
            echo "<span style='color:red;'>ERROR: No se pudo crear el usuario (login) '$username'.</span><br><br>";
            continue; // Saltar al siguiente
        }

        echo "-> Usuario (login) '$username' creado con ID: $newUserId.<br>";

        // --- PASO 2: Crear el Actor (Perfil) y enlazarlo ---
        
        // Preparamos los datos para el modelo de Actor
        $actorData = [
            'full_name' => $full_name,
            'national_id' => $national_id,
            'role' => $role_name,
            'user_id' => $newUserId,
            'email' => $username . '@foodtrack.com' // Email de relleno
        ];
        
        // Asumimos que $actorModel->createActor() inserta en la tabla 'actors'
        $newActorId = $actorModel->createActor($actorData);

        if ($newActorId) {
            echo "-> Actor '$full_name' (rol: $role_name) creado y enlazado al usuario $newUserId.<br>";
        } else {
            echo "<span style='color:orange;'>ADVERTENCIA: Se creó el usuario '$username' pero NO se pudo crear el actor.</span><br>";
            // En un sistema real, aquí deberías borrar el usuario creado (rollback)
        }

    } catch (Exception $e) {
        // Captura errores de BD, ej: 'username' o 'national_id' duplicado
        echo "<span style='color:red;'>Error fatal al procesar '$username': " . $e->getMessage() . "</span><br>";
    }
    
    echo "<br>"; // Separador
}

echo "================================================<br>";
echo "Proceso completado.<br>";

?>