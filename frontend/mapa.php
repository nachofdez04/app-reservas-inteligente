<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../backend/config/conexion.php';

$nombreUsuario = $_SESSION['usuario']['nombre'];

$fecha = $_GET['fecha'] ?? null;
$franja = $_GET['franja'] ?? null;

if ($fecha) {
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        header('Location: mapa.php?error=fechaPasada');
        exit;
    }
}

$plazas = $pdo->query("SELECT * FROM plazas ORDER BY nombre ASC")->fetchAll();

$reservadas = [];
$reservasConflicto = [];
if ($fecha && $franja) {
    $stmt = $pdo->prepare("SELECT plaza_id, franja FROM reservas WHERE fecha = ?");
    $stmt->execute([$fecha]);
    $todasReservas = $stmt->fetchAll();
    
    foreach ($todasReservas as $reserva) {
        if ($reserva['franja'] === $franja) {
            $reservadas[] = $reserva['plaza_id'];
        } 
        elseif ($franja === 'día completo' && ($reserva['franja'] === 'mañana' || $reserva['franja'] === 'tarde')) {
            $reservadas[] = $reserva['plaza_id'];
        }
        elseif (($franja === 'mañana' || $franja === 'tarde') && $reserva['franja'] === 'día completo') {
            $reservadas[] = $reserva['plaza_id'];
        }
    }
}

$page_title = 'Mapa de Plazas';
$page_type = 'mapa';

include 'components/header.php';

$current_page = 'mapa';
include 'components/sidebar.php';
?>

<main class="main">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nueva Reserva</h2>
    <div class="action-bar">
      <a href="reservas.php" class="action-bar-btn" title="Ver mis reservas">
        <i class="fa-solid fa-calendar-check"></i>
      </a>
    </div>
  </div>
  
  <?php if (isset($_GET['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div>
          <?php if ($_GET['exito'] === 'reserva'): ?>
            ¡Reserva realizada con éxito! Tu plaza ha sido confirmada.
          <?php else: ?>
            Operación realizada con éxito.
          <?php endif; ?>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>
  
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-exclamation me-2"></i>        <div>        <?php          
        if ($_GET['error'] === 'fechaPasada') {
            echo 'No puedes hacer reservas para fechas pasadas.';
          } elseif ($_GET['error'] === 'finDeSemana') {
            echo $_SESSION['error'] ?? 'No se puede reservar plaza los fines de semana.';
            unset($_SESSION['error']);
          } elseif ($_GET['error'] === 'fechaFutura') {
            echo $_SESSION['error'] ?? 'No se puede reservar con más de una semana de antelación.';
            unset($_SESSION['error']);
          } elseif ($_GET['error'] === 'limiteDiario') {
            echo 'Ya tienes una reserva para esta fecha. Solo se permite una reserva por día.';
          } elseif ($_GET['error'] === 'reservaExiste' && isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
          } elseif ($_GET['error'] === 'bloqueada' && isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
          } else {
            echo 'Ha ocurrido un error al procesar tu reserva. Por favor, inténtalo de nuevo.';
          }
        ?>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <div class="content-card">
    <div class="alert alert-info mb-0">
      <div class="d-flex">
        <i class="fa-solid fa-circle-info me-3" style="font-size: 1.5rem;"></i>
        <div>
          <h5 class="mb-2">¿Cómo reservar una plaza?</h5>
          <ol class="mb-0">
            <li>Selecciona la fecha deseada para tu reserva</li>
            <li>Elige la franja horaria que necesitas</li>
            <li>Haz clic en "Mostrar Mapa" para ver las plazas disponibles</li>
            <li>Selecciona una plaza que esté libre</li>
            <li>Confirma tu reserva</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <form method="GET" class="content-card calendario-form">
    <div class="calendario-header mb-4">
      <h4 class="mb-0">
        <i class="fa-solid fa-calendar-days me-2" style="color: #2ca0f7;"></i>
        Selecciona fecha y horario
      </h4>
      <p class="text-muted mb-0">Elige cuándo quieres reservar tu plaza de parking</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-group-enhanced">
          <label for="fecha" class="form-label-enhanced">
            <i class="fa-solid fa-calendar-day me-2"></i>
            Fecha de reserva
          </label>
          <div class="date-input-wrapper">
            <input type="date" id="fecha" name="fecha" class="form-control-enhanced" 
                   value="<?= htmlspecialchars($fecha ?? '') ?>" required>
            <div class="input-icon">
              <i class="fa-solid fa-calendar-alt"></i>
            </div>
          </div>
          <small class="form-text text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            Solo se puede reservar de lunes a viernes, hasta una semana de antelación
          </small>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group-enhanced">
          <label for="franja" class="form-label-enhanced">
            <i class="fa-solid fa-clock me-2"></i>
            Franja horaria
          </label>
          <div class="select-wrapper">
            <select id="franja" name="franja" class="form-select-enhanced" required>
              <option value="">Selecciona una franja</option>
              <option value="mañana" <?= $franja === 'mañana' ? 'selected' : '' ?>>
                🌅 Mañana (08:00 - 14:00)
              </option>
              <option value="tarde" <?= $franja === 'tarde' ? 'selected' : '' ?>>
                🌇 Tarde (14:00 - 20:00)
              </option>
              <option value="día completo" <?= $franja === 'día completo' ? 'selected' : '' ?>>
                🌞 Día completo (08:00 - 20:00)
              </option>
            </select>
            <div class="select-icon">
              <i class="fa-solid fa-chevron-down"></i>
            </div>
          </div>
          <small class="form-text text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            El día completo incluye ambas franjas horarias
          </small>
        </div>
      </div>
    </div>
    
    <div class="form-actions mt-4">
      <a href="reservas.php" class="btn btn-outline-secondary btn-enhanced">
        <i class="fa-solid fa-arrow-left me-2"></i> 
        Volver a reservas
      </a>
      <button type="submit" class="btn btn-primary btn-enhanced btn-search">
        <i class="fa-solid fa-search me-2"></i> 
        Buscar plazas disponibles
        <span class="btn-ripple"></span>
      </button>
    </div>
  </form>

  <?php if ($fecha && $franja): ?>
    <div class="content-card">
      <h3>Plazas disponibles - <?= date('d/m/Y', strtotime($fecha)) ?> (<?= ucfirst($franja) ?>)</h3>
      
      <div class="mapa-leyenda mb-3">
        <div class="d-flex gap-3 align-items-center">
          <div class="d-flex align-items-center">
            <div class="plaza disponible-mini me-1"></div>
            <small>Disponible</small>
          </div>
          <div class="d-flex align-items-center">
            <div class="plaza reservada-mini me-1"></div>
            <small>Reservada</small>
          </div>
          <div class="d-flex align-items-center">
            <div class="plaza bloqueada-mini me-1"></div>
            <small>Bloqueada</small>
          </div>
        </div>
      </div>
      
      <div class="mapa-grid">
        <?php foreach ($plazas as $plaza):
          $estado = 'disponible';
          $tooltip = '';
          
          if ($plaza['bloqueada']) {
              $estado = 'bloqueada';
              $tooltip = 'Esta plaza está bloqueada para reservas';
          } elseif (in_array($plaza['id'], $reservadas)) {
              $estado = 'reservada';
              if ($franja === 'día completo') {
                  $tooltip = 'Esta plaza ya está reservada en alguna franja de este día';
              } else {
                  $tooltip = 'Esta plaza ya está reservada para la franja seleccionada o el día completo';
              }
          } else {
              $tooltip = 'Haz clic para reservar esta plaza';
          }
        ?>
          <form method="POST" action="../backend/controllers/guardar-reserva.php" style="display: inline;">
            <input type="hidden" name="plaza_id" value="<?= $plaza['id'] ?>">
            <input type="hidden" name="fecha" value="<?= $fecha ?>">
            <input type="hidden" name="franja" value="<?= $franja ?>">
            <button type="<?= $estado === 'disponible' ? 'submit' : 'button' ?>"
                    class="plaza <?= $estado ?>"                    <?= $estado !== 'disponible' ? 'disabled' : '' ?>
                    title="<?= $tooltip ?>">
              <i class="fa-solid fa-car me-1"></i>
              <?= htmlspecialchars($plaza['nombre']) ?>
            </button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="content-card">
      <div class="empty-reserva">
        <img src="assets/img/empty.svg" alt="Seleccionar fecha" />
        <p>Selecciona una fecha y franja horaria para ver las plazas disponibles</p>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php
$additional_js = ['reservas.js'];
include 'components/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const hoy = new Date();
    const año = hoy.getFullYear();
    let mes = hoy.getMonth() + 1;
    let dia = hoy.getDate();
    
    if (mes < 10) mes = '0' + mes;
    if (dia < 10) dia = '0' + dia;
    
    const fechaHoy = `${año}-${mes}-${dia}`;
    fechaInput.setAttribute('min', fechaHoy);
    
    const fechaLimite = new Date();
    fechaLimite.setDate(fechaLimite.getDate() + 7);
    const añoLimite = fechaLimite.getFullYear();
    let mesLimite = fechaLimite.getMonth() + 1;
    let diaLimite = fechaLimite.getDate();
    
    if (mesLimite < 10) mesLimite = '0' + mesLimite;
    if (diaLimite < 10) diaLimite = '0' + diaLimite;
    
    const fechaMaxima = `${añoLimite}-${mesLimite}-${diaLimite}`;
    fechaInput.setAttribute('max', fechaMaxima);
    
    if (!fechaInput.value || new Date(fechaInput.value) < new Date(fechaHoy)) {
        fechaInput.value = fechaHoy;
    }
    fechaInput.addEventListener('change', function() {
        const fechaSeleccionada = new Date(this.value);
        const fechaLimiteObj = new Date(fechaMaxima);
        
        if (fechaSeleccionada > fechaLimiteObj) {
            alert('No puedes reservar con más de una semana de antelación. Fecha límite: ' + fechaLimiteObj.toLocaleDateString('es-ES'));
            this.value = fechaMaxima;
            return;
        }
        
        const diaSemana = fechaSeleccionada.getDay();
        if (diaSemana === 0 || diaSemana === 6) {
            alert('No se puede reservar plaza los fines de semana (sábados y domingos).');
            this.value = fechaHoy;
        }
    });
    
    if (fechaInput.value && !document.getElementById('franja').value) {
        document.getElementById('franja').focus();
    }
    
    const mapaGrid = document.querySelector('.mapa-grid');
    if (mapaGrid) {
        mapaGrid.querySelectorAll('.plaza').forEach((plaza, index) => {
            plaza.style.opacity = '0';
            plaza.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                plaza.style.transition = 'all 0.3s ease';
                plaza.style.opacity = '1';
                plaza.style.transform = 'scale(1)';
            }, 50 * index);
        });
    }
});
</script>