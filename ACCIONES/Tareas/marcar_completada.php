<?php

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php"); // Sube dos niveles
    exit();
}

// Verificar que se recibe un ID y es un entero
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje_error'] = "ID de tarea no válido.";
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../CONFIG/db_connection.php'; 

$id_tarea_completar = $_GET['id'];
$id_usuario_actual_completar = $_SESSION['id_usuario'];

$sql_complete = "UPDATE Tareas SET estado_tarea = 'Completada' 
                 WHERE id_tarea = ? AND id_usuario_fk = ? AND activa = TRUE";
$stmt_complete = $conn->prepare($sql_complete);

if ($stmt_complete) {
    $stmt_complete->bind_param("ii", $id_tarea_completar, $id_usuario_actual_completar);
    if ($stmt_complete->execute()) {
        if ($stmt_complete->affected_rows > 0) {
            $_SESSION['mensaje_exito'] = "¡Tarea marcada como completada!";
        } else {
            // No error, pero no afectó filas (quizás ya estaba completada o no pertenece al usuario)
            $_SESSION['mensaje_error'] = "No se pudo marcar la tarea como completada o ya lo estaba.";
        }
    } else {
        error_log("Error al marcar tarea completada (MySQLi): " . $stmt_complete->error);
        $_SESSION['mensaje_error'] = "Error al actualizar la tarea. (" . $stmt_complete->errno .")";
    }
    $stmt_complete->close();
} else {
    error_log("Error al preparar la consulta para completar tarea (MySQLi): " . $conn->error);
    $_SESSION['mensaje_error'] = "Error del servidor al procesar la solicitud.";
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

header("Location: ../../index.php");
exit();
?>