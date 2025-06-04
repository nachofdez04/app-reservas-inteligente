/**
 * Funciones para mejorar la interactividad del calendario
 * y el panel de reservas
 */
document.addEventListener("DOMContentLoaded", function() {
    // Cerrar alertas automáticamente después de 5 segundos
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alerta => {
        setTimeout(() => {
            // Crear un nuevo evento de bootstrap para cerrar la alerta
            const closeEvent = new bootstrap.Alert(alerta);
            closeEvent.close();
        }, 5000);
    });

    // Añadir efecto hover a las celdas del calendario que tienen días
    const diasCalendario = document.querySelectorAll('.calendar-day');
    diasCalendario.forEach(dia => {
        const celda = dia.parentElement; // La celda TD que contiene el enlace
        celda.style.position = 'relative';

        // Crear un efecto de hover
        celda.addEventListener('mouseenter', function() {
            if (dia.classList.contains('selected')) return;
            dia.style.backgroundColor = '#e3f2fd';
            dia.style.transform = 'scale(1.1)';
        });

        celda.addEventListener('mouseleave', function() {
            if (dia.classList.contains('selected')) return;
            dia.style.backgroundColor = '';
            dia.style.transform = '';
        });
    });

    // Efecto de transición suave al cargar las reservas
    const reservasItems = document.querySelectorAll('.reserva-item');
    const reservasLista = document.querySelector('.reservas-lista');
    const emptyReserva = document.querySelector('.empty-reserva');
    
    // Si hay elementos en la lista, aplicar animación
    if (reservasItems.length > 0) {
        // Añadir animación a la lista completa primero
        if (reservasLista) {
            reservasLista.style.opacity = '0';
            setTimeout(() => {
                reservasLista.style.transition = 'opacity 0.3s ease';
                reservasLista.style.opacity = '1';
            }, 100);
        }
        
        // Luego animar cada elemento individualmente
        reservasItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(15px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.4s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 200 + (100 * index));
        });
    } else if (emptyReserva) {
        // Animación para el mensaje de vacío
        emptyReserva.style.opacity = '0';
        emptyReserva.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            emptyReserva.style.transition = 'all 0.5s ease';
            emptyReserva.style.opacity = '1';
            emptyReserva.style.transform = 'scale(1)';
        }, 150);
    }

    // Modal para confirmación de cancelación de reserva
    const cancelarReservaModal = document.getElementById('cancelarReservaModal');
    const plazaReserva = document.getElementById('plazaReserva');
    const franjaReserva = document.getElementById('franjaReserva');
    const fechaReserva = document.getElementById('fechaReserva');
    const confirmarCancelacion = document.getElementById('confirmarCancelacion');
    
    // Variable para guardar el formulario actual
    let formularioActual = null;
    
    if (cancelarReservaModal) {
        const modal = new bootstrap.Modal(cancelarReservaModal);
        
        // Añadir confirmación antes de cancelar una reserva
        const formsCancelar = document.querySelectorAll('form[action*="cancelar-reserva.php"]');
        formsCancelar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Guardar referencia al formulario actual
                formularioActual = this;
                
                // Obtener información de la reserva para el mensaje
                const reservaItem = this.closest('.reserva-item');
                
                // Obtener detalles de la reserva
                const plazaElement = reservaItem.querySelector('h5');
                const franjaElement = reservaItem.querySelector('p');
                const fechaElement = document.querySelector('.fecha-header h4');
                
                // Limpiar el texto para eliminar los iconos
                const plazaTexto = plazaElement ? plazaElement.textContent.trim() : 'Plaza no especificada';
                const franjaTexto = franjaElement ? franjaElement.textContent.trim().replace(/^\s*\S+\s*/, '') : '';
                const fechaTexto = fechaElement ? fechaElement.textContent.trim() : '';
                
                // Establecer contenido en el modal
                plazaReserva.textContent = plazaTexto;
                franjaReserva.innerHTML = '<i class="fa-regular fa-clock me-1"></i> Horario: ' + franjaTexto;
                fechaReserva.innerHTML = '<i class="fa-regular fa-calendar me-1"></i> ' + fechaTexto;
                
                // Añadir animación de entrada
                cancelarReservaModal.classList.add('fade');
                
                // Mostrar el modal
                modal.show();
            });
        });
        
        // Manejar la confirmación de cancelación
        confirmarCancelacion.addEventListener('click', function() {
            // Mostrar feedback visual al usuario
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Cancelando...';
            this.disabled = true;
            
            // Pequeño retraso para mostrar el feedback
            setTimeout(() => {
                if (formularioActual) {
                    formularioActual.submit();
                }
                modal.hide();
            }, 500);
        });
    }
});
