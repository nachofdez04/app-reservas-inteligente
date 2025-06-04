document.addEventListener("DOMContentLoaded", () => {
    const fechaInput = document.getElementById("fecha");
    const franjaInput = document.getElementById("franja");
    const plazaSelect = document.getElementById("plaza");
    let loadingIndicator = null;

    function mostrarCargando() {
        if (!loadingIndicator) {
            loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'cargando-plazas';
            loadingIndicator.innerHTML = `
                <div class="spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
                <span>Cargando plazas disponibles...</span>
            `;
            plazaSelect.parentNode.insertAdjacentElement('beforeend', loadingIndicator);
            
            if (!document.querySelector('#plaza-loading-styles')) {
                const style = document.createElement('style');
                style.id = 'plaza-loading-styles';
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
        }
        
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

        if (!fecha || !franja) return;

        mostrarCargando();
        plazaSelect.disabled = true;
        plazaSelect.innerHTML = `<option value="">Cargando plazas...</option>`;

        try {
            const res = await fetch(`../../backend/controllers/plazas-disponibles.php?fecha=${fecha}&franja=${franja}`);
            const data = await res.json();

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
            plazaSelect.innerHTML = `<option value="">Error al cargar plazas</option>`;
        } finally {
            ocultarCargando();
            plazaSelect.disabled = false;
        }
    }

    if (fechaInput && franjaInput && plazaSelect) {
        fechaInput.addEventListener("change", cargarPlazas);
        franjaInput.addEventListener("change", cargarPlazas);
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.querySelector('.card-form');
    const fechaInput = document.getElementById('fecha');
    const franjaSelect = document.getElementById('franja');
    const plazaSelect = document.getElementById('plaza');
    
    if (!formulario || !fechaInput || !franjaSelect || !plazaSelect) return;
    
    formulario.addEventListener('submit', function(event) {
        if (fechaInput.value && franjaSelect.value && plazaSelect.value) {
            event.preventDefault();
            mostrarResumenReserva();
        }
    });
    
    function mostrarResumenReserva() {
        const fecha = fechaInput.value;
        const franja = franjaSelect.options[franjaSelect.selectedIndex].text;
        const plaza = plazaSelect.options[plazaSelect.selectedIndex].text;
        
        const fechaObj = new Date(fecha);
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);
        
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'modal-overlay animate__animated animate__fadeIn';
        
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content animate__animated animate__zoomIn';
        
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
        
        modalOverlay.appendChild(modalContent);
        document.body.appendChild(modalOverlay);
        
        document.querySelector('.close-modal').addEventListener('click', cerrarModal);
        document.querySelector('.btn-cancelar-reserva').addEventListener('click', cerrarModal);
        document.querySelector('.btn-confirmar-reserva').addEventListener('click', () => {
            formulario.submit();
        });
        
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        modalOverlay.addEventListener('click', cerrarModal);
        
        if (!document.querySelector('#reserva-preview-styles')) {
            const style = document.createElement('style');
            style.id = 'reserva-preview-styles';
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
    }
    
    function cerrarModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            const modalContent = modal.querySelector('.modal-content');
            modalContent.classList.remove('animate__zoomIn');
            modalContent.classList.add('animate__zoomOut');
            modal.classList.remove('animate__fadeIn');
            modal.classList.add('animate__fadeOut');
            
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const formularioReserva = document.querySelector('.card-form');
    
    if (!formularioReserva) return;
    
    const inputs = formularioReserva.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', validarCampo);
        input.addEventListener('change', validarCampo);
    });
    
    function validarCampo(event) {
        const campo = event.target;
        const valor = campo.value.trim();
        
        const errorPrevio = campo.parentNode.querySelector('.error-message');
        if (errorPrevio) {
            errorPrevio.remove();
        }
        
        campo.classList.remove('is-invalid', 'is-valid');
        
        let esValido = true;
        let mensajeError = '';
        
        if (campo.hasAttribute('required') && !valor) {
            esValido = false;
            mensajeError = 'Este campo es obligatorio';        
        } else if (campo.type === 'date' && valor) {
            const fechaSeleccionada = new Date(valor);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
              if (fechaSeleccionada < hoy) {
                esValido = false;
                mensajeError = 'La fecha no puede ser anterior a hoy';
            } else {
                const diaSemana = fechaSeleccionada.getDay();
                if (diaSemana === 0 || diaSemana === 6) {
                    esValido = false;
                    mensajeError = 'No se puede reservar plaza los fines de semana';
                } else {
                    const fechaLimite = new Date();
                    fechaLimite.setDate(fechaLimite.getDate() + 7);
                    fechaLimite.setHours(0, 0, 0, 0);
                    
                    if (fechaSeleccionada > fechaLimite) {
                        esValido = false;
                        mensajeError = 'No se puede reservar con más de una semana de antelación';
                    }
                }
            }
        }
        
        if (esValido) {
            campo.classList.add('is-valid');
        } else {
            campo.classList.add('is-invalid');
            mostrarMensajeError(campo, mensajeError);
        }
        
        return esValido;
    }
    
    function mostrarMensajeError(campo, mensaje) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger mt-1';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.textContent = mensaje;
        campo.parentNode.appendChild(errorDiv);
    }
    
    formularioReserva.addEventListener('submit', function(event) {
        let formularioValido = true;
        
        inputs.forEach(input => {
            if (!validarCampo({ target: input })) {
                formularioValido = false;
            }
        });
        
        if (!formularioValido) {
            event.preventDefault();
            mostrarNotificacion('Por favor, corrige los errores en el formulario', 'error');
        }
    });
});


/**
 * Obtiene las plazas disponibles para una fecha y franja específica
 * @param {string} fecha - Fecha en formato YYYY-MM-DD
 * @param {string} franja - ID de la franja horaria
 * @returns {Promise} Promesa con las plazas disponibles
 */
async function obtenerPlazasDisponibles(fecha, franja) {
    try {
        const response = await fetch(`../../backend/controllers/plazas-disponibles.php?fecha=${fecha}&franja=${franja}`);
        return await response.json();
    } catch (error) {
        return [];
    }
}

/**
 * Formatea la información de una reserva para mostrar
 * @param {Object} reserva - Objeto con los datos de la reserva
 * @returns {string} HTML formateado
 */
function formatearInfoReserva(reserva) {
    return `
        <div class="reserva-resumen">
            <p><strong>Fecha:</strong> ${formatearFecha(reserva.fecha)}</p>
            <p><strong>Franja:</strong> ${reserva.franja}</p>
            <p><strong>Plaza:</strong> ${reserva.plaza}</p>
        </div>
    `;
}
