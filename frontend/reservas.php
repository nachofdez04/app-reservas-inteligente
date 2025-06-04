<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../backend/models/reserva.php';

$usuarioId = $_SESSION['usuario']['id'];
$nombreUsuario = $_SESSION['usuario']['nombre'];

if (isset($_GET['mes']) && isset($_GET['anio'])) {
    $mesActual = (int)$_GET['mes'];
    $anioActual = (int)$_GET['anio'];
    
    if ($mesActual < 1) {
        $mesActual = 12;
        $anioActual--;
    } elseif ($mesActual > 12) {
        $mesActual = 1;
        $anioActual++;
    }
    
} else {
    $mesActual = date('n');
    $anioActual = date('Y');
}

$diaActual = date('j');

if (isset($_GET['fecha'])) {
    $fechaSeleccionada = $_GET['fecha'];
    
    $mesSeleccionado = date('n', strtotime($fechaSeleccionada));
    $anioSeleccionado = date('Y', strtotime($fechaSeleccionada));
    
    if ($mesSeleccionado != $mesActual || $anioSeleccionado != $anioActual) {
        $mesActual = $mesSeleccionado;
        $anioActual = $anioSeleccionado;
    }
} else {
    if (isset($_GET['mes']) && isset($_GET['anio'])) {
        $fechaSeleccionada = sprintf('%04d-%02d-01', $anioActual, $mesActual);
    } else {
        $fechaSeleccionada = date('Y-m-d');
    }
}

$primerDia = strtotime("$anioActual-$mesActual-01");
$ultimoDia = strtotime(date('Y-m-t', $primerDia));

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
$nombreMes = strftime('%B', $primerDia);
$nombreMes = ucfirst($nombreMes); 
if ($nombreMes === false) { 
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $nombreMes = $meses[$mesActual - 1];
}

$primerDiaSemana = date('w', $primerDia);
if ($primerDiaSemana == 0) {
    $primerDiaSemana = 7; 
}

$casillasVacias = $primerDiaSemana - 1;

$totalDias = date('t', $primerDia);

$reservasDia = Reserva::obtenerReservasPorFecha($usuarioId, $fechaSeleccionada);

$inicioMes = "$anioActual-$mesActual-01";
$finMes = date('Y-m-t', strtotime($inicioMes));
$reservasMes = Reserva::obtenerReservasPorRango($usuarioId, $inicioMes, $finMes);

$diasConReservas = [];
foreach ($reservasMes as $reserva) {
    $diaMes = (int)date('j', strtotime($reserva['fecha']));
    $diasConReservas[$diaMes] = true;
}

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
$fechaMostrar = strftime('%e de %B de %Y', strtotime($fechaSeleccionada));
if ($fechaMostrar === false) {
    $mesesEsp = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto', 
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    $mes = date('n', strtotime($fechaSeleccionada));
    $dia = date('j', strtotime($fechaSeleccionada));
    $anio = date('Y', strtotime($fechaSeleccionada));
    $fechaMostrar = "$dia de {$mesesEsp[$mes]} de $anio";
}

if ($mesActual == 1) {
    $urlMesAnterior = "?mes=12&anio=" . ($anioActual - 1);
} else {
    $urlMesAnterior = "?mes=" . ($mesActual - 1) . "&anio=$anioActual";
}

if ($mesActual == 12) {
    $urlMesSiguiente = "?mes=1&anio=" . ($anioActual + 1);
} else {
    $urlMesSiguiente = "?mes=" . ($mesActual + 1) . "&anio=$anioActual";
}

$page_title = 'Reservas';
$page_type = 'reservas';

include 'components/header.php';

$current_page = 'reservas';
include 'components/sidebar.php';
?>

<main class="main">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tus reservas</h2>
    <div class="action-bar">
      <a href="mapa.php" class="action-bar-btn" title="Nueva reserva">
        <i class="fa-solid fa-plus"></i>
      </a>
    </div>
  </div>
  
  <?php if (isset($_SESSION['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div><?= htmlspecialchars($_SESSION['exito']) ?></div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <div><?= htmlspecialchars($_SESSION['error']) ?></div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div class="row">
    <div class="col-lg-7">
      <div class="content-card">
        <div class="calendar-header">
          <a href="<?= $urlMesAnterior ?>" class="btn btn-navigation" title="Mes anterior">
            <i class="fa-solid fa-chevron-left"></i>
          </a>
          <h3 class="mb-0"><?= $nombreMes ?> <?= $anioActual ?></h3>
          <a href="<?= $urlMesSiguiente ?>" class="btn btn-navigation" title="Mes siguiente">
            <i class="fa-solid fa-chevron-right"></i>
          </a>
        </div>
        
        <table class="calendar-table mt-3">
          <thead>
            <tr>
              <th>Lu</th>
              <th>Ma</th>
              <th>Mi</th>
              <th>Ju</th>
              <th>Vi</th>
              <th>Sa</th>
              <th>Do</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php 
              for ($i = 0; $i < $casillasVacias; $i++) {
                  echo '<td></td>';
              }
              
              $contadorDias = $casillasVacias;
              
              for ($dia = 1; $dia <= $totalDias; $dia++) {
                  $fechaDia = date('Y-m-d', strtotime("$anioActual-$mesActual-$dia"));
                  $esDiaSeleccionado = ($fechaDia === $fechaSeleccionada);
                  $esHoy = ($dia == date('j') && $mesActual == date('n') && $anioActual == date('Y'));
                  $tieneReservas = isset($diasConReservas[$dia]);
                  
                  $diaSemana = date('w', strtotime($fechaDia));
                  $esFinDeSemana = ($diaSemana == 0 || $diaSemana == 6);
                  
                  $esDiaPasado = strtotime($fechaDia) < strtotime(date('Y-m-d'));
                  
                  $fechaLimite = date('Y-m-d', strtotime('+7 days'));
                  $esFechaLejana = strtotime($fechaDia) > strtotime($fechaLimite);
                  
                  $clasesDia = 'calendar-day';
                  if ($esDiaSeleccionado) $clasesDia .= ' selected';
                  if ($esHoy) $clasesDia .= ' today';
                  if ($esFinDeSemana) $clasesDia .= ' weekend';
                  if ($esDiaPasado) $clasesDia .= ' past';
                  if ($esFechaLejana) $clasesDia .= ' distant-future';
                  
                  $esClickeable = !$esDiaPasado && !$esFechaLejana && !$esFinDeSemana;
                  
                  echo '<td>';
                  if ($esClickeable) {
                      echo '<a href="?fecha=' . $fechaDia . '" class="' . $clasesDia . '">';
                  } else {
                      $tooltipTexto = '';
                      if ($esDiaPasado) {
                          $tooltipTexto = 'Fecha pasada';
                      } elseif ($esFinDeSemana) {
                          $tooltipTexto = 'No se puede reservar los fines de semana';
                      } elseif ($esFechaLejana) {
                          $tooltipTexto = 'No se puede reservar con más de una semana de antelación';
                      }
                      echo '<span class="' . $clasesDia . '" title="' . $tooltipTexto . '">';
                  }
                  
                  echo $dia;
                            
                  if ($tieneReservas) {
                      echo '<span class="dot-indicator"></span>';
                  }
                  
                  if ($esClickeable) {
                      echo '</a>';
                  } else {
                      echo '</span>';
                  }
                  
                  echo '</td>';
                  
                  $contadorDias++;
                  
                  if ($contadorDias % 7 === 0) {
                      echo '</tr><tr>';
                  }
              }
              
              $celdasRestantes = 7 - ($contadorDias % 7);
              if ($celdasRestantes < 7) {
                  for ($i = 0; $i < $celdasRestantes; $i++) {
                      echo '<td></td>';
                  }
              }
              ?>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="col-lg-5">
      <div class="content-card h-100 d-flex flex-column">
        <div class="fecha-header mb-4 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <i class="fa-regular fa-calendar-check me-2" style="font-size: 1.5rem; color: #2ca0f7;"></i>
            <h4 class="mb-0"><?= $fechaMostrar ?></h4>
          </div>
          <?php if ($fechaSeleccionada != date('Y-m-d')): ?>
          <a href="?fecha=<?= date('Y-m-d') ?>" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-calendar-day me-1"></i> Hoy
          </a>
          <?php endif; ?>
        </div>
        
        <?php if (empty($reservasDia)): ?>
          <?php 
            $esFechaFutura = strtotime($fechaSeleccionada) > strtotime(date('Y-m-d'));
            $esHoy = $fechaSeleccionada == date('Y-m-d');
          ?>
          <div class="empty-reserva my-auto">
            <img src="assets/img/empty.svg" alt="Sin reservas" />
          <?php if ($esFechaFutura || $esHoy): ?>
              <p class="mt-3">No tienes reservas para <?= $esHoy ? 'hoy' : 'el día seleccionado' ?>.</p>
              <a href="mapa.php?fecha=<?= $fechaSeleccionada ?>" class="btn btn-primary mt-2 animated-btn">
                <i class="fa-solid fa-plus me-1"></i> Hacer una reserva
              </a>
            <?php else: ?>
              <p class="mt-3">No tuviste reservas para este día.</p>
              <a href="mapa.php" class="btn btn-sm btn-outline-secondary mt-2">
                <i class="fa-solid fa-calendar me-1"></i> Ver fechas disponibles
              </a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="reservas-lista">
            <?php foreach($reservasDia as $reserva): ?>
              <div class="reserva-item">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="d-flex align-items-center">
                      <?php if ($reserva['fecha'] < date('Y-m-d')): ?>
                        <i class="fa-solid fa-car-side me-2" style="color: #6c757d;"></i>
                      <?php else: ?>
                        <i class="fa-solid fa-car-side me-2" style="color: #2ca0f7;"></i>
                      <?php endif; ?>
                      <h5>Plaza <?= htmlspecialchars($reserva['plaza']) ?></h5>
                    </div>
                    <div class="reserva-info">
                      <p class="mb-1">
                        <i class="fa-regular fa-clock me-1" style="color: #666;"></i>
                        <?= htmlspecialchars($reserva['franja']) ?>
                      </p>
                      <?php
                        $horas = explode(" - ", $reserva['franja']);
                        $horaInicio = isset($horas[0]) ? $horas[0] : "";
                        $horaFin = isset($horas[1]) ? $horas[1] : "";
                        
                        $esHoy = $reserva['fecha'] == date('Y-m-d');
                        $horaActual = date('H:i');
                        $reservaActiva = $esHoy && $horaActual >= $horaInicio && $horaActual <= $horaFin;
                      ?>
                      
                      <?php if ($reservaActiva): ?>
                        <div class="estado-reserva activa">
                          <span class="pulso"></span> Reserva activa
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php if ($reserva['fecha'] >= date('Y-m-d')): ?>
                    <form action="../backend/controllers/cancelar-reserva.php" method="POST" class="ms-3">
                      <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                      <input type="hidden" name="fecha_retorno" value="<?= $fechaSeleccionada ?>">
                      <button type="submit" class="btn-cancel" <?= $reservaActiva ? 'title="No se puede cancelar una reserva activa"' : '' ?>>
                        <i class="fa-regular fa-trash-can"></i> Cancelar
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="badge bg-secondary">
                      <i class="fa-solid fa-check me-1"></i> Completada
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="cancelarReservaModal" tabindex="-1" aria-labelledby="cancelarReservaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 bg-danger text-white">
        <h5 class="modal-title" id="cancelarReservaModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar cancelación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div class="mb-3">
          <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #dc3545;"></i>
        </div>
        <h5>¿Estás seguro de que quieres cancelar esta reserva?</h5>
        <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
        <div id="detalleReserva" class="alert alert-light mt-3 mb-0 text-start">
          <div id="plazaReserva" class="fw-bold"></div>
          <div id="franjaReserva"></div>
          <div id="fechaReserva"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark me-1"></i> No, mantener reserva
        </button>
        <button type="button" class="btn btn-danger" id="confirmarCancelacion">
          <i class="fa-solid fa-trash-can me-1"></i> Sí, cancelar reserva
        </button>
      </div>
    </div>
  </div>
</div>

<?php
$additional_js = ['calendario.js'];
include 'components/footer.php';
?>
