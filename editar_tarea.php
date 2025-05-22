<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'CONFIG/db_connection.php'; // Ajusta la ruta si es necesario
$id_usuario_actual = $_SESSION['id_usuario'];
$tarea_id_get = null;
$tarea = null;
$error_carga = '';

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $tarea_id_get = $_GET['id'];

    $sql_select = "SELECT id_tarea, titulo_tarea, descripcion_detallada, fecha_limite, prioridad, estado_tarea
                   FROM Tareas
                   WHERE id_tarea = ? AND id_usuario_fk = ? AND activa = TRUE"; // Solo tareas activas
    $stmt_select = $conn->prepare($sql_select);

    if ($stmt_select) {
        $stmt_select->bind_param("ii", $tarea_id_get, $id_usuario_actual);
        if ($stmt_select->execute()) {
            $result = $stmt_select->get_result();
            if ($result->num_rows === 1) {
                $tarea = $result->fetch_assoc();
            } else {
                $_SESSION['mensaje_error'] = "Tarea no encontrada o no tienes permiso para editarla.";
                header("Location: index.php");
                exit();
            }
        } else {
            $error_carga = "Error al ejecutar la búsqueda de la tarea.";
            error_log("Error ejecución select editar_tarea: " . $stmt_select->error);
        }
        $stmt_select->close();
    } else {
        $error_carga = "Error al preparar la consulta para cargar la tarea.";
        error_log("Error preparación select editar_tarea: " . $conn->error);
    }
} else {
    $_SESSION['mensaje_error'] = "ID de tarea no válido o no proporcionado.";
    header("Location: index.php");
    exit();
}

// Mostrar mensajes de error desde la sesión (si vienen de una redirección de la acción de actualizar)
$error_tarea_edit = isset($_SESSION['error_tarea_edit']) ? $_SESSION['error_tarea_edit'] : '';
unset($_SESSION['error_tarea_edit']); // Limpiar después de mostrar

// Repoblar campos si hay un error y se guardaron datos en sesión
$titulo_val = isset($_SESSION['form_edit_data']['titulo_tarea']) ? htmlspecialchars($_SESSION['form_edit_data']['titulo_tarea']) : ($tarea ? htmlspecialchars($tarea['titulo_tarea']) : '');
$descripcion_val = isset($_SESSION['form_edit_data']['descripcion_detallada']) ? htmlspecialchars($_SESSION['form_edit_data']['descripcion_detallada']) : ($tarea ? htmlspecialchars($tarea['descripcion_detallada']) : '');
$fecha_limite_val = isset($_SESSION['form_edit_data']['fecha_limite']) ? htmlspecialchars($_SESSION['form_edit_data']['fecha_limite']) : ($tarea && $tarea['fecha_limite'] ? htmlspecialchars($tarea['fecha_limite']) : '');
$prioridad_val = isset($_SESSION['form_edit_data']['prioridad']) ? htmlspecialchars($_SESSION['form_edit_data']['prioridad']) : ($tarea ? htmlspecialchars($tarea['prioridad']) : 'Media');
$estado_val = isset($_SESSION['form_edit_data']['estado_tarea']) ? htmlspecialchars($_SESSION['form_edit_data']['estado_tarea']) : ($tarea ? htmlspecialchars($tarea['estado_tarea']) : 'Pendiente');
unset($_SESSION['form_edit_data']);


require_once 'INCLUDES/header.php';
?>
<title>Editar Tarea - Gestor</title>

<div class="container form-container">
    <h1><i class="fas fa-edit"></i> Editar Tarea</h1>

    <?php if (!empty($error_carga)): ?>
        <p class="error-message-form"><?php echo htmlspecialchars($error_carga); ?></p>
        <a href="index.php" class="btn-cancel" style="display:inline-block; margin-top:10px;">Volver a mis tareas</a>
    <?php elseif ($tarea): ?>
        <?php if (!empty($error_tarea_edit)): ?>
            <p class="error-message-form"><?php echo htmlspecialchars($error_tarea_edit); ?></p>
        <?php endif; ?>

        <form action="ACCIONES/Tareas/actualizar_datos_tarea.php" method="POST">
            <input type="hidden" name="id_tarea" value="<?php echo htmlspecialchars($tarea['id_tarea']); ?>">

            <div class="form-group">
                <label for="titulo_tarea">Título de la Tarea <span style="color:red;">*</span></label>
                <input type="text" id="titulo_tarea" name="titulo_tarea" value="<?php echo $titulo_val; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion_detallada">Descripción (Opcional)</label>
                <textarea id="descripcion_detallada" name="descripcion_detallada"><?php echo $descripcion_val; ?></textarea>
            </div>
            <div class="form-group">
                <label for="fecha_limite">Fecha Límite (Opcional)</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo $fecha_limite_val; ?>">
            </div>
            <div class="form-group">
                <label for="prioridad">Prioridad <span style="color:red;">*</span></label>
                <select id="prioridad" name="prioridad" required>
                    <option value="Baja" <?php echo ($prioridad_val == 'Baja') ? 'selected' : ''; ?>>Baja</option>
                    <option value="Media" <?php echo ($prioridad_val == 'Media') ? 'selected' : ''; ?>>Media</option>
                    <option value="Alta" <?php echo ($prioridad_val == 'Alta') ? 'selected' : ''; ?>>Alta</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_tarea">Estado <span style="color:red;">*</span></label>
                <select id="estado_tarea" name="estado_tarea" required>
                    <option value="Pendiente" <?php echo ($estado_val == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Progreso" <?php echo ($estado_val == 'En Progreso') ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="Completada" <?php echo ($estado_val == 'Completada') ? 'selected' : ''; ?>>Completada</option>
                    <option value="Cancelada" <?php echo ($estado_val == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="form-actions">
                <a href="index.php" class="btn-cancel">Cancelar</a>
                <button type="submit" name="actualizar_tarea" class="btn-submit">Actualizar Tarea</button>
            </div>
        </form>
    <?php else: ?>
        <p class="error-message-form">No se pudo cargar la tarea para editar o no existe.</p>
         <a href="index.php" class="btn-cancel" style="display:inline-block; margin-top:10px;">Volver a mis tareas</a>
    <?php endif; ?>
    <?php
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    ?>
</div>

<?php require_once 'INCLUDES/footer.php'; ?>