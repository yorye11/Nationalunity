<?php

$nombre_usuario_actual_header = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Invitado';
$esta_logueado = isset($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos_main.css">
    <link rel="stylesheet" href="css/estilos_forms_tareas.css"> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class=""> <header class="main-header">
        <div class="theme-switcher-container">
            <button id="theme-toggle" class="theme-toggle-btn">
                <i class="fas fa-moon"></i>
            </button>
        </div>
        <a href="index.php" class="logo">GestorTareas</a>
        <?php if ($esta_logueado): ?>
        <div class="header-actions">
            <span class="welcome-user">Hola, <?php echo htmlspecialchars($nombre_usuario_actual_header); ?></span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
        <?php endif; ?>
    </header>