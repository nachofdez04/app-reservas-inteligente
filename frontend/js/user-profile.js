// Script para manejar la funcionalidad del perfil de usuario
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar el dropdown del perfil
    const userProfile = document.querySelector('.user-profile');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (userProfile && profileDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.preventDefault();
            profileDropdown.classList.toggle('show');
        });
        
        // Cerrar el dropdown cuando se hace clic fuera
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target) && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Mostrar el diálogo de confirmación de cierre de sesión
    const logoutBtn = document.querySelector('.logout-btn');
    const logoutConfirm = document.querySelector('.logout-confirm');
    const cancelLogout = document.querySelector('.btn-cancel-logout');
    const logoutDialog = document.querySelector('.logout-dialog');
    
    if (logoutBtn && logoutConfirm) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.style.overflow = 'hidden'; // Prevenir scroll
            logoutConfirm.classList.add('show');
            
            // Aplicar efecto de entrada con un ligero retraso para mejor visualización
            setTimeout(() => {
                if (logoutDialog) {
                    logoutDialog.style.opacity = '1';
                    logoutDialog.style.transform = 'translateY(0)';
                }
            }, 50);
        });
        
        // Cancelar el cierre de sesión
        if (cancelLogout) {
            cancelLogout.addEventListener('click', function() {                // Efecto de salida
                if (logoutDialog) {
                    logoutDialog.style.opacity = '0';
                    logoutDialog.style.transform = 'translateY(-20px)';
                    
                    // Aplicar fade-out al fondo también
                    logoutConfirm.style.opacity = '0';
                    
                    // Pequeño retraso antes de ocultar el modal
                    setTimeout(() => {
                        logoutConfirm.classList.remove('show');
                        document.body.style.overflow = ''; // Restaurar scroll
                        // Restaurar la opacidad para la próxima vez
                        logoutConfirm.style.opacity = '1';
                    }, 300);
                } else {
                    logoutConfirm.classList.remove('show');
                    document.body.style.overflow = ''; // Restaurar scroll
                }
            });
        }
        
        // Cerrar el diálogo haciendo clic en el fondo
        logoutConfirm.addEventListener('click', function(e) {
            if (e.target === logoutConfirm) {
                cancelLogout.click(); // Usar el mismo efecto que el botón cancelar
            }
        });
        
        // Cerrar el diálogo con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && logoutConfirm.classList.contains('show')) {
                cancelLogout.click();
            }
        });
    }
});
