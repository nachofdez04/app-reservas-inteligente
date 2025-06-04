<?php


session_start();
require_once '../config/conexion.php';
require_once '../models/reserva.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$usuario_id = $_SESSION['id'];
$method = $_SERVER['REQUEST_METHOD'];
$reservaModel = new Reserva();

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($reservaModel, $usuario_id);
            break;
            
        case 'POST':
            handlePostRequest($reservaModel, $usuario_id);
            break;
            
        case 'PUT':
            handlePutRequest($reservaModel, $usuario_id);
            break;
            
        case 'DELETE':
            handleDeleteRequest($reservaModel, $usuario_id);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage()
    ]);
}

function handleGetRequest($reservaModel, $usuario_id) {
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            $fecha_desde = $_GET['fecha_desde'] ?? null;
            $fecha_hasta = $_GET['fecha_hasta'] ?? null;
            $estado = $_GET['estado'] ?? null;
            
            $reservas = $reservaModel->obtenerReservasUsuario(
                $usuario_id, 
                $fecha_desde, 
                $fecha_hasta, 
                $estado
            );
            
            echo json_encode([
                'success' => true,
                'data' => $reservas,
                'total' => count($reservas)
            ]);
            break;
            
        case 'detail':
            $reserva_id = $_GET['id'] ?? null;
            
            if (!$reserva_id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de reserva requerido']);
                return;
            }
            
            $reserva = $reservaModel->obtenerReservaPorId($reserva_id, $usuario_id);
            
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $reserva
            ]);
            break;
            
        case 'stats':
            $stats = $reservaModel->obtenerEstadisticasUsuario($usuario_id);
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}


function handlePostRequest($reservaModel, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required_fields = ['fecha', 'franja_id', 'plaza_id'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Campo requerido: $field"]);
            return;
        }
    }    
    $fecha_reserva = new DateTime($input['fecha']);
    $hoy = new DateTime();
    $hoy->setTime(0, 0, 0);
    
    if ($fecha_reserva < $hoy) {
        http_response_code(400);
        echo json_encode(['error' => 'La fecha de reserva no puede ser anterior a hoy']);
        return;
    }
    
    $diaSemana = date('w', strtotime($input['fecha']));
    if ($diaSemana == 0 || $diaSemana == 6) {
        http_response_code(400);
        echo json_encode(['error' => 'No se puede reservar plaza los fines de semana']);
        return;
    }
    
    $fecha_limite = new DateTime('+7 days');
    $fecha_limite->setTime(0, 0, 0);
    
    if ($fecha_reserva > $fecha_limite) {
        http_response_code(400);
        echo json_encode(['error' => 'No se puede reservar con más de una semana de antelación']);
        return;
    }
    
    if (!$reservaModel->verificarDisponibilidad($input['plaza_id'], $input['fecha'], $input['franja_id'])) {
        http_response_code(409);
        echo json_encode(['error' => 'La plaza no está disponible para la fecha y franja seleccionadas']);
        return;
    }    
    if (Reserva::usuarioTieneReservaEnFecha($usuario_id, $input['fecha'])) {
        http_response_code(409);
        echo json_encode(['error' => 'Ya tienes una reserva para esta fecha. Solo se permite una reserva por día.']);
        return;
    }
    
    $reserva_id = $reservaModel->crearReserva(
        $usuario_id,
        $input['plaza_id'],
        $input['fecha'],
        $input['franja_id']
    );
    
    if ($reserva_id) {
        $reserva = $reservaModel->obtenerReservaPorId($reserva_id, $usuario_id);
        
        echo json_encode([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => $reserva
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear la reserva']);
    }
}


function handlePutRequest($reservaModel, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $reserva_id = $_GET['id'] ?? null;
    
    if (!$reserva_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de reserva requerido']);
        return;
    }
    
    $reserva_actual = $reservaModel->obtenerReservaPorId($reserva_id, $usuario_id);
    if (!$reserva_actual) {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
        return;
    }
    
    $fecha_reserva = new DateTime($reserva_actual['fecha']);
    $hoy = new DateTime();
    
    if ($fecha_reserva < $hoy) {
        http_response_code(400);
        echo json_encode(['error' => 'No se pueden modificar reservas pasadas']);
        return;
    }
    
    $campos_actualizables = ['fecha', 'franja_id', 'plaza_id'];
    $datos_actualizar = [];
    
    foreach ($campos_actualizables as $campo) {
        if (isset($input[$campo])) {
            $datos_actualizar[$campo] = $input[$campo];
        }
    }
    
    if (empty($datos_actualizar)) {
        http_response_code(400);
        echo json_encode(['error' => 'No hay datos para actualizar']);
        return;
    }
    if (isset($datos_actualizar['plaza_id']) || isset($datos_actualizar['fecha']) || isset($datos_actualizar['franja_id'])) {
        $plaza_id = $datos_actualizar['plaza_id'] ?? $reserva_actual['plaza_id'];
        $fecha = $datos_actualizar['fecha'] ?? $reserva_actual['fecha'];
        $franja_id = $datos_actualizar['franja_id'] ?? $reserva_actual['franja_id'];
        
        if (isset($datos_actualizar['fecha'])) {
            $diaSemana = date('w', strtotime($datos_actualizar['fecha']));
            if ($diaSemana == 0 || $diaSemana == 6) {
                http_response_code(400);
                echo json_encode(['error' => 'No se puede reservar plaza los fines de semana']);
                return;
            }
        }
        
        if (!$reservaModel->verificarDisponibilidad($plaza_id, $fecha, $franja_id, $reserva_id)) {
            http_response_code(409);
            echo json_encode(['error' => 'La nueva configuración no está disponible']);
            return;
        }
    }
    
    if ($reservaModel->actualizarReserva($reserva_id, $datos_actualizar)) {
        $reserva_actualizada = $reservaModel->obtenerReservaPorId($reserva_id, $usuario_id);
        
        echo json_encode([
            'success' => true,
            'message' => 'Reserva actualizada exitosamente',
            'data' => $reserva_actualizada
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar la reserva']);
    }
}


function handleDeleteRequest($reservaModel, $usuario_id) {
    $reserva_id = $_GET['id'] ?? null;
    
    if (!$reserva_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de reserva requerido']);
        return;
    }
    
    $reserva = $reservaModel->obtenerReservaPorId($reserva_id, $usuario_id);
    if (!$reserva) {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
        return;
    }
    
    $fecha_reserva = new DateTime($reserva['fecha']);
    $hoy = new DateTime();
    
    if ($fecha_reserva < $hoy) {
        http_response_code(400);
        echo json_encode(['error' => 'No se pueden cancelar reservas pasadas']);
        return;
    }
    
    if ($reservaModel->cancelarReserva($reserva_id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al cancelar la reserva']);
    }
}
?>
