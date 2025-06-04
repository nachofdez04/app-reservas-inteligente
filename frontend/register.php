<?php
session_start();

$page_title = "Registro | ParkingTFG";
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
        <div class="register-card">
            <a href="#" class="logo">
                <i class="fa-solid fa-car"></i> ParkingTFG
            </a>
            <h3 class="text-center mb-4">Crear cuenta</h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php 
                    switch($_GET['error']) {
                        case 'correo_existe':
                            echo 'Ese correo ya está registrado';
                            break;
                        case 'validacion':
                            echo 'Por favor, completa todos los campos correctamente';
                            break;
                        case 'servidor':
                            echo 'Error del servidor. Inténtalo de nuevo más tarde';
                            break;
                        default:
                            echo 'Error al crear la cuenta. Inténtalo de nuevo';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="../backend/controllers/register-controller.php" method="POST" class="needs-validation" novalidate>
                <div class="form-group mb-3">
                    <label for="nombre" class="form-label">
                        <i class="fas fa-user me-1"></i>
                        Nombre completo
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Tu nombre completo" 
                        value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                        pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{2,50}"
                        required 
                    />
                    <div class="invalid-feedback">
                        El nombre debe tener entre 2 y 50 caracteres, solo letras y espacios.
                    </div>
                </div>

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

                <div class="form-group mb-3">
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
                            placeholder="Contraseña segura" 
                            minlength="6"
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$"
                            required 
                        />
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <small>La contraseña debe tener al menos 6 caracteres, incluyendo mayúscula, minúscula y número.</small>
                    </div>
                    <div class="invalid-feedback">
                        La contraseña debe cumplir los requisitos de seguridad.
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="confirmar_clave" class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        Confirmar contraseña
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="confirmar_clave" 
                        name="confirmar_clave" 
                        placeholder="Repite tu contraseña" 
                        required 
                    />
                    <div class="invalid-feedback">
                        Las contraseñas no coinciden.
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terminos" required>
                    <label class="form-check-label" for="terminos">
                        Acepto los <a href="#" class="text-primary">términos y condiciones</a> del servicio
                    </label>
                    <div class="invalid-feedback">
                        Debes aceptar los términos y condiciones.
                    </div>
                </div>

                <button type="submit" class="register-btn w-100 mb-3">
                    <i class="fas fa-user-plus me-2"></i>
                    Crear cuenta
                </button>

                <div class="text-center">
                    <a href="login.php" class="login-link">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        ¿Ya tienes cuenta? Inicia sesión
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
            
            const claveInput = document.getElementById('clave');
            const confirmarClaveInput = document.getElementById('confirmar_clave');
            
            function validarContrasenas() {
                if (claveInput.value !== confirmarClaveInput.value) {
                    confirmarClaveInput.setCustomValidity('Las contraseñas no coinciden');
                } else {
                    confirmarClaveInput.setCustomValidity('');
                }
            }
            
            if (claveInput && confirmarClaveInput) {
                claveInput.addEventListener('input', validarContrasenas);
                confirmarClaveInput.addEventListener('input', validarContrasenas);
            }
            
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    validarContrasenas(); 
                    
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
            
            const passwordStrength = document.createElement('div');
            passwordStrength.className = 'password-strength mt-1';
            claveInput.parentNode.appendChild(passwordStrength);
            
            if (claveInput) {
                claveInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    let messages = [];
                    
                    if (password.length >= 6) strength++;
                    else messages.push('Al menos 6 caracteres');
                    
                    if (/[a-z]/.test(password)) strength++;
                    else messages.push('Una minúscula');
                    
                    if (/[A-Z]/.test(password)) strength++;
                    else messages.push('Una mayúscula');
                    
                    if (/\d/.test(password)) strength++;
                    else messages.push('Un número');
                    
                    const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745'];
                    const texts = ['Débil', 'Regular', 'Buena', 'Fuerte'];
                    
                    if (password.length > 0) {
                        passwordStrength.innerHTML = `
                            <small class="text-${strength < 2 ? 'danger' : strength < 3 ? 'warning' : 'success'}">
                                Fortaleza: ${texts[strength - 1] || 'Muy débil'}
                                ${messages.length > 0 ? ' - Falta: ' + messages.join(', ') : ''}
                            </small>
                        `;
                    } else {
                        passwordStrength.innerHTML = '';
                    }
                });
            }
        });
    </script>
</body>
</html>
