<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.html');
    exit;
}
$nombreUsuario = $_SESSION['usuario']['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta char// Contenido principal -->
<main class="main">
  <!-- Header con acciones -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-plus me-2"></i> Reservar plaza</h2>
  </div>

  <div class="content-card animate__animated animate__fadeIn">
    <form action="../backend/controllers/guardar-reserva.php" method="POST" class="card-form">8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reservar Plaza</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="css/estilo-unificado.css" />
  <style>
    .flatpickr-calendar {
      box-shadow: 0 15px 35px rgba(0,0,0,0.12);
      border-radius: 15px;
      padding: 15px;
      animation: fadeIn 0.3s ease-out;
      border: none;
      font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    .flatpickr-day {
      border-radius: 8px;
      transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
      margin: 2px;
      height: 40px;
      line-height: 40px;
    }
    .flatpickr-day:hover {
      background-color: #e3f2fd;
      transform: scale(1.1);
    }
    .flatpickr-day.selected {
      background-color: #2ca0f7;
      box-shadow: 0 3px 8px rgba(44, 160, 247, 0.5);
      color: white;
      font-weight: 600;
      position: relative;
    }
    .flatpickr-day.selected:hover {
      background-color: #0b88e8;
    }
    .flatpickr-day.selected:before {
      content: "";
      position: absolute;
      top: -4px;
      left: 50%;
      transform: translateX(-50%);
      width: 4px;
      height: 4px;
      background-color: #fff;
      border-radius: 50%;
      opacity: 0.7;
    }
    .flatpickr-day.today {
      border: 2px solid #2ca0f7;
      font-weight: bold;
      color: #2ca0f7;
    }
    .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
      color: rgba(64, 72, 82, 0.3);
    }
    .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
      cursor: not-allowed;
      background-color: rgba(247, 72, 78, 0.1);
      color: rgba(247, 72, 78, 0.6);
      text-decoration: line-through;
    }
    .flatpickr-months {
      padding-bottom: 15px;
      background-color: #f7f9fc;
      border-radius: 10px 10px 0 0;
      margin: -15px -15px 10px -15px;
      padding: 15px 15px 5px 15px;
    }
    .flatpickr-current-month {
      font-weight: 600;
      font-size: 1.2rem;
      padding: 10px 0;
      color: #333;
    }
    .flatpickr-weekday {
      color: #2ca0f7;
      font-weight: 600;
      font-size: 0.9rem;
    }
    .flatpickr-monthSelect-month:hover {
      background: #e3f2fd;
    }
    .flatpickr-monthSelect-month.selected {
      background: #2ca0f7;
    }
    .input-group-text {
      background-color: #e3f2fd;
      color: #2ca0f7;
      border-color: #d1edff;
      transition: all 0.3s ease;
    }
    .input-group:hover .input-group-text {
      background-color: #d1edff;
      color: #0b88e8;
    }
    .form-control:focus + .input-group-text {
      border-color: #2ca0f7;
      box-shadow: 0 0 0 0.25rem rgba(44, 160, 247, 0.25);
    }
    .form-control[readonly] {
      background-color: #fff;
      cursor: pointer;
    }
    
    .calendar-input-group {
      transition: all 0.3s ease;
    }
    
    .focused-input {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(44, 160, 247, 0.15);
    }
    
    .btn-today {
      background-color: transparent;
      border: 1px solid #2ca0f7;
      color: #2ca0f7;
      border-radius: 8px;
      padding: 4px 12px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-today:hover {
      background-color: #2ca0f7;
      color: white;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div>
    <a href="dashboard.php" class="logo">TFG</a>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
          <i class="fa-solid fa-house"></i> Tu día
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="reservas.php">
          <i class="fa-regular fa-calendar"></i> Reservas
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="reservar.php">
          <i class="fa-solid fa-plus"></i> Reservar
        </a>
      </li>
    </ul>
  </div>
  
  <div>
    <hr class="my-3" style="border-color: #444;" />
    
    <div class="user-profile d-flex align-items-center">
      <div class="user-avatar"><?= strtoupper(substr($nombreUsuario, 0, 2)) ?></div>
      <div class="ms-3">
        <div class="small text-white"><?= htmlspecialchars($nombreUsuario) ?></div>
      </div>
      
      <!-- Dropdown del perfil -->
      <div class="profile-dropdown">
        <div class="profile-info">
          <h4><?= htmlspecialchars($nombreUsuario) ?></h4>
          <p><?= htmlspecialchars($_SESSION['usuario']['correo'] ?? 'correo@ejemplo.com') ?></p>
        </div>
        <hr style="border-color: #444;" />
        <a href="#" class="small logout logout-btn">
          <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmación de cierre de sesión -->
<div class="logout-confirm">
  <div class="logout-dialog">
    <h4>¿Estás seguro de que quieres cerrar sesión?</h4>
    <p>Tendrás que volver a iniciar sesión para acceder a tu cuenta.</p>
    <div class="logout-actions">
      <button class="btn-cancel-logout"><i class="fa-solid fa-xmark"></i> Cancelar</button>
      <a href="../backend/controllers/logout.php" class="btn-confirm-logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
  </div>
</div>

<!-- Contenido principal -->
<main class="main">
  <!-- Header con acciones -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reservar plaza</h2>
  </div>

  <div class="content-card">
    <form action="../backend/controllers/guardar-reserva.php" method="POST" class="card-form">
      <div class="mb-4">
        <label for="fecha" class="form-label">Fecha de reserva</label>
        <div class="input-group calendar-input-group">
          <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
          <input type="text" class="form-control" name="fecha" id="fecha" placeholder="Selecciona una fecha" required readonly />
        </div>
        <div class="d-flex align-items-center mt-2">
          <div class="help-text">
            <i class="fa-solid fa-circle-info"></i> Selecciona una fecha para tu reserva
          </div>
          <button type="button" class="btn-today ms-auto btn-sm" id="goToToday">
            <i class="fa-solid fa-calendar-day"></i> Hoy
          </button>
        </div>
      </div>

      <div class="mb-4">
        <label for="franja" class="form-label">Franja horaria</label>
        <select class="form-select" name="franja" id="franja" required>
          <option value="">Selecciona una franja</option>
          <option value="mañana">Mañana</option>
          <option value="tarde">Tarde</option>
          <option value="día completo">Día completo</option>
        </select>
        <div class="help-text">
          <i class="fa-solid fa-circle-info"></i> Indica si necesitas la plaza por la mañana, tarde o día completo
        </div>
      </div>

      <div class="mb-4">
        <label for="plaza" class="form-label">Plaza disponible</label>
        <select class="form-select" name="plaza_id" id="plaza" required>
          <option value="">Selecciona una plaza</option>
        </select>
        <div class="help-text">
          <i class="fa-solid fa-circle-info"></i> Elige entre las plazas disponibles para esa fecha y franja
        </div>
      </div>

      <button type="submit" class="btn-reservar">
        <i class="fa-solid fa-check"></i> Confirmar Reserva
      </button>
  </form>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Obtener la fecha actual
  const hoy = new Date();
  const unMesDespues = new Date();
  unMesDespues.setMonth(unMesDespues.getMonth() + 2);
  
  // Función para animar el calendario cuando se abre
  function animateCalendarOpen(cal) {
    const calendar = cal.calendarContainer;
    calendar.classList.add('animate__animated', 'animate__fadeInDown', 'animate__faster');
    
    calendar.addEventListener('animationend', function() {
      calendar.classList.remove('animate__animated', 'animate__fadeInDown', 'animate__faster');
    }, { once: true });
  }
  
  // Obtener las fechas indisponibles (ejemplo - aquí se conectaría con el backend)
  async function obtenerFechasOcupadas() {
    try {
      // Esta parte es solo un ejemplo - en una implementación real habría que conectar con el backend
      return []; // Aquí se devolverían fechas ocupadas desde el backend
    } catch (err) {
      console.error("Error al obtener fechas ocupadas:", err);
      return [];
    }
  }
  
  // Función para personalizar los tooltips
  function customDayHover(day, cell) {
    const date = day.dateObj;
    if (date < hoy) {
      cell.title = "Fecha pasada";
    }
  }
  
  // Configuración del calendario
  const fpInstance = flatpickr("#fecha", {
    locale: "es",
    dateFormat: "Y-m-d",
    minDate: "today",
    maxDate: unMesDespues,
    disableMobile: true,
    theme: "material_blue",
    showMonths: 1,
    animate: true,
    enableTime: false,
    altInput: true,
    altFormat: "d F Y",
    monthSelectorType: "static",
    position: "auto",
    nextArrow: '<i class="fa fa-chevron-right"></i>',
    prevArrow: '<i class="fa fa-chevron-left"></i>',
    onReady: function(selectedDates, dateStr, instance) {
      // Añadir animación al abrir
      instance.calendarContainer.classList.add('flatpickr-calendar-animated');
    },
    onOpen: function(selectedDates, dateStr, instance) {
      animateCalendarOpen(instance);
    },
    onDayCreate: function(dObj, dStr, fp, dayElem) {
      // Personalizar los tooltips
      const date = dayElem.dateObj;
      if (date < hoy) {
        dayElem.title = "Fecha pasada";
      } else {
        dayElem.title = "Haz clic para seleccionar";
      }
    },
    onChange: function(selectedDates, dateStr) {
      // Mantener la misma funcionalidad que tenía el evento change original
      const event = new Event('change');
      document.getElementById('fecha').dispatchEvent(event);
    }
  });
  
  // Mejorar la experiencia de selección con un pequeño efecto
  document.getElementById('fecha').addEventListener('focus', function() {
    this.parentElement.classList.add('focused-input');
  });
  
  document.addEventListener('click', function(e) {
    if (!document.getElementById('fecha').contains(e.target) && 
        !document.querySelector('.flatpickr-calendar') || 
        (document.querySelector('.flatpickr-calendar') && !document.querySelector('.flatpickr-calendar').contains(e.target))) {
      document.getElementById('fecha').parentElement.classList.remove('focused-input');
    }
  });
  
  // Botón para ir a hoy
  document.getElementById('goToToday').addEventListener('click', function() {
    fpInstance.setDate(new Date());
    
    // Disparar manualmente el evento change para cargar las plazas disponibles
    const event = new Event('change');
    document.getElementById('fecha').dispatchEvent(event);
  });
});
</script>
<script src="js/cargarPlazas.js" type="module"></script>
<script src="js/user-profile.js"></script>
<script src="js/reserva-preview.js"></script>
</body>
</html>
