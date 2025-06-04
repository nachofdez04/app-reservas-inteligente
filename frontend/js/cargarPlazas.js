document.addEventListener("DOMContentLoaded", () => {
  const fechaInput = document.getElementById("fecha");
  const franjaInput = document.getElementById("franja");
  const plazaSelect = document.getElementById("plaza");
  let loadingIndicator = null;

  function mostrarCargando() {
    // Crear indicador de carga si no existe
    if (!loadingIndicator) {
      loadingIndicator = document.createElement('div');
      loadingIndicator.className = 'cargando-plazas';
      loadingIndicator.innerHTML = `
        <div class="spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        <span>Cargando plazas disponibles...</span>
      `;
      // Insertar después del select de plaza
      plazaSelect.parentNode.insertAdjacentElement('beforeend', loadingIndicator);
      
      // Estilos dinámicos
      const style = document.createElement('style');
      style.textContent = `
        .cargando-plazas {
          display: flex;
          align-items: center;
          margin-top: 10px;
          color: #2ca0f7;
          font-size: 0.9rem;
          opacity: 0;
          transform: translateY(-5px);
          transition: all 0.3s ease;
        }
        .cargando-plazas.visible {
          opacity: 1;
          transform: translateY(0);
        }
        .cargando-plazas .spinner {
          margin-right: 8px;
          animation: spin 1.2s linear infinite;
        }
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `;
      document.head.appendChild(style);
    }
    
    // Hacer visible
    setTimeout(() => {
      loadingIndicator.classList.add('visible');
    }, 10);
  }

  function ocultarCargando() {
    if (loadingIndicator) {
      loadingIndicator.classList.remove('visible');
    }
  }

  async function cargarPlazas() {
    const fecha = fechaInput.value;
    const franja = franjaInput.value;

    // Evitamos llamada si no hay ambos valores
    if (!fecha || !franja) return;

    // Mostrar indicador de carga
    mostrarCargando();
    plazaSelect.disabled = true;
    plazaSelect.innerHTML = `<option value="">Cargando plazas...</option>`;

    try {
      const res = await fetch(`../../backend/controllers/plazas-disponibles.php?fecha=${fecha}&franja=${franja}`);
      const data = await res.json();

      // Pequeña demora para mejor UX
      await new Promise(resolve => setTimeout(resolve, 500));

      if (!Array.isArray(data)) {
        plazaSelect.innerHTML = `<option value="">Error al cargar plazas</option>`;
        return;
      }

      if (data.length === 0) {
        plazaSelect.innerHTML = `<option value="">No hay plazas disponibles</option>`;
        return;
      }

      plazaSelect.innerHTML = data
        .map(p => `<option value="${p.id}">${p.nombre}</option>`)
        .join('');

    } catch (err) {
      console.error("Error al cargar plazas:", err);
      plazaSelect.innerHTML = `<option value="">Error al cargar plazas</option>`;
    } finally {
      ocultarCargando();
      plazaSelect.disabled = false;
    }
  }

  fechaInput.addEventListener("change", cargarPlazas);
  franjaInput.addEventListener("change", cargarPlazas);
});
