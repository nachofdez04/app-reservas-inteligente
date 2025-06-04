document.addEventListener("DOMContentLoaded", function() {
    
    const diasCalendario = document.querySelectorAll('.calendar-day');
    diasCalendario.forEach(dia => {
        const celda = dia.parentElement;
        celda.style.position = 'relative';

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


    const reservasItems = document.querySelectorAll('.reserva-item');
    const reservasLista = document.querySelector('.reservas-lista');
    const emptyReserva = document.querySelector('.empty-reserva');
    
    if (reservasItems.length > 0) {
        if (reservasLista) {
            reservasLista.style.opacity = '0';
            setTimeout(() => {
                reservasLista.style.transition = 'opacity 0.3s ease';
                reservasLista.style.opacity = '1';
            }, 100);
        }
        
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
        emptyReserva.style.opacity = '0';
        emptyReserva.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            emptyReserva.style.transition = 'all 0.5s ease';
            emptyReserva.style.opacity = '1';
            emptyReserva.style.transform = 'scale(1)';
        }, 150);
    }

    
    const cancelarReservaModal = document.getElementById('cancelarReservaModal');
    const plazaReserva = document.getElementById('plazaReserva');
    const franjaReserva = document.getElementById('franjaReserva');
    const fechaReserva = document.getElementById('fechaReserva');
    const confirmarCancelacion = document.getElementById('confirmarCancelacion');
    
    let formularioActual = null;
    
    if (cancelarReservaModal) {
        const modal = new bootstrap.Modal(cancelarReservaModal);
        
        const formsCancelar = document.querySelectorAll('form[action*="cancelar-reserva.php"]');
        formsCancelar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                formularioActual = this;
                
                const reservaItem = this.closest('.reserva-item');
                
                const plazaElement = reservaItem.querySelector('h5');
                const franjaElement = reservaItem.querySelector('p');
                const fechaElement = document.querySelector('.fecha-header h4');
                
                const plazaTexto = plazaElement ? plazaElement.textContent.trim() : 'Plaza no especificada';
                const franjaTexto = franjaElement ? franjaElement.textContent.trim().replace(/^\s*\S+\s*/, '') : '';
                const fechaTexto = fechaElement ? fechaElement.textContent.trim() : '';
                
                if (plazaReserva) plazaReserva.textContent = plazaTexto;
                if (franjaReserva) franjaReserva.innerHTML = '<i class="fa-regular fa-clock me-1"></i> Horario: ' + franjaTexto;
                if (fechaReserva) fechaReserva.innerHTML = '<i class="fa-regular fa-calendar me-1"></i> ' + fechaTexto;
                
                cancelarReservaModal.classList.add('fade');
                
                modal.show();
            });
        });
        
        if (confirmarCancelacion) {
            confirmarCancelacion.addEventListener('click', function() {
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Cancelando...';
                this.disabled = true;
                
                setTimeout(() => {
                    if (formularioActual) {
                        formularioActual.submit();
                    }
                    modal.hide();
                }, 500);
            });
        }
    }


    function resaltarDiaActual() {
        const hoy = new Date();
        const diaActual = hoy.getDate();
        const mesActual = hoy.getMonth();
        const añoActual = hoy.getFullYear();
        
        const diasCalendario = document.querySelectorAll('.calendar-day');
        diasCalendario.forEach(dia => {
            const fecha = dia.getAttribute('data-fecha');
            if (fecha) {
                const fechaDia = new Date(fecha);
                if (fechaDia.getDate() === diaActual && 
                    fechaDia.getMonth() === mesActual && 
                    fechaDia.getFullYear() === añoActual) {
                    dia.classList.add('today');
                }
            }
        });
    }
    

    function añadirIndicadoresReservas() {
        const diasConReservas = document.querySelectorAll('.calendar-day[data-tiene-reservas="true"]');
        diasConReservas.forEach(dia => {
            if (!dia.querySelector('.dot-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'dot-indicator';
                dia.appendChild(indicator);
            }
        });
    }    
    function inicializarNavegacionCalendario() {
        const prevBtn = document.querySelector('.calendar-header .btn-navigation[href*="mes="]');
        const nextBtn = document.querySelector('.calendar-header .btn-navigation[href*="mes="]:last-of-type');
        
        const botonesNavegacion = document.querySelectorAll('.btn-navigation');
        botonesNavegacion.forEach(boton => {
            boton.addEventListener('click', function() {
                const calendario = document.querySelector('.calendar-table');
                if (calendario) {
                    calendario.style.transition = 'opacity 0.2s ease';
                    calendario.style.opacity = '0.6';
                }
                
                this.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    if (this) {
                        this.style.transform = '';
                    }
                }, 150);
            });
        });
    }
    

    function aplicarEfectoTransicion() {
        const calendarioTable = document.querySelector('.calendar-table');
        if (calendarioTable) {
            calendarioTable.style.transition = 'opacity 0.3s ease';
            calendarioTable.style.opacity = '0.5';
        }
    }
    
    function mejorarEfectosVisuales() {
        const diasCalendario = document.querySelectorAll('.calendar-day');
          diasCalendario.forEach(dia => {
            if (dia.classList.contains('weekend')) {
                dia.setAttribute('title', 'No se puede reservar los fines de semana');
            }
            
            if (dia.classList.contains('past')) {
                const currentTitle = dia.getAttribute('title') || '';
                dia.setAttribute('title', currentTitle + (currentTitle ? ' - ' : '') + 'Día pasado');
            }
            
            if (dia.classList.contains('today')) {
                dia.setAttribute('title', 'Hoy');
            }
            
            dia.addEventListener('mouseenter', function() {
                if (this.classList.contains('weekend') || this.classList.contains('past')) {
                    this.style.transition = 'all 0.2s ease';
                }
            });
        });
    }
    resaltarDiaActual();
    añadirIndicadoresReservas();
    inicializarNavegacionCalendario();
    mejorarEfectosVisuales();
    
    function confirmarAccionesDestructivas() {
        const botonesEliminar = document.querySelectorAll('.btn-cancel, .btn-danger');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
            });
        });
    }
    

    function mejorarAccesibilidad() {
        const diasCalendario = document.querySelectorAll('.calendar-day');
        diasCalendario.forEach((dia, index) => {
            dia.setAttribute('tabindex', '0');
            dia.setAttribute('role', 'button');
            dia.setAttribute('aria-label', `Seleccionar día ${dia.textContent}`);
            
            dia.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    dia.click();
                }
                
                if (e.key === 'ArrowRight' && diasCalendario[index + 1]) {
                    diasCalendario[index + 1].focus();
                } else if (e.key === 'ArrowLeft' && diasCalendario[index - 1]) {
                    diasCalendario[index - 1].focus();
                }
            });
        });
    }
    
    confirmarAccionesDestructivas();
    mejorarAccesibilidad();
});
