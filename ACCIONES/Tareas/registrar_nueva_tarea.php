<?php

session_start(); 

// Verificar si el usuario está logueado Y si el formulario fue enviado correctamente
if (!isset($_SESSION['id_usuario'])) {
    // Si no hay sesión de usuario, redirigir al login principal
    header("Location: ../../login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['crear_tarea'])) {
    // Si no es un POST o no se presionó el botón 'crear_tarea',
    $_SESSION['error_tarea'] = "Acceso no válido para registrar tarea.";
    header("Location: ../../crear_tarea.php"); 
    exit();
}


require_once '../../CONFIG/db_connection.php';

$id_usuario = $_SESSION['id_usuario'];
$titulo_tarea = trim($_POST['titulo_tarea']);
$descripcion_detallada = isset($_POST['descripcion_detallada']) ? trim($_POST['descripcion_detallada']) : null;
$fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
$prioridad = $_POST['prioridad'];

// Guardar datos del formulario en sesión para repoblar en caso de error
$_SESSION['form_data'] = $_POST;

// Validaciones
if (empty($titulo_tarea)) {
    $_SESSION['error_tarea'] = "El título de la tarea es obligatorio.";
    header("Location: ../../crear_tarea.php"); // Redirige a crear_tarea.php en la raíz
    exit();
}
if (!in_array($prioridad, ['Alta', 'Media', 'Baja'])) {
    $_SESSION['error_tarea'] = "La prioridad seleccionada no es válida.";
    header("Location: ../../crear_tarea.php");
    exit();
}
// Validación de fecha (solo si se proporciona)
if ($fecha_limite !== null && $fecha_limite !== '' && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha_limite)) {
    $_SESSION['error_tarea'] = "El formato de la fecha límite no es válido.";
    header("Location: ../../crear_tarea.php");
    exit();
}

if ($fecha_limite === '') {
    $fecha_limite = null;
}


$sql_insert = "INSERT INTO Tareas (id_usuario_fk, titulo_tarea, descripcion_detallada, fecha_limite, prioridad, estado_tarea, activa)
               VALUES (?, ?, ?, ?, ?, 'Pendiente', TRUE)";
$stmt = $conn->prepare($sql_insert);

if ($stmt) {
    $stmt->bind_param("issss", $id_usuario, $titulo_tarea, $descripcion_detallada, $fecha_limite, $prioridad);
    if ($stmt->execute()) {
        unset($_SESSION['form_data']); // Limpiar datos de formulario si fue exitoso
        $_SESSION['mensaje_exito'] = "¡Tarea creada exitosamente!"; 
        header("Location: ../../index.php"); 
        exit();
    } else {
        error_log("Error al registrar tarea (MySQLi): " . $stmt->error);
        $_SESSION['error_tarea'] = "Error al guardar la tarea. (" . $stmt->errno . ")";
    }
    $stmt->close();
} else {
    error_log("Error al preparar la inserción de tarea (MySQLi): " . $conn->error);
    $_SESSION['error_tarea'] = "Error del servidor al preparar la tarea.";
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}


header("Location: ../../crear_tarea.php");
exit();
?>