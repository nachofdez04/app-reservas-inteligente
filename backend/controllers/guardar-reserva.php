<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/reserva.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

$usuarioId = $_SESSION['usuario']['id'];
$plazaId = $_POST['plaza_id'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$franja = $_POST['franja'] ?? null;

if (!$plazaId || !$fecha || !$franja) {
    die('Faltan datos obligatorios');
}

$fechaActual = date('Y-m-d');
if ($fecha < $fechaActual) {
    $_SESSION['error'] = 'No se puede reservar en fechas pasadas';
    header('Location: ../../frontend/mapa.php?error=fechaPasada');
    exit;
}

$diaSemana = date('w', strtotime($fecha));
if ($diaSemana == 0 || $diaSemana == 6) {
    $_SESSION['error'] = 'No se puede reservar plaza los fines de semana (sábados y domingos)';
    header('Location: ../../frontend/mapa.php?fecha=' . $fecha . '&franja=' . $franja . '&error=finDeSemana');
    exit;
}

$fechaLimite = date('Y-m-d', strtotime('+7 days'));
if ($fecha > $fechaLimite) {
    $_SESSION['error'] = 'No se puede reservar con más de una semana de antelación. Fecha límite: ' . date('d/m/Y', strtotime($fechaLimite));
    header('Location: ../../frontend/mapa.php?fecha=' . $fecha . '&franja=' . $franja . '&error=fechaFutura');
    exit;
}

if (Reserva::usuarioTieneReservaEnFecha($usuarioId, $fecha)) {
    $_SESSION['error'] = 'Ya tienes una reserva para esta fecha. Solo se permite una reserva por día.';
    header('Location: ../../frontend/mapa.php?fecha=' . $fecha . '&franja=' . $franja . '&error=limiteDiario');
    exit;
}


$stmt = $pdo->prepare("
    SELECT franja FROM reservas
    WHERE plaza_id = ? AND fecha = ?
");
$stmt->execute([$plazaId, $fecha]);
$reservasExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

$reservada = false;
if (!empty($reservasExistentes)) {
    if (in_array('día completo', $reservasExistentes)) {
        $reservada = true;
    } else if ($franja == 'día completo' && (in_array('mañana', $reservasExistentes) || in_array('tarde', $reservasExistentes))) {
        $reservada = true;
    } else if (in_array($franja, $reservasExistentes)) {
        $reservada = true;
    }
}

$stmt2 = $pdo->prepare("SELECT bloqueada FROM plazas WHERE id = ?");
$stmt2->execute([$plazaId]);
$bloqueada = $stmt2->fetchColumn();

if ($bloqueada) {
    $_SESSION['error'] = 'Esta plaza está bloqueada y no está disponible para reservas.';
    header('Location: ../../frontend/mapa.php?fecha=' . $fecha . '&franja=' . $franja . '&error=bloqueada');
    exit;
}

if ($reservada) {
    if ($franja == 'día completo') {
        $_SESSION['error'] = 'No se puede reservar el día completo porque la plaza ya tiene una reserva en alguna franja del día.';
    } else {
        $_SESSION['error'] = 'Esta plaza ya está reservada para la franja horaria seleccionada o el día completo.';
    }
    
    header('Location: ../../frontend/mapa.php?fecha=' . $fecha . '&franja=' . $franja . '&error=reservada');
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO reservas (usuario_id, plaza_id, fecha, franja)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$usuarioId, $plazaId, $fecha, $franja]);

header('Location: ../../frontend/dashboard.php?reserva=ok');
exit;
