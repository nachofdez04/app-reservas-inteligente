<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    public static function registrar($nombre, $correo, $clave) {
        global $pdo;
        $claveHasheada = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, correo, clave) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $correo, $claveHasheada]);
    }

    public static function login($correo, $clave) {
        global $pdo;
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            return $usuario;
        }
        return false;
    }

    public static function existeCorreo($correo) {
        global $pdo;
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetchColumn() > 0;
    }
}
