<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_compras');
// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_compras')) {
    header('Location: ../login.php');
    exit;
}

$compra_id = intval($_GET['id']);

// Obtener información de la compra
$sql_compra = "SELECT c.*, u.nombre as usuario_nombre, u.email, u.direccion, u.telefono 
               FROM compras c 
               JOIN usuarios u ON c.usuario_id = u.id 
               WHERE c.id = $compra_id";
$result_compra = $conexion->query($sql_compra);
$compra = $result_compra->fetch_assoc();

// Obtener detalles de la compra
$sql_detalles = "SELECT cd.*, m.nombre as medicamento_nombre 
                 FROM compras_detalle cd 
                 JOIN medicamentos m ON cd.medicamento_id = m.id 
                 WHERE cd.compra_id = $compra_id";
$result_detalles = $conexion->query($sql_detalles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Compra - Farmacia Online</title>
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
                    <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Detalle de Compra #<?php echo $compra['id']; ?></h2>
            <a href="gestion_compras.php" class="btn">Volver a Compras</a>
        </div>
        
        <div class="detalle-compra">
            <div class="info-cliente">
                <h3>Información del Cliente</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($compra['usuario_nombre']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong> <?php echo htmlspecialchars($compra['email']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Dirección:</strong> <?php echo htmlspecialchars($compra['direccion']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Teléfono:</strong> <?php echo htmlspecialchars($compra['telefono']); ?>
                    </div>
                </div>
            </div>
            
            <div class="info-compra">
                <h3>Información de la Compra</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Fecha:</strong> <?php echo $compra['fecha_compra']; ?>
                    </div>
                    <div class="info-item">
                        <strong>Total:</strong> $<?php echo number_format($compra['total'], 2); ?>
                    </div>
                    <div class="info-item">
                        <strong>Estado:</strong> 
                        <span class="estado <?php echo $compra['estado']; ?>">
                            <?php echo ucfirst($compra['estado']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <h3>Productos Comprados</h3>
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($detalle = $result_detalles->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['medicamento_nombre']); ?></td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                            <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                            <td>$<?php echo number_format($detalle['cantidad'] * $detalle['precio_unitario'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="total-fila">
                        <td colspan="3" class="texto-derecha"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($compra['total'], 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>