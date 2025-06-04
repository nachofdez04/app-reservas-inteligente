<?php
$usuario_nombre = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Usuario';
$usuario_email = isset($_SESSION['usuario']['correo']) ? $_SESSION['usuario']['correo'] : '';
$usuario_iniciales = strtoupper(substr($usuario_nombre, 0, 1));

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="sidebar">
    <div>
        <a href="dashboard.php" class="logo">
            <i class="fa-solid fa-car"></i> ParkingTFG
        </a>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fa-solid fa-calendar-day"></i>
                Tu día
            </a>
            <a class="nav-link <?php echo ($current_page == 'reservas') ? 'active' : ''; ?>" href="reservas.php">
                <i class="fa-solid fa-calendar-check"></i>
                Reservas
            </a>
        </nav>
    </div>
    
    <div class="user-profile">
        <div class="d-flex align-items-center">
            <div class="user-avatar" title="Perfil de <?php echo htmlspecialchars($usuario_nombre); ?>">
                <?php echo $usuario_iniciales; ?>
            </div>            <div class="ms-2 d-none d-md-block">
                <small class="text-white"><?php echo htmlspecialchars($usuario_nombre); ?></small>
            </div>
        </div>
        <div class="profile-dropdown">
            <div class="profile-info">
                <h4><?php echo htmlspecialchars($usuario_nombre); ?></h4>
                <?php if ($usuario_email): ?>
                    <p><i class="fa-solid fa-envelope me-1"></i><?php echo htmlspecialchars($usuario_email); ?></p>
                <?php endif; ?>
                <p><i class="fa-solid fa-clock me-1"></i>Última conexión: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
            
            <a href="#" class="logout logout-btn">
                <i class="fa-solid fa-sign-out-alt me-2"></i>
                Cerrar Sesión
            </a>
        </div>
    </div>
</div>

<div class="logout-confirm">
    <div class="logout-dialog">
        <h4>¿Cerrar sesión?</h4>
        <p>¿Estás seguro de que quieres cerrar la sesión actual? Tendrás que volver a iniciar sesión para acceder al sistema.</p>
        <div class="logout-actions">
            <button class="btn-cancel-logout">
                <i class="fa-solid fa-times"></i>
                Cancelar
            </button>
            <a href="../backend/controllers/logout.php" class="btn-confirm-logout">
                <i class="fa-solid fa-sign-out-alt"></i>
                Cerrar sesión
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
});
</script>
