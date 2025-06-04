<?php
require_once __DIR__ . '/../models/usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';    if (Usuario::existeCorreo($correo)) {
        header('Location: ../../frontend/register.php?error=correo_existe');
        exit;
    }

    if (Usuario::registrar($nombre, $correo, $clave)) {
        header('Location: ../../frontend/login.php?registro=ok');
    } else {
        header('Location: ../../frontend/register.php?error=servidor');
    }
}
