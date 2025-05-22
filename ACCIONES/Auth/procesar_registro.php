<?php
session_start();

// Redirigir si ya está logueado
if (isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../CONFIG/db_connection.php'; //

// Función para redirigir con mensaje de error y datos del formulario
function redirigir_con_error($mensaje, $datos_formulario = []) {
    $_SESSION['error_registro'] = $mensaje;
    if (!empty($datos_formulario)) {
        $_SESSION['form_data'] = $datos_formulario;
    }
    header("Location: ../../registro.php"); // Ajusta esta ruta si es diferente
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) { //
    $nombre_usuario = trim($_POST['nombre_usuario']); //
    $email = trim($_POST['email']); //
    $contrasena = $_POST['contrasena']; //
    $confirmar_contrasena = $_POST['confirmar_contrasena']; //

    $datos_formulario_error = [
        'nombre_usuario' => $nombre_usuario,
        'email' => $email
        // No guardamos las contraseñas por seguridad
    ];

    // --- Validaciones ---
    if (empty($nombre_usuario) || empty($email) || empty($contrasena) || empty($confirmar_contrasena)) { //
        redirigir_con_error("Todos los campos son obligatorios.", $datos_formulario_error); //
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //
        redirigir_con_error("El formato del correo electrónico no es válido.", $datos_formulario_error); //
    } elseif (strlen($contrasena) < 8) { //
        redirigir_con_error("La contraseña debe tener al menos 8 caracteres.", $datos_formulario_error); //
    } elseif ($contrasena !== $confirmar_contrasena) { //
        redirigir_con_error("Las contraseñas no coinciden.", $datos_formulario_error); //
    } else {
        // Verificar si el email ya existe
        if (!$conn) { 
             error_log("Error de conexión a la base de datos no establecida.");
             redirigir_con_error("Error en el servidor al conectar con la base de datos. Por favor, inténtalo más tarde.", $datos_formulario_error);
        }

        $stmt_check = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE email = ?"); //
        if ($stmt_check === false) {
            error_log("Error al preparar la consulta de verificación de email (MySQLi): " . $conn->error); //
            redirigir_con_error("Error en el servidor (check query). Por favor, inténtalo más tarde.", $datos_formulario_error); //
        } else {
            $stmt_check->bind_param("s", $email); //
            $stmt_check->execute(); //
            $stmt_check->store_result(); //

            if ($stmt_check->num_rows > 0) { //
                $stmt_check->close(); //
                redirigir_con_error("Este correo electrónico ya está registrado. Intenta con otro.", $datos_formulario_error); //
            } else {
                $stmt_check->close(); //
                // El email no existe, proceder con el registro
                $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT); //

                $stmt_insert = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, contrasena, activo) VALUES (?, ?, ?, TRUE)"); //
                if ($stmt_insert === false) {
                     error_log("Error al preparar la consulta de inserción (MySQLi): " . $conn->error); //
                    redirigir_con_error("Error en el servidor (insert query). Por favor, inténtalo más tarde.", $datos_formulario_error); //
                } else {
                    $stmt_insert->bind_param("sss", $nombre_usuario, $email, $contrasena_hasheada); //
                    if ($stmt_insert->execute()) { //
                        $id_nuevo_usuario = $stmt_insert->insert_id; //
                        $_SESSION['id_usuario'] = $id_nuevo_usuario; //
                        $_SESSION['nombre_usuario'] = $nombre_usuario; //
                        
                        // Limpiar cualquier dato de error o formulario de la sesión en caso de éxito
                        unset($_SESSION['error_registro']);
                        unset($_SESSION['form_data']);

                        $stmt_insert->close(); //
                        if (isset($conn) && $conn instanceof mysqli) { //
                            $conn->close(); //
                        }
                        header("Location: ../../index.php"); //
                        exit();
                    } else {
                        error_log("Error al ejecutar la inserción (MySQLi): " . $stmt_insert->error); //
                        $stmt_insert->close(); //
                        redirigir_con_error("Hubo un error al registrar tu cuenta. Inténtalo de nuevo más tarde.", $datos_formulario_error); //
                    }
                }
            }
        }
    }
} else {
  
    header("Location: ../../registro.php");
    exit();
}

// Cerrar la conexión si sigue abierta
if (isset($conn) && $conn instanceof mysqli) { //
    $conn->close(); //
}
?>