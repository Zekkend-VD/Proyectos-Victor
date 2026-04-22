<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_compras');
// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_compras')) {
    header('Location: ../login.php');
    exit;
}

// Obtener compras
$sql = "SELECT c.*, u.nombre as usuario_nombre 
        FROM compras c 
        JOIN usuarios u ON c.usuario_id = u.id 
        ORDER BY c.fecha_compra DESC";
$result = $conexion->query($sql);

// Obtener estadísticas
$sql_estadisticas = "SELECT 
                    COUNT(*) as total_compras,
                    SUM(total) as ingreso_total,
                    AVG(total) as promedio_compra,
                    COUNT(CASE WHEN estado = 'completada' THEN 1 END) as completadas,
                    COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
                    COUNT(CASE WHEN estado = 'cancelada' THEN 1 END) as canceladas
                    FROM compras";
$result_estadisticas = $conexion->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Compras - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="gestion_enfermedades.php">Gestión de Enfermedades</a></li>
                    <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                    <li><a href="gestion_compras.php" class="active">Gestión de Compras</a></li>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión de Compras</h2>
            <p>Administra las compras realizadas por los usuarios</p>
        </div>
        
        <div class="estadisticas">
            <div class="tarjeta-estadistica">
                <h3>Total Compras</h3>
                <p class="numero"><?php echo $estadisticas['total_compras']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Ingresos Totales</h3>
                <p class="numero">$<?php echo number_format($estadisticas['ingreso_total'] ?? 0, 2); ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Promedio por Compra</h3>
                <p class="numero">$<?php echo number_format($estadisticas['promedio_compra'] ?? 0, 2); ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Completadas</h3>
                <p class="numero"><?php echo $estadisticas['completadas']; ?></p>
            </div>
        </div>
        
        <div class="filtros-compras">
            <div class="filtros-group">
                <button class="btn btn-outline active">Todas (<?php echo $estadisticas['total_compras']; ?>)</button>
                <button class="btn btn-outline">Completadas (<?php echo $estadisticas['completadas']; ?>)</button>
                <button class="btn btn-outline">Pendientes (<?php echo $estadisticas['pendientes']; ?>)</button>
                <button class="btn btn-outline">Canceladas (<?php echo $estadisticas['canceladas']; ?>)</button>
            </div>
        </div>
        
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th class="columna-id">ID</th>
                        <th class="columna-nombre">Usuario</th>
                        <th class="columna-fecha">Fecha</th>
                        <th class="columna-precio">Total</th>
                        <th class="columna-estado">Estado</th>
                        <th class="columna-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($compra = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro">
                                    <strong>#<?php echo $compra['id']; ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($compra['usuario_nombre']); ?></td>
                                <td class="columna-fecha"><?php echo date('d/m/Y H:i', strtotime($compra['fecha_compra'])); ?></td>
                                <td class="texto-derecha"><strong>$<?php echo number_format($compra['total'], 2); ?></strong></td>
                                <td class="texto-centro">
                                    <span class="estado <?php echo $compra['estado']; ?>">
                                        <?php echo ucfirst($compra['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="ver_compra.php?id=<?php echo $compra['id']; ?>" class="btn btn-ver btn-sm">Ver Detalle</a>
                                        <a href="cambiar_estado.php?id=<?php echo $compra['id']; ?>" class="btn btn-editar btn-sm">Cambiar Estado</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <p>No hay compras registradas</p>
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