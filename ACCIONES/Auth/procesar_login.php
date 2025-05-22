<?php

session_start(); 

// Si el usuario ya está logueado, no debería estar aquí, pero por si acaso.
if (isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php"); // Redirige a la raíz del proyecto/index.php
    exit();
}

// Verificar que el script se llama vía POST y que el botón de login fue presionado
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['login'])) {
    // Si no es POST o no viene del formulario, redirigir al login
    header("Location: ../../login.php");
    exit();
}

require_once '../../CONFIG/db_connection.php';

$email = trim($_POST['email']);
$contrasena_ingresada = $_POST['contrasena'];
$error_login = ''; // Variable para mensajes de error

if (empty($email) || empty($contrasena_ingresada)) {
    $error_login = "El correo electrónico y la contraseña son obligatorios.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_login = "El formato del correo electrónico no es válido.";
} else {
    // Buscar el usuario por email
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contrasena, activo FROM Usuarios WHERE email = ?");
    if ($stmt === false) {
        error_log("Error al preparar la consulta de login (MySQLi): " . $conn->error);
        $error_login = "Error en el servidor. Por favor, inténtalo más tarde.";
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            if (!$usuario['activo']) {
                $error_login = "Tu cuenta ha sido desactivada. Contacta al administrador.";
            } elseif (password_verify($contrasena_ingresada, $usuario['contrasena'])) {
                // Contraseña correcta, iniciar sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                session_regenerate_id(true); // Buena práctica de seguridad

                // Cerrar statement y conexión antes de redirigir
                $stmt->close();
                if (isset($conn) && $conn instanceof mysqli) {
                    $conn->close();
                }

                header("Location: ../../index.php"); // Redirige a la raíz del proyecto/index.php
                exit();
            } else {
                $error_login = "Correo electrónico o contraseña incorrectos.";
            }
        } else {
            $error_login = "Correo electrónico o contraseña incorrectos.";
        }
        $stmt->close();
    }
}

// Guardar el mensaje de error y el email en sesión para mostrarlos en login.php
$_SESSION['error_login'] = $error_login;
$_SESSION['login_intent_email'] = $email; // Para repoblar el campo email

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

header("Location: ../../login.php"); 
exit();
?>