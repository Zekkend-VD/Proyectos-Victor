<?php
session_start();
require '../conexion.php';

// Verificar permisos para solicitar productos
if (!isset($_SESSION['rol']) || !tiene_permiso('solicitar_productos')) {
    mostrar_error_permisos('solicitar_productos');
    exit;
}

// Procesar el formulario de solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['producto_nombre'])) {
    $producto_nombre = $conexion->real_escape_string($_POST['producto_nombre']);
    $cantidad = intval($_POST['cantidad']);
    $notas = $conexion->real_escape_string($_POST['notas']);
    
    // Para usuarios especiales, usar un ID por defecto
    $almacenista_id = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] > 0 ? $_SESSION['usuario_id'] : 1;

    $sql = "INSERT INTO pedidos_proveedor (almacenista_id, producto_nombre, cantidad, notas, estado) 
            VALUES ($almacenista_id, '$producto_nombre', $cantidad, '$notas', 'pendiente')";

    if ($conexion->query($sql)) {
        $mensaje = "Solicitud de producto enviada correctamente al proveedor";
    } else {
        $error = "Error al enviar la solicitud: " . $conexion->error;
    }
}

// Obtener las solicitudes previas
$almacenista_id = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] > 0 ? $_SESSION['usuario_id'] : 1;
$sql_solicitudes = "SELECT * FROM pedidos_proveedor WHERE almacenista_id = $almacenista_id ORDER BY fecha_pedido DESC";
$result_solicitudes = $conexion->query($sql_solicitudes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Productos - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (tiene_permiso('gestion_inventario')): ?>
                        <li><a href="gestion_inventario.php">Gestión de Inventario</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('solicitar_productos')): ?>
                        <li><a href="solicitar_productos.php" class="active">Solicitar Productos</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Solicitar Productos al Proveedor</h2>
            <p>Envía solicitudes de productos para reponer el inventario</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h3>Nueva Solicitud</h3>
            <form method="post">
                <div class="campo">
                    <label for="producto_nombre">Nombre del Producto:</label>
                    <input type="text" id="producto_nombre" name="producto_nombre" required 
                           placeholder="Ej: Paracetamol 500mg, Amoxicilina 250mg">
                </div>

                <div class="campo">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" required min="1" 
                           placeholder="Ej: 100">
                </div>

                <div class="campo">
                    <label for="notas">Notas (opcional):</label>
                    <textarea id="notas" name="notas" 
                              placeholder="Detalles adicionales sobre el producto, urgencia, etc."></textarea>
                </div>

                <div class="acciones-form">
                    <button type="submit" class="btn btn-confirmar">Enviar Solicitud</button>
                    <a href="index.php" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>

        <div style="margin-top: 3rem;">
            <h3>Mis Solicitudes Anteriores</h3>
            <div class="tabla-contenedor">
                <table class="tabla-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_solicitudes && $result_solicitudes->num_rows > 0): ?>
                            <?php while($solicitud = $result_solicitudes->fetch_assoc()): ?>
                                <tr>
                                    <td class="texto-centro"><?php echo $solicitud['id']; ?></td>
                                    <td><?php echo htmlspecialchars($solicitud['producto_nombre']); ?></td>
                                    <td class="texto-centro"><?php echo $solicitud['cantidad']; ?></td>
                                    <td class="texto-centro">
                                        <span class="estado <?php echo $solicitud['estado']; ?>">
                                            <?php echo ucfirst($solicitud['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="texto-centro">
                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_pedido'])); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($solicitud['notas']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="texto-centro">
                                    <div class="mensaje-vacio">
                                        <p>No has realizado ninguna solicitud</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>