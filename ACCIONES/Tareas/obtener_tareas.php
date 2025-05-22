<?php
// Este archivo asume que session_start() ya fue llamado
// y que $conn (la conexión a la BD) y $_SESSION['id_usuario'] están disponibles.

if (!isset($conn) || !isset($_SESSION['id_usuario'])) {
    // Manejar el caso donde las dependencias no están listas
    // Esto no debería pasar si se incluye correctamente.
    error_log("Error: obtener_tareas.php llamado sin conexión a BD o sesión de usuario.");
    $tareas = []; // Asegurar que $tareas exista
  
    return;
}

$id_usuario_actual_inc = $_SESSION['id_usuario']; // Usar un nombre de variable diferente para evitar colisiones


$tareas = []; // Inicializar $tareas aquí para asegurar que siempre esté definida
$sql_tareas_inc = "SELECT id_tarea, titulo_tarea, descripcion_detallada, fecha_limite, prioridad, estado_tarea
                   FROM Tareas
                   WHERE id_usuario_fk = ? AND activa = TRUE
                   ORDER BY FIELD(prioridad, 'Alta', 'Media', 'Baja'), fecha_limite ASC, fecha_creacion DESC";

$stmt_tareas_inc = $conn->prepare($sql_tareas_inc);

if ($stmt_tareas_inc) {
    $stmt_tareas_inc->bind_param("i", $id_usuario_actual_inc);
    $stmt_tareas_inc->execute();
    $result_tareas_inc = $stmt_tareas_inc->get_result();
    while ($fila_inc = $result_tareas_inc->fetch_assoc()) {
        $tareas[] = $fila_inc; // Esta variable $tareas es la que se manda a index.php
    }
    $stmt_tareas_inc->close();
} else {
    error_log("Error al preparar la consulta de tareas: " . $conn->error);
    
}
// No se cierra la conn por los includes y otras cosas que la puedan necesitar aun
?>