<?php
session_start();
require_once '../models/reserva.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario']['id'];
    $reserva_id = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT);
    
    $fecha_retorno = isset($_POST['fecha_retorno']) ? $_POST['fecha_retorno'] : '';
    
    if (!$reserva_id) {
        $_SESSION['error'] = 'ID de reserva inválido';
        header('Location: ../../frontend/reservas.php');
        exit;
    }
    
    $resultado = Reserva::cancelar($reserva_id, $usuario_id);
    if ($resultado) {
        $_SESSION['exito'] = 'Reserva cancelada correctamente. La plaza ha sido liberada y está disponible para otros usuarios.';
    } else {
        $_SESSION['error'] = 'No se pudo cancelar la reserva. Por favor, verifica que la reserva exista y sea para una fecha futura.';
    }
    
    if (!empty($fecha_retorno)) {
        header('Location: ../../frontend/reservas.php?fecha=' . $fecha_retorno);
    } else {
        header('Location: ../../frontend/reservas.php');
    }
    exit;
} else {
    header('Location: ../../frontend/reservas.php');
    exit;
}
