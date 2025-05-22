<?php
session_start();


if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$error_registro = '';
if (isset($_SESSION['error_registro'])) {
    $error_registro = $_SESSION['error_registro'];
    unset($_SESSION['error_registro']); 
}


$nombre_usuario_valor = '';
$email_valor = '';

if (isset($_SESSION['form_data'])) {
    $nombre_usuario_valor = htmlspecialchars($_SESSION['form_data']['nombre_usuario'] ?? '');
    $email_valor = htmlspecialchars($_SESSION['form_data']['email'] ?? '');
    unset($_SESSION['form_data']); 
}


$success_registro = '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Gestor de Tareas</title>
    <link rel="stylesheet" href="css/estilos_auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class=""> 
    <div class="theme-switcher-container">
        <button id="theme-toggle" class="theme-toggle-btn">
            <i class="fas fa-moon"></i> </button>
    </div>

    <div class="auth-container">
        <div class="auth-form-wrapper">
            <h2>Crear Cuenta</h2>
            <p class="auth-subtitle">Únete y empieza a organizar tus tareas.</p>

            <?php if (!empty($error_registro)): ?>
                <p class="error-message"><?php echo $error_registro; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_registro)): ?>
                <p class="success-message"><?php echo $success_registro; ?></p>
            <?php endif; ?>

            <?php if (empty($success_registro)): ?>
               <form id="registroForm" action="ACCIONES/Auth/procesar_registro.php" method="POST" novalidate>
                <div class="input-group">
                    <label for="nombre_usuario">Nombre Completo</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo $nombre_usuario_valor; ?>" required>
                    <small class="error-text" id="error_nombre_usuario"></small>
                </div>
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_valor; ?>" required>
                    <small class="error-text" id="error_email"></small>
                </div>
                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                    <small class="error-text" id="error_contrasena"></small>
                </div>
                <div class="input-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                    <small class="error-text" id="error_confirmar_contrasena"></small>
                </div>
                <button type="submit" name="registro" class="btn btn-primary">Registrarme</button>
            </form>
            <?php endif; ?>
            <div class="auth-links">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
         <div class="auth-image-section">
            <img src="img/login-bg.jpg" alt="Colaboración y Organización">
            </div>
    </div>
    <script src="JS/Modo_oscuro.js"> </script>
    <script src="JS/Registro.js"> </script>            
</body>
</html>