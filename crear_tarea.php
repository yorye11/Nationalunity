<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); 
    exit();
}

$error_tarea = '';
$success_tarea = '';

// Recuperar datos del formulario si hubo un error para repoblar
$titulo_tarea_val = isset($_SESSION['form_data']['titulo_tarea']) ? htmlspecialchars($_SESSION['form_data']['titulo_tarea']) : '';
$descripcion_val = isset($_SESSION['form_data']['descripcion_detallada']) ? htmlspecialchars($_SESSION['form_data']['descripcion_detallada']) : '';
$fecha_limite_val = isset($_SESSION['form_data']['fecha_limite']) ? htmlspecialchars($_SESSION['form_data']['fecha_limite']) : '';
$prioridad_val = isset($_SESSION['form_data']['prioridad']) ? htmlspecialchars($_SESSION['form_data']['prioridad']) : 'Media'; // Default
// Limpiar datos de formulario de la sesión después de usarlos
unset($_SESSION['form_data']);

// Mostrar mensajes de error o éxito desde la sesión (si vienen de una redirección)
if (isset($_SESSION['error_tarea'])) {
    $error_tarea = $_SESSION['error_tarea'];
    unset($_SESSION['error_tarea']);
}
if (isset($_SESSION['success_tarea'])) {
    $success_tarea = $_SESSION['success_tarea'];
    unset($_SESSION['success_tarea']);
}


require_once 'INCLUDES/header.php';
?>
<title>Crear Nueva Tarea - Gestor</title>

<div class="container form-container">
    <h1><i class="fas fa-plus-circle"></i> Crear Nueva Tarea</h1>

    <?php if (!empty($error_tarea)): ?>
        <p class="error-message-form"><?php echo $error_tarea; ?></p>
    <?php endif; ?>
    <?php if (!empty($success_tarea)): ?>
        <p class="success-message" style="background-color: var(--success-bg-color); color: var(--success-text-color); border: 1px solid var(--success-border-color); padding: 10px; border-radius: var(--border-radius); margin-bottom: 15px;"><?php echo $success_tarea; ?></p>
    <?php endif; ?>

    <form action="ACCIONES/Tareas/registrar_nueva_tarea.php" method="POST">
        <div class="form-group">
            <label for="titulo_tarea">Título de la Tarea <span style="color:red;">*</span></label>
            <input type="text" id="titulo_tarea" name="titulo_tarea" value="<?php echo $titulo_tarea_val; ?>" required>
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
                <option value="Media" <?php echo ($prioridad_val == 'Media' || empty($prioridad_val)) ? 'selected' : ''; ?>>Media</option>
                <option value="Alta" <?php echo ($prioridad_val == 'Alta') ? 'selected' : ''; ?>>Alta</option>
            </select>
        </div>
        <div class="form-actions">
            <a href="index.php" class="btn-cancel">Cancelar</a>
            <button type="submit" name="crear_tarea" class="btn-submit">Guardar Tarea</button>
        </div>
    </form>
</div>

<?php
require_once 'INCLUDES/footer.php';
?>