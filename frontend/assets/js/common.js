document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.querySelector('.user-profile');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (userProfile && profileDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.preventDefault();
            profileDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target) && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    const logoutBtn = document.querySelector('.logout-btn');
    const logoutConfirm = document.querySelector('.logout-confirm');
    const cancelLogout = document.querySelector('.btn-cancel-logout');
    const logoutDialog = document.querySelector('.logout-dialog');
    
    if (logoutBtn && logoutConfirm) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.style.overflow = 'hidden'; 
            logoutConfirm.classList.add('show');
            
            setTimeout(() => {
                if (logoutDialog) {
                    logoutDialog.style.opacity = '1';
                    logoutDialog.style.transform = 'translateY(0)';
                }
            }, 50);
        });
        
        if (cancelLogout) {
            cancelLogout.addEventListener('click', function() {
                if (logoutDialog) {
                    logoutDialog.style.opacity = '0';
                    logoutDialog.style.transform = 'translateY(-20px)';
                    
                    logoutConfirm.style.opacity = '0';
                    
                    setTimeout(() => {
                        logoutConfirm.classList.remove('show');
                        document.body.style.overflow = ''; 
                        logoutConfirm.style.opacity = '1';
                    }, 300);
                } else {
                    logoutConfirm.classList.remove('show');
                    document.body.style.overflow = ''; 
                }
            });
        }
        
        logoutConfirm.addEventListener('click', function(e) {
            if (e.target === logoutConfirm) {
                cancelLogout.click(); 
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && logoutConfirm.classList.contains('show')) {
                cancelLogout.click();
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alerta => {
        setTimeout(() => {
            if (alerta && alerta.parentNode) {
                alerta.style.opacity = '0';
                alerta.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        const closeEvent = new bootstrap.Alert(alerta);
                        closeEvent.close();
                    } else {
                        alerta.remove();
                    }
                }, 300);
            }
        }, 5000);
    });
});


/**
 * Función para mostrar notificaciones temporales
 * @param {string} mensaje - El mensaje a mostrar
 * @param {string} tipo - Tipo de notificación ('success', 'error', 'warning', 'info')
 * @param {number} duracion - Duración en milisegundos (por defecto 3000)
 */
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${tipo}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fa-solid ${getIconoTipo(tipo)}"></i>
            <span>${mensaje}</span>
            <button class="notification-close"><i class="fa-solid fa-times"></i></button>
        </div>
    `;
    
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }
            .notification.show {
                transform: translateX(0);
            }
            .notification-content {
                padding: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .notification-success { background-color: #d4edda; color: #155724; border-left: 4px solid #28a745; }
            .notification-error { background-color: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
            .notification-warning { background-color: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
            .notification-info { background-color: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
            .notification-close {
                background: none;
                border: none;
                margin-left: auto;
                cursor: pointer;
                opacity: 0.7;
            }
            .notification-close:hover { opacity: 1; }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, duracion);
    
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    });
}

/**
 * Obtiene el icono correspondiente al tipo de notificación
 * @param {string} tipo - Tipo de notificación
 * @returns {string} Clase del icono
 */
function getIconoTipo(tipo) {
    const iconos = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return iconos[tipo] || iconos.info;
}

/**
 * Función para confirmar acciones importantes
 * @param {string} mensaje - Mensaje de confirmación
 * @param {Function} callback - Función a ejecutar si se confirma
 */
function confirmarAccion(mensaje, callback) {
    callback();
}

/**
 * Función para formatear fechas
 * @param {string|Date} fecha - Fecha a formatear
 * @returns {string} Fecha formateada
 */
function formatearFecha(fecha) {
    const fechaObj = new Date(fecha);
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return fechaObj.toLocaleDateString('es-ES', opciones);
}
