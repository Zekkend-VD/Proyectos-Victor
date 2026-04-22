<?php
session_start();
require '../conexion.php';

if (!isset($_SESSION['rol'])) {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?> - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Inicio</a></li>
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
                        <li><a href="gestion_inventario.php">Gestión de Inventario</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('estadisticas')): ?>
                        <li><a href="estadisticas.php">Gestión de estadisticas</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('respaldo_bd')): ?>
                        <li><a href="respaldo_bd.php">Respaldo BD</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('solicitar_productos')): ?>
                        <li><a href="solicitar_productos.php">Solicitar Productos</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_pedidos_proveedor')): ?>
                        <li><a href="gestion_pedidos.php">Gestión de Pedidos</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_finanzas')): ?>
                        <li><a href="gestion_finanzas.php">Gestión Financiera</a></li>
                    <?php endif; ?>
                        <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Bienvenido, <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h2>
            <p>Gestiona los aspectos de tu farmacia online desde aquí</p>
        </div>
        
        <div class="dashboard">
            <?php if (tiene_permiso('gestion_enfermedades')): ?>
            <div class="card">
                <h3>Gestión de Enfermedades</h3>
                <p>Agrega, edita y elimina enfermedades y sus medicamentos asociados. Mantén actualizado el catálogo de productos.</p>
                <a href="gestion_enfermedades.php" class="btn btn-primary">Ir a Gestión</a>
            </div>
            <?php endif; ?>
            
            <?php if (tiene_permiso('gestion_usuarios')): ?>
            <div class="card">
                <h3>Gestión de Usuarios</h3>
                <p>Administra los usuarios registrados, verifica información y gestiona permisos del sistema.</p>
                <a href="gestion_usuarios.php" class="btn btn-primary">Ir a Gestión</a>
            </div>
            <?php endif; ?>
            
            <?php if (tiene_permiso('gestion_compras')): ?>
            <div class="card">
                <h3>Gestión de Compras</h3>
                <p>Revisa y gestiona todas las compras realizadas por los usuarios. Cambia estados y verifica detalles.</p>
                <a href="gestion_compras.php" class="btn btn-primary">Ir a Gestión</a>
            </div>
            <?php endif; ?>
            
            <?php if (tiene_permiso('gestion_inventario')): ?>
            <div class="card">
                <h3>Gestión de Inventario</h3>
                <p>Controla el stock de medicamentos, actualiza cantidades y monitorea productos con stock bajo.</p>
                <a href="gestion_inventario.php" class="btn btn-primary">Ir a Gestión</a>
            </div>
            <?php endif; ?>

            <?php if (tiene_permiso('estadisticas')): ?>
            <div class="card">
                <h3>Estadísticas</h3>
                <p>Visualiza métricas importantes, gráficos de ventas y análisis de rendimiento del sistema.</p>
                <a href="estadisticas.php" class="btn btn-primary">Ver Estadísticas</a>
            </div>
            <?php endif; ?>
            </div>
            <?php if (tiene_permiso('respaldo_bd')): ?>
            <div class="card">
                <h3>Respaldo de Base de Datos</h3>
                <p>Realiza respaldos completos de la base de datos y gestiona los archivos de respaldo existentes.</p>
                <a href="respaldo_bd.php" class="btn btn-primary">Ir a Respaldos</a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('solicitar_productos')): ?>
            <div class="card">
                <h3>Solicitar Productos</h3>
                <p>Solicita productos al proveedor para reponer el inventario.</p>
                <a href="solicitar_productos.php" class="btn btn-primary">Hacer Solicitud</a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('gestion_pedidos_proveedor')): ?>
            <div class="card">
                <h3>Gestión de Pedidos</h3>
                <p>Revisa y gestiona los pedidos de productos de los almacenistas.</p>
                <a href="gestion_pedidos.php" class="btn btn-primary">Ver Pedidos</a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('gestion_finanzas')): ?>
            <div class="card">
                <h3>Gestión Financiera</h3>
                <p>Controla los ingresos, gastos y realiza pagos a trabajadores.</p>
                <a href="gestion_finanzas.php" class="btn btn-primary">Ir a Finanzas</a>
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