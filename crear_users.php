<?php
require 'vendor/autoload.php';

use App\Models\User;

$userModel = new User();

$roles = [
    'admin' => 'admin123',
    'inspector' => 'insp123',
    'vocero_parroquial' => 'vp123',
    'vocero_institucional' => 'vi123',
    'director' => 'dir123'
];

foreach ($roles as $role => $password) {
    $username = $role; // puedes personalizar si necesitas sufijos únicos
    $result = $userModel->createUser($username, $password, $role);
    echo "Usuario '$username' creado con rol '$role'<br>";
}
