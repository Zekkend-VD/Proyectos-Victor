<?php
session_start();
require '../conexion.php';

// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_enfermedades')) {
    header('Location: ../login.php');
    exit;
}

// Obtener enfermedades
$sql = "SELECT * FROM enfermedades";
$result = $conexion->query($sql);

// Contar medicamentos por enfermedad
$sql_medicamentos = "SELECT enfermedad_id, COUNT(*) as total FROM medicamentos GROUP BY enfermedad_id";
$result_medicamentos = $conexion->query($sql_medicamentos);
$medicamentos_por_enfermedad = [];
while($row = $result_medicamentos->fetch_assoc()) {
    $medicamentos_por_enfermedad[$row['enfermedad_id']] = $row['total'];
}

// Procesar mensajes
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Enfermedades - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="gestion_enfermedades.php" class="active">Gestión de Enfermedades</a></li>
                    <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                    <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión de Enfermedades</h2>
            <p>Administra las enfermedades y sus medicamentos</p>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="estadisticas">
            <div class="tarjeta-estadistica">
                <h3>Total Enfermedades</h3>
                <p class="numero"><?php echo $result->num_rows; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Total Medicamentos</h3>
                <p class="numero"><?php echo array_sum($medicamentos_por_enfermedad); ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Promedio por Enfermedad</h3>
                <p class="numero"><?php echo $result->num_rows > 0 ? round(array_sum($medicamentos_por_enfermedad) / $result->num_rows, 1) : 0; ?></p>
            </div>
        </div>
        
        <div class="acciones">
            <a href="agregar_enfermedad.php" class="btn btn-primary">➕ Agregar Nueva Enfermedad</a>
        </div>
        
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th class="columna-id">ID</th>
                        <th class="columna-nombre">Nombre</th>
                        <th class="columna-descripcion">Descripción</th>
                        <th class="columna-cantidad">Medicamentos</th>
                        <th class="columna-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($enfermedad = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro"><?php echo $enfermedad['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($enfermedad['nombre']); ?></strong>
                                </td>
                                <td class="columna-descripcion">
                                    <?php 
                                    $descripcion = htmlspecialchars($enfermedad['descripcion']);
                                    echo strlen($descripcion) > 100 ? substr($descripcion, 0, 100) . '...' : $descripcion;
                                    ?>
                                </td>
                                <td class="texto-centro">
                                    <span class="badge"><?php echo $medicamentos_por_enfermedad[$enfermedad['id']] ?? 0; ?> medicamentos</span>
                                </td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="editar_enfermedad.php?id=<?php echo $enfermedad['id']; ?>" class="btn btn-editar btn-sm">✏️ Editar</a>
                                        <a href="eliminar_enfermedad.php?id=<?php echo $enfermedad['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta enfermedad y todos sus medicamentos?')">🗑️ Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <div class="icono-vacio">💊</div>
                                    <h3>No hay enfermedades registradas</h3>
                                    <p>Comienza agregando la primera enfermedad al sistema</p>
                                    <a href="agregar_enfermedad.php" class="btn btn-primary">Agregar Primera Enfermedad</a>
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