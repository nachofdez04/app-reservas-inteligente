    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="assets/js/common.js"></script>
    
    <?php if (isset($page_type)): ?>
        <?php if ($page_type === 'calendar' || $page_type === 'dashboard' || $page_type === 'reservas'): ?>
            <script src="assets/js/calendario.js"></script>
        <?php endif; ?>
        
        <?php if ($page_type === 'reservar' || $page_type === 'nueva-reserva'): ?>
            <script src="assets/js/reservas.js"></script>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?php echo $inline_scripts; ?>
        </script>
    <?php endif; ?>
    
    <script>        
        const AppConfig = {
            baseUrl: '<?php echo isset($_SERVER["HTTP_HOST"]) ? "//" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) : ""; ?>',
            userId: <?php echo isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : 'null'; ?>,
            userName: '<?php echo isset($_SESSION['usuario']['nombre']) ? addslashes($_SESSION['usuario']['nombre']) : ''; ?>',
            currentPage: '<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>',
            version: '1.0.0'
        };
        
        window.Utils = {
            formatDate: function(date) {
                return new Date(date).toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },
            
            formatTime: function(time) {
                return new Date('1970-01-01T' + time + 'Z').toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
            
            showMessage: function(message, type = 'info') {
                if (typeof mostrarNotificacion === 'function') {
                    mostrarNotificacion(message, type);
                } 
            }
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('page-' + AppConfig.currentPage);
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            const formsCriticos = document.querySelectorAll('form[data-confirm]');            
            formsCriticos.forEach(form => {
                form.addEventListener('submit', function(e) {
                });
            });
            
            const enlacesImportantes = document.querySelectorAll('a[href*="reservar"], a[href*="guardar"], a[href*="cancelar"]');
            enlacesImportantes.forEach(enlace => {
                enlace.addEventListener('click', function() {
                    if (!this.hasAttribute('data-no-loading')) {
                        const icono = this.querySelector('i');
                        if (icono && !icono.classList.contains('fa-spin')) {
                            icono.className = 'fa-solid fa-circle-notch fa-spin';
                        }
                    }
                });
            });
        });
        <?php if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false)): ?>
        console.log('🚗 Sistema de Reservas de Parking - Modo Debug Activo');
        console.log('Configuración:', AppConfig);
        console.log('Página actual:', AppConfig.currentPage);
        console.log('Usuario:', AppConfig.userName);
        <?php endif; ?>
    </script>

</body>
</html>
