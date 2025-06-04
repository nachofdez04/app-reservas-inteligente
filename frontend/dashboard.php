<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../backend/models/reserva.php';

$usuario = $_SESSION['usuario'];
$nombreUsuario = $usuario['nombre'];
$idUsuario = $usuario['id'];

$hoy = date('Y-m-d');
$reservasHoy = Reserva::obtenerReservasDelDia($idUsuario, $hoy);

$page_title = 'Tu día';
$page_type = 'dashboard';

include 'components/header.php';

$current_page = 'dashboard';
include 'components/sidebar.php';
?>

<main class="main">
  <div class="welcome-header">
    <h2>Buenos dias, <?= htmlspecialchars($nombreUsuario) ?></h2>
  </div>
  
  <?php if (isset($_GET['reserva']) && $_GET['reserva'] === 'ok'): ?>
    <div class="alert alert-success">✅ Reserva realizada correctamente.</div>
  <?php endif; ?>
  
  <div class="content-card">
    <h3>Tus reservas para hoy</h3>
    
    <?php if (empty($reservasHoy)): ?>
      <div class="empty-reserva">
        <img src="assets/img/empty.svg" alt="Sin reservas" />
        <p>No tienes reservas para hoy</p>
      </div>
  <?php else: ?>
    <div class="row mt-4">
      <?php foreach ($reservasHoy as $reserva): ?>
        <div class="col-md-4">
          <div class="card-reserva mb-3">
            <h5 class="text-primary">Plaza <?= htmlspecialchars($reserva['plaza']) ?></h5>
            <p><strong><?= htmlspecialchars($reserva['franja']) ?></strong></p>
            <p><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  </div>
  
  <div class="content-card">
    <h3>¿Necesitas alguna reserva más?</h3>
    <div class="d-flex mt-4">
      <a href="mapa.php" class="service-tile">
        <div class="action-icon">
          <i class="fa-solid fa-car"></i>
        </div>
        <p>Parking</p>
      </a>
    </div>
  </div>
</main>

<?php
$additional_js = [];
include 'components/footer.php';
?>
