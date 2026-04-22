<?php
session_start();
require '../conexion.php';

// Verificar permisos para gestionar pedidos
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_pedidos_proveedor')) {
    mostrar_error_permisos('gestion_pedidos_proveedor');
    exit;
}

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pedido_id'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $estado = $conexion->real_escape_string($_POST['estado']);

    $sql_actualizar = "UPDATE pedidos_proveedor SET estado = '$estado'";
    
    if ($estado == 'aceptado') {
        $sql_actualizar .= ", fecha_aceptado = NOW()";
    } elseif ($estado == 'enviado') {
        $sql_actualizar .= ", fecha_enviado = NOW()";
    } elseif ($estado == 'completado') {
        $sql_actualizar .= ", fecha_enviado = NOW()";
    }
    
    $sql_actualizar .= " WHERE id = $pedido_id";

    if ($conexion->query($sql_actualizar)) {
        $mensaje = "Estado del pedido actualizado correctamente a: " . ucfirst($estado);
    } else {
        $error = "Error al actualizar el estado: " . $conexion->error;
    }
}

// Obtener todos los pedidos
$sql_pedidos = "SELECT pp.*, u.nombre as almacenista_nombre 
                FROM pedidos_proveedor pp
                LEFT JOIN usuarios u ON pp.almacenista_id = u.id
                ORDER BY pp.fecha_pedido DESC";
$result_pedidos = $conexion->query($sql_pedidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (tiene_permiso('gestion_pedidos_proveedor')): ?>
                        <li><a href="gestion_pedidos.php" class="active">Gestión de Pedidos</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión de Pedidos de Productos</h2>
            <p>Gestiona las solicitudes de productos de los almacenistas</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Almacenista</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Fecha Pedido</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_pedidos && $result_pedidos->num_rows > 0): ?>
                        <?php while($pedido = $result_pedidos->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro"><?php echo $pedido['id']; ?></td>
                                <td><?php echo htmlspecialchars($pedido['almacenista_nombre'] ?: 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['producto_nombre']); ?></td>
                                <td class="texto-centro"><?php echo $pedido['cantidad']; ?></td>
                                <td class="texto-centro">
                                    <span class="estado <?php echo $pedido['estado']; ?>">
                                        <?php echo ucfirst($pedido['estado']); ?>
                                    </span>
                                </td>
                                <td class="texto-centro">
                                    <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($pedido['notas']); ?></td>
                                <td>
                                    <form method="post" class="acciones-tabla">
                                        <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                        <select name="estado" onchange="this.form.submit()" class="btn btn-sm">
                                            <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="aceptado" <?php echo $pedido['estado'] == 'aceptado' ? 'selected' : ''; ?>>Aceptado</option>
                                            <option value="enviado" <?php echo $pedido['estado'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                            <option value="completado" <?php echo $pedido['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <p>No hay pedidos pendientes</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>