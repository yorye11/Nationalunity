<?php

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php"); 
    exit();
}

// Verificar que se recibe un ID y es un entero
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje_error'] = "ID de tarea no válido.";
    header("Location: ../../index.php");
    exit();
}

require_once '../../CONFIG/db_connection.php'; 

$id_tarea_archivar = $_GET['id'];
$id_usuario_actual_archivar = $_SESSION['id_usuario'];


$sql_archive = "UPDATE Tareas SET activa = FALSE, estado_tarea = 'Cancelada' 
                WHERE id_tarea = ? AND id_usuario_fk = ?"; // Solo el propio usuario puede archivar sus tareas
$stmt_archive = $conn->prepare($sql_archive);

if ($stmt_archive) {
    $stmt_archive->bind_param("ii", $id_tarea_archivar, $id_usuario_actual_archivar);
    if ($stmt_archive->execute()) {
        if ($stmt_archive->affected_rows > 0) {
            $_SESSION['mensaje_exito'] = "¡Tarea archivada correctamente!";
        } else {
            $_SESSION['mensaje_error'] = "No se pudo archivar la tarea o ya estaba archivada.";
        }
    } else {
        error_log("Error al archivar tarea (MySQLi): " . $stmt_archive->error);
        $_SESSION['mensaje_error'] = "Error al archivar la tarea. (" . $stmt_archive->errno . ")";
    }
    $stmt_archive->close();
} else {
    error_log("Error al preparar la consulta para archivar tarea (MySQLi): " . $conn->error);
    $_SESSION['mensaje_error'] = "Error del servidor al procesar la solicitud.";
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

header("Location: ../../index.php"); 
exit();
?>