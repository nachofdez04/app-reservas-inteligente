<?php
session_start();
require_once '../config/conexion.php';
require_once '../models/reserva.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    $franja = $_GET['franja'] ?? 'mañana';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !strtotime($fecha)) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'Formato de fecha incorrecto']);
        exit;
    }
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'No se puede consultar fechas pasadas']);
        exit;
    }
    
    $diaSemana = date('w', strtotime($fecha));
    if ($diaSemana == 0 || $diaSemana == 6) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'No se puede reservar plaza los fines de semana']);
        exit;
    }
    
    $fechaLimite = date('Y-m-d', strtotime('+7 days'));
    if ($fecha > $fechaLimite) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'No se puede consultar fechas con más de una semana de antelación']);
        exit;
    }
    
    $franjas_validas = ['mañana', 'tarde', 'día completo'];
    if (!in_array($franja, $franjas_validas)) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'Franja horaria inválida']);
        exit;
    }
    $plazas = Reserva::obtenerPlazasDisponibles($fecha, $franja);
    
    header('Content-Type: application/json');
    echo json_encode($plazas);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit;
}
