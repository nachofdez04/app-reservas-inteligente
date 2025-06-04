/**
 * reserva-preview.js - Muestra un resumen de la reserva antes de confirmarla
 */
document.addEventListener("DOMContentLoaded", () => {
  const formulario = document.querySelector('.card-form');
  const fechaInput = document.getElementById('fecha');
  const franjaSelect = document.getElementById('franja');
  const plazaSelect = document.getElementById('plaza');
  
  // Prevenir el envío directo del formulario para mostrar resumen
  formulario.addEventListener('submit', function(event) {
    // Solo mostrar preview si todos los campos están completos
    if (fechaInput.value && franjaSelect.value && plazaSelect.value) {
      event.preventDefault();
      mostrarResumenReserva();
    }
  });
  
  function mostrarResumenReserva() {
    // Obtener valores seleccionados
    const fecha = fechaInput.value;
    const franja = franjaSelect.options[franjaSelect.selectedIndex].text;
    const plaza = plazaSelect.options[plazaSelect.selectedIndex].text;
    
    // Formatear fecha para mostrar
    const fechaObj = new Date(fecha);
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);
    
    // Crear elementos para el modal
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'modal-overlay animate__animated animate__fadeIn';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content animate__animated animate__zoomIn';
    
    // Contenido del modal
    modalContent.innerHTML = `
      <div class="modal-header">
        <h3><i class="fa-solid fa-check-circle"></i> Confirmar reserva</h3>
        <button class="close-modal"><i class="fa-solid fa-times"></i></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Estás a punto de realizar la siguiente reserva:</p>
        
        <div class="reserva-info">
          <div class="info-item">
            <span class="icon"><i class="fa-solid fa-calendar-day"></i></span>
            <span class="label">Fecha:</span>
            <span class="value">${fechaFormateada}</span>
          </div>
          <div class="info-item">
            <span class="icon"><i class="fa-solid fa-clock"></i></span>
            <span class="label">Franja:</span>
            <span class="value">${franja}</span>
          </div>
          <div class="info-item">
            <span class="icon"><i class="fa-solid fa-car"></i></span>
            <span class="label">Plaza:</span>
            <span class="value">${plaza}</span>
          </div>
        </div>
        
        <p class="mt-3">¿Deseas confirmar esta reserva?</p>
      </div>
      <div class="modal-footer">
        <button class="btn-cancelar-reserva">Cancelar</button>
        <button class="btn-confirmar-reserva">Confirmar reserva</button>
      </div>
    `;
    
    // Añadir el modal al DOM
    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);
    
    // Eventos de los botones
    document.querySelector('.close-modal').addEventListener('click', cerrarModal);
    document.querySelector('.btn-cancelar-reserva').addEventListener('click', cerrarModal);
    document.querySelector('.btn-confirmar-reserva').addEventListener('click', () => {
      // Enviar el formulario
      formulario.submit();
    });
    
    // Evitar que se cierre al hacer click en el contenido
    modalContent.addEventListener('click', (e) => {
      e.stopPropagation();
    });
    
    // Cerrar al hacer click fuera
    modalOverlay.addEventListener('click', cerrarModal);
    
    // Añadir estilos dinámicamente
    const style = document.createElement('style');
    style.textContent = `
      .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
      }
      
      .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        overflow: hidden;
      }
      
      .modal-header {
        background: #f7f9fc;
        padding: 15px 20px;
        border-bottom: 1px solid #ebedf2;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      
      .modal-header h3 {
        margin: 0;
        font-size: 1.2rem;
        color: #333;
      }
      
      .modal-header h3 i {
        color: #2ca0f7;
        margin-right: 8px;
      }
      
      .close-modal {
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #777;
      }
      
      .modal-body {
        padding: 20px;
      }
      
      .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #ebedf2;
        text-align: right;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
      }
      
      .reserva-info {
        background-color: #f7f9fc;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
      }
      
      .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
      }
      
      .info-item:last-child {
        margin-bottom: 0;
      }
      
      .info-item .icon {
        width: 30px;
        height: 30px;
        background-color: #e3f2fd;
        color: #2ca0f7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
      }
      
      .info-item .label {
        width: 70px;
        color: #666;
        font-weight: 500;
      }
      
      .info-item .value {
        flex: 1;
        font-weight: 600;
        color: #333;
      }
      
      .btn-cancelar-reserva {
        background: transparent;
        border: 1px solid #ddd;
        color: #666;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      
      .btn-cancelar-reserva:hover {
        background-color: #f5f5f7;
        border-color: #ccc;
      }
      
      .btn-confirmar-reserva {
        background-color: #2ca0f7;
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
      }
      
      .btn-confirmar-reserva:hover {
        background-color: #0b88e8;
      }
    `;
    document.head.appendChild(style);
  }
  
  function cerrarModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
      // Animaciones de cierre
      const modalContent = modal.querySelector('.modal-content');
      modalContent.classList.remove('animate__zoomIn');
      modalContent.classList.add('animate__zoomOut');
      modal.classList.remove('animate__fadeIn');
      modal.classList.add('animate__fadeOut');
      
      // Eliminar después de la animación
      setTimeout(() => {
        modal.remove();
      }, 300);
    }
  }
});
