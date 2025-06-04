<?php
require_once __DIR__ . '/../models/usuario.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    $usuario = Usuario::login($correo, $clave);    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        header('Location: ../../frontend/dashboard.php');
        exit;
    } else {
        header('Location: ../../frontend/login.php?error=credenciales');
        exit;
    }
}
