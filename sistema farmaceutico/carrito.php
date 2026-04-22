<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los items del carrito
$sql = "SELECT c.id, m.nombre, m.precio, c.cantidad, (m.precio * c.cantidad) as subtotal 
        FROM carrito c 
        JOIN medicamentos m ON c.medicamento_id = m.id 
        WHERE c.usuario_id = $usuario_id";
$result = $conexion->query($sql);

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras - Farmacia Online</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmacia Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="carrito.php" class="active">Carrito</a></li>
                    <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']): ?>
                        <li><a href="admin/index.php">Panel Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2>Carrito de Compras</h2>
            <p>Revisa y confirma tus productos antes de comprar</p>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="tabla-contenedor">
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th class="columna-nombre">Medicamento</th>
                            <th class="columna-precio">Precio Unitario</th>
                            <th class="columna-cantidad">Cantidad</th>
                            <th class="columna-precio">Subtotal</th>
                            <th class="columna-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td class="texto-derecha">$<?php echo number_format($item['precio'], 2); ?></td>
                                <td class="texto-centro"><?php echo $item['cantidad']; ?></td>
                                <td class="texto-derecha">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="eliminar_carrito.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                            <?php $total += $item['subtotal']; ?>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-fila">
                            <td colspan="3" class="texto-derecha"><strong>Total:</strong></td>
                            <td colspan="2" class="texto-derecha"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="acciones-carrito">
                <a href="index.php" class="btn btn-outline">Seguir Comprando</a>
                <a href="confirmar_compra.php" class="btn btn-confirmar">Confirmar Compra</a>
            </div>
        <?php else: ?>
            <div class="mensaje-vacio">
                <div class="icono-vacio">🛒</div>
                <h3>Tu carrito está vacío</h3>
                <p>Agrega algunos productos para continuar</p>
                <a href="index.php" class="btn btn-primary">Explorar Productos</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>