<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_usuarios');
// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_usuarios')) {
    header('Location: ../login.php');
    exit;
}

// Obtener usuarios
$sql = "SELECT * FROM usuarios ORDER BY fecha_registro DESC";
$result = $conexion->query($sql);

// Eliminar usuario si se solicita
if (isset($_GET['eliminar'])) {
    $usuario_id = intval($_GET['eliminar']);
    if ($usuario_id > 0) {
        $sql_eliminar = "DELETE FROM usuarios WHERE id = $usuario_id";
        if ($conexion->query($sql_eliminar)) {
            $mensaje = "Usuario eliminado correctamente";
            header('Location: gestion_usuarios.php?mensaje=' . urlencode($mensaje));
            exit;
        } else {
            $error = "Error al eliminar usuario";
        }
    }
}

// Obtener estadísticas
$sql_estadisticas = "SELECT 
                    COUNT(*) as total_usuarios,
                    COUNT(DISTINCT compras.usuario_id) as usuarios_con_compras
                    FROM usuarios 
                    LEFT JOIN compras ON usuarios.id = compras.usuario_id";
$result_estadisticas = $conexion->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Farmacia Online</title>
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
                    <li><a href="gestion_usuarios.php" class="active">Gestión de Usuarios</a></li>
                    <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión de Usuarios</h2>
            <p>Administra los usuarios registrados en el sistema</p>
        </div>
        
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje-exito"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="estadisticas">
            <div class="tarjeta-estadistica">
                <h3>Total Usuarios</h3>
                <p class="numero"><?php echo $estadisticas['total_usuarios']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Usuarios Activos</h3>
                <p class="numero"><?php echo $estadisticas['usuarios_con_compras']; ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Registros Hoy</h3>
                <p class="numero"><?php 
                    $sql_hoy = "SELECT COUNT(*) as hoy FROM usuarios WHERE DATE(fecha_registro) = CURDATE()";
                    $result_hoy = $conexion->query($sql_hoy);
                    echo $result_hoy->fetch_assoc()['hoy'];
                ?></p>
            </div>
        </div>
        
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th class="columna-id">ID</th>
                        <th class="columna-nombre">Nombre</th>
                        <th class="columna-email">Email</th>
                        <th class="columna-descripcion">Dirección</th>
                        <th class="columna-nombre">Teléfono</th>
                        <th class="columna-fecha">Fecha de Registro</th>
                        <th class="columna-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($usuario = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro"><?php echo $usuario['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td class="columna-descripcion"><?php echo htmlspecialchars($usuario['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                <td class="columna-fecha"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-editar btn-sm">Editar</a>
                                        <a href="gestion_usuarios.php?eliminar=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este usuario?')">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <p>No hay usuarios registrados</p>
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