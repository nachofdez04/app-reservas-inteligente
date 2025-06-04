<?php
require_once __DIR__ . '/../config/conexion.php';

class Reserva {
    public static function obtenerReservasDelDia($usuarioId, $fecha) {
        global $pdo;
        $sql = "SELECT r.*, p.nombre AS plaza
                FROM reservas r
                INNER JOIN plazas p ON r.plaza_id = p.id
                WHERE r.usuario_id = ? AND r.fecha = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId, $fecha]);
        return $stmt->fetchAll();
    }

    public static function obtenerTodas($usuarioId) {
        global $pdo;
        $sql = "SELECT r.*, p.nombre AS plaza
                FROM reservas r
                INNER JOIN plazas p ON r.plaza_id = p.id
                WHERE r.usuario_id = ?
                ORDER BY r.fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }    public static function obtenerReservasPorFecha($usuarioId, $fecha) {
        global $pdo;
        $sql = "SELECT r.*, p.nombre AS plaza
                FROM reservas r
                INNER JOIN plazas p ON r.plaza_id = p.id
                WHERE r.usuario_id = ? AND r.fecha = ?
                ORDER BY r.franja ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId, $fecha]);
        return $stmt->fetchAll();
    }
    
    public static function obtenerReservasPorRango($usuarioId, $fechaInicio, $fechaFin) {
        global $pdo;
        $sql = "SELECT r.*, p.nombre AS plaza, r.fecha
                FROM reservas r
                INNER JOIN plazas p ON r.plaza_id = p.id
                WHERE r.usuario_id = ? AND r.fecha BETWEEN ? AND ?
                ORDER BY r.fecha ASC, r.franja ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId, $fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }    public static function obtenerPlazasDisponibles($fecha, $franja) {
        global $pdo;
        $sql = "SELECT * FROM plazas
                WHERE bloqueada = 0
                AND id NOT IN (
                    SELECT plaza_id FROM reservas WHERE fecha = ? AND franja = ?
                )
                ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fecha, $franja]);
        return $stmt->fetchAll();
    }

    public static function usuarioTieneReservaEnFecha($usuarioId, $fecha) {
        global $pdo;
        $sql = "SELECT COUNT(*) FROM reservas WHERE usuario_id = ? AND fecha = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId, $fecha]);
        return $stmt->fetchColumn() > 0;
    }public static function cancelar($reservaId, $usuarioId) {
        global $pdo;
        
        $sqlVerificar = "SELECT * FROM reservas WHERE id = ? AND usuario_id = ?";
        $stmtVerificar = $pdo->prepare($sqlVerificar);
        $stmtVerificar->execute([$reservaId, $usuarioId]);
        $reserva = $stmtVerificar->fetch();
        
        if (!$reserva) {
            return false; 
        }
        
        $hoy = date('Y-m-d');
        if ($reserva['fecha'] < $hoy) {
            return false; 
        }
        
        if ($reserva['fecha'] == $hoy) {
            $horaActual = date('H:i');
            
            $horaFin = explode(' - ', $reserva['franja'])[1] ?? '23:59';
            
            if ($horaActual > $horaFin) {
                return false; 
            }
        }
        
        $sql = "DELETE FROM reservas WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$reservaId]);
    }
}
