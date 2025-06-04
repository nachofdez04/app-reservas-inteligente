<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Sistema de Reservas de Parking</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <link rel="stylesheet" href="assets/css/main.css">
    
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <meta name="description" content="Sistema de gestión y reservas de plazas de parking">
    <meta name="author" content="Sistema de Reservas TFG">
</head>
<body>
    <?php if (isset($show_loading) && $show_loading): ?>
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner">
            <i class="fa-solid fa-circle-notch fa-spin"></i>
            <p>Cargando...</p>
        </div>
    </div>
    
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        
        .loading-overlay.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .loading-spinner {
            text-align: center;
            color: #2ca0f7;
        }
        
        .loading-spinner i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .loading-spinner p {
            margin: 0;
            font-weight: 500;
        }
    </style>
    
    <script>
        window.addEventListener('load', function() {
            const loading = document.getElementById('loading-overlay');
            if (loading) {
                loading.classList.add('fade-out');
                setTimeout(() => loading.remove(), 300);
            }
        });
    </script>
    <?php endif; ?>
