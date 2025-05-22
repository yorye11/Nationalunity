<?php
session_start(); 


if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}


$error_login = '';
if (isset($_SESSION['error_login'])) {
    $error_login = $_SESSION['error_login'];
    unset($_SESSION['error_login']); // Limpiar el mensaje para que no se muestre de nuevo
}

$login_intent_email = '';
if (isset($_SESSION['login_intent_email'])) {
    $login_intent_email = $_SESSION['login_intent_email'];
    unset($_SESSION['login_intent_email']); // Limpiar el email
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestor de Tareas</title>
    <link rel="stylesheet" href="CSS/estilos_auth.css">
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
            <h2>Iniciar Sesión</h2>
            <p class="auth-subtitle">Bienvenido de nuevo. Ingresa tus credenciales.</p>

            <?php if (!empty($error_login)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_login); ?></p>
            <?php endif; ?>

            <form action="ACCIONES/Auth/procesar_login.php" method="POST" novalidate>
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($login_intent_email); ?>" required>
                </div>
                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Ingresar</button>
            </form>
            <div class="auth-links">
                <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </div>
        </div>
        <div class="auth-image-section">
            <img src="IMG/login-bg.jpg" alt="Tareas Organizadas">
        </div>
    </div>

    <script src="JS/Modo_oscuro.js"></script>
</body>
</html>