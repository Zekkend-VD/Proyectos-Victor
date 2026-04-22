<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_inventario');
// Verificar permisos para gestión de inventario
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_inventario')) {
    header('Location: ../login.php');
    exit;
}

// Procesar actualización de stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_stock'])) {
    $medicamento_id = intval($_POST['medicamento_id']);
    $nuevo_stock = intval($_POST['stock']);
    
    $sql = "UPDATE medicamentos SET stock = $nuevo_stock WHERE id = $medicamento_id";
    if ($conexion->query($sql)) {
        $mensaje = "Stock actualizado correctamente";
    } else {
        $error = "Error al actualizar stock: " . $conexion->error;
    }
}

// Obtener medicamentos para el inventario
$sql = "SELECT m.*, e.nombre as enfermedad_nombre 
        FROM medicamentos m 
        LEFT JOIN enfermedades e ON m.enfermedad_id = e.id 
        ORDER BY m.stock ASC";
$result = $conexion->query($sql);

// Estadísticas de inventario
$sql_estadisticas = "SELECT 
                    COUNT(*) as total_medicamentos,
                    SUM(stock) as total_stock,
                    COUNT(CASE WHEN stock < 10 THEN 1 END) as stock_bajo,
                    COUNT(CASE WHEN stock = 0 THEN 1 END) as stock_agotado
                    FROM medicamentos";
$result_estadisticas = $conexion->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (tiene_permiso('gestion_enfermedades')): ?>
                        <li><a href="gestion_enfermedades.php">Gestión de Enfermedades</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_usuarios')): ?>
                        <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_compras')): ?>
                        <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_inventario')): ?>
                        <li><a href="gestion_inventario.php" class="active">Gestión de Inventario</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión de Inventario</h2>
            <p>Controla el stock y disponibilidad de medicamentos</p>
        </div>
        
        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="estadisticas">
            <div class="tarjeta-estadistica">
                <h3>Total Medicamentos</h3>
                <p class="numero"><?php echo $estadisticas['total_medicamentos']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Stock Total</h3>
                <p class="numero"><?php echo $estadisticas['total_stock']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Stock Bajo</h3>
                <p class="numero" style="color: var(--warning);"><?php echo $estadisticas['stock_bajo']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Agotados</h3>
                <p class="numero" style="color: var(--danger);"><?php echo $estadisticas['stock_agotado']; ?></p>
            </div>
        </div>
        
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th class="columna-id">ID</th>
                        <th class="columna-nombre">Medicamento</th>
                        <th class="columna-descripcion">Descripción</th>
                        <th class="columna-nombre">Enfermedad</th>
                        <th class="columna-precio">Precio</th>
                        <th class="columna-cantidad">Stock</th>
                        <th class="columna-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($medicamento = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro"><?php echo $medicamento['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($medicamento['nombre']); ?></strong>
                                </td>
                                <td class="columna-descripcion">
                                    <?php 
                                    $descripcion = htmlspecialchars($medicamento['descripcion']);
                                    echo strlen($descripcion) > 100 ? substr($descripcion, 0, 100) . '...' : $descripcion;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($medicamento['enfermedad_nombre'] ?? 'No asignada'); ?></td>
                                <td class="texto-derecha">$<?php echo number_format($medicamento['precio'], 2); ?></td>
                                <td class="texto-centro">
                                    <span class="estado <?php 
                                        echo $medicamento['stock'] == 0 ? 'cancelada' : 
                                             ($medicamento['stock'] < 10 ? 'pendiente' : 'completada'); 
                                    ?>">
                                        <?php echo $medicamento['stock']; ?> unidades
                                    </span>
                                </td>
                                <td>
                                    <div class="acciones-tabla">
                                        <button type="button" onclick="mostrarFormularioStock(<?php echo $medicamento['id']; ?>, <?php echo $medicamento['stock']; ?>)" 
                                                class="btn btn-editar btn-sm">
                                            Actualizar Stock
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <p>No hay medicamentos en el inventario</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Formulario flotante para actualizar stock -->
        <div id="formulario-stock" class="formulario-flotante" style="display: none;">
            <div class="form-container">
                <h3>Actualizar Stock</h3>
                <form method="post">
                    <input type="hidden" name="medicamento_id" id="medicamento_id">
                    <div class="campo">
                        <label for="stock">Nuevo Stock:</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>
                    <div class="acciones-form">
                        <button type="submit" name="actualizar_stock" class="btn btn-confirmar">Actualizar</button>
                        <button type="button" onclick="ocultarFormularioStock()" class="btn btn-outline">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        function mostrarFormularioStock(medicamentoId, stockActual) {
            document.getElementById('medicamento_id').value = medicamentoId;
            document.getElementById('stock').value = stockActual;
            document.getElementById('formulario-stock').style.display = 'block';
        }
        
        function ocultarFormularioStock() {
            document.getElementById('formulario-stock').style.display = 'none';
        }
        
        // Cerrar formulario al hacer clic fuera
        document.getElementById('formulario-stock').addEventListener('click', function(e) {
            if (e.target === this) {
                ocultarFormularioStock();
            }
        });
    </script>
</body>
</html>