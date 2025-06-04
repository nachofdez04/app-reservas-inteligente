<?php
session_start();

$page_title = "Iniciar Sesión | ParkingTFG";
$page_type = "auth"; 
$additional_css = []; 
$additional_js = []; 

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="auth-body">
    <main class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
        <div class="login-card">
            <a href="#" class="logo">
                <i class="fa-solid fa-car"></i> ParkingTFG
            </a>
            <h3 class="text-center mb-4">Iniciar Sesión</h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php 
                    switch($_GET['error']) {
                        case 'credenciales':
                            echo 'Correo o contraseña incorrectos';
                            break;
                        case 'sesion':
                            echo 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente';
                            break;
                        default:
                            echo 'Error al iniciar sesión. Inténtalo de nuevo';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Registro exitoso. Ya puedes iniciar sesión.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout']) && $_GET['logout'] === 'ok'): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Has cerrado sesión correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="../backend/controllers/login-controller.php" method="POST" class="needs-validation" novalidate>
                <div class="form-group mb-3">
                    <label for="correo" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="correo" 
                        name="correo" 
                        placeholder="tu@correo.com" 
                        value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                        required 
                    />
                    <div class="invalid-feedback">
                        Por favor, introduce un correo válido.
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="clave" class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        Contraseña
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="clave" 
                            name="clave" 
                            placeholder="Tu contraseña" 
                            required 
                        />
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        Por favor, introduce tu contraseña.
                    </div>
                </div>

                <button type="submit" class="login-btn w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar sesión
                </button>

                <div class="text-center">
                    <a href="register.php" class="register-link">
                        <i class="fas fa-user-plus me-1"></i>
                        ¿No tienes cuenta? Regístrate
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('clave');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
            
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        });
    </script>
</body>
</html>
