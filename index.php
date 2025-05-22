<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'CONFIG/db_connection.php'; 

$nombre_usuario_actual = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Usuario';

require_once 'ACCIONES/Tareas/obtener_tareas.php'; // 

require_once 'INCLUDES/header.php';
?>
<title>Mis Tareas - Gestor</title>

    <div class="container">
        <h1 class="page-title">Mis Tareas Pendientes</h1>

        <a href="crear_tarea.php" class="btn-add-task">Nueva Tarea <i class="fas fa-plus"></i></a>

        <?php if (!empty($_GET['mensaje_exito'])): ?>
            <p class="success-message" style="background-color: var(--success-bg-color); color: var(--success-text-color); border: 1px solid var(--success-border-color); padding: 10px; border-radius: var(--border-radius); margin-bottom: 15px;">
                <?php echo htmlspecialchars($_GET['mensaje_exito']); ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($_GET['mensaje_error'])): ?>
            <p class="error-message" style="background-color: var(--error-bg-color); color: var(--error-text-color); border: 1px solid var(--error-border-color); padding: 10px; border-radius: var(--border-radius); margin-bottom: 15px;">
                <?php echo htmlspecialchars($_GET['mensaje_error']); ?>
            </p>
        <?php endif; ?>


        <?php if (isset($tareas) && count($tareas) > 0): ?>
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $tarea): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tarea['titulo_tarea']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower(htmlspecialchars($tarea['prioridad'])); ?>">
                                    <?php echo htmlspecialchars($tarea['prioridad']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo str_replace(' ', '-', strtolower(htmlspecialchars($tarea['estado_tarea']))); ?>">
                                    <?php echo htmlspecialchars($tarea['estado_tarea']); ?>
                                </span>
                            </td>
                            <td><?php echo $tarea['fecha_limite'] ? htmlspecialchars(date("d/m/Y", strtotime($tarea['fecha_limite']))) : 'N/A'; ?></td>
                            <td class="actions">
                                <a href="editar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>" class="btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <?php if ($tarea['estado_tarea'] !== 'Completada'): ?>
                                <a href="ACCIONES/Tareas/marcar_completada.php?id=<?php echo $tarea['id_tarea']; ?>" class="btn-complete" title="Marcar como completada" onclick="return confirm('¿Marcar esta tarea como completada?');"><i class="fas fa-check-circle"></i></a>
                                <?php endif; ?>
                                <a href="ACCIONES/Tareas/archivar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>" class="btn-archive" title="Archivar" onclick="return confirm('¿Está seguro de que desea archivar esta tarea?');"><i class="fas fa-archive"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<?php

require_once 'INCLUDES/footer.php';


if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>