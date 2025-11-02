<?php
require 'vendor/autoload.php';
$userModel = new \App\Models\User();
$userModel->createUser('admin', 'admin123', 'admin');
echo "Admin creado";