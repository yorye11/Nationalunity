<?php

session_start();

// Verificar si el usuario está logueado y si el formulario fue enviado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['actualizar_tarea'])) {
    $_SESSION['mensaje_error'] = "Acceso no válido.";
    header("Location: ../../index.php");
    exit();
}

require_once '../../CONFIG/db_connection.php'; 

$id_usuario_sesion = $_SESSION['id_usuario'];
$id_tarea = filter_input(INPUT_POST, 'id_tarea', FILTER_VALIDATE_INT);

$titulo_tarea = trim($_POST['titulo_tarea']);
$descripcion_detallada = isset($_POST['descripcion_detallada']) ? trim($_POST['descripcion_detallada']) : null;
$fecha_limite_post = $_POST['fecha_limite']; // Obtener el valor tal cual del POST
$prioridad = $_POST['prioridad'];
$estado_tarea = $_POST['estado_tarea'];

// Guardar datos del formulario en sesión para repoblar en caso de error
$_SESSION['form_edit_data'] = $_POST;

// Validaciones
if (!$id_tarea) {
    $_SESSION['mensaje_error'] = "ID de tarea no válido.";
    header("Location: ../../index.php");
    exit();
}
if (empty($titulo_tarea)) {
    $_SESSION['error_tarea_edit'] = "El título de la tarea es obligatorio.";
    header("Location: ../../editar_tarea.php?id=" . $id_tarea); 
    exit();
}
if (!in_array($prioridad, ['Alta', 'Media', 'Baja'])) {
    $_SESSION['error_tarea_edit'] = "La prioridad seleccionada no es válida.";
    header("Location: ../../editar_tarea.php?id=" . $id_tarea);
    exit();
}
if (!in_array($estado_tarea, ['Pendiente', 'En Progreso', 'Completada', 'Cancelada'])) {
    $_SESSION['error_tarea_edit'] = "El estado seleccionado no es válido.";
    header("Location: ../../editar_tarea.php?id=" . $id_tarea);
    exit();
}

// Validar y formatear fecha límite
$fecha_limite_para_bd = null;
if (!empty($fecha_limite_post)) {
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha_limite_post)) {
        $_SESSION['error_tarea_edit'] = "El formato de la fecha límite no es válido.";
        header("Location: ../../editar_tarea.php?id=" . $id_tarea);
        exit();
    }
    $fecha_limite_para_bd = $fecha_limite_post;
}


$sql_update = "UPDATE Tareas SET
                    titulo_tarea = ?,
                    descripcion_detallada = ?,
                    fecha_limite = ?,
                    prioridad = ?,
                    estado_tarea = ?
               WHERE id_tarea = ? AND id_usuario_fk = ?";

$stmt_update = $conn->prepare($sql_update);

if ($stmt_update) {
    $stmt_update->bind_param("sssssii", $titulo_tarea, $descripcion_detallada, $fecha_limite_para_bd, $prioridad, $estado_tarea, $id_tarea, $id_usuario_sesion);
    if ($stmt_update->execute()) {
        unset($_SESSION['form_edit_data']); // Limpiar datos de formulario si fue exitoso
        if ($stmt_update->affected_rows > 0) {
            $_SESSION['mensaje_exito'] = "¡Tarea actualizada exitosamente!";
        } else {
            $_SESSION['mensaje_exito'] = "No se realizaron cambios en la tarea (o ya estaba actualizada).";
        }
        header("Location: ../../index.php"); // Sube dos niveles
        exit();
    } else {
        error_log("Error al actualizar tarea (MySQLi): " . $stmt_update->error);
        $_SESSION['error_tarea_edit'] = "Error al actualizar la tarea. (" . $stmt_update->errno . ")";
    }
    $stmt_update->close();
} else {
    error_log("Error al preparar la actualización de tarea (MySQLi): " . $conn->error);
    $_SESSION['error_tarea_edit'] = "Error del servidor al preparar la actualización.";
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

header("Location: ../../editar_tarea.php?id=" . $id_tarea);
exit();
?>