<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_usuarios');
// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_usuarios')) {
    header('Location: ../login.php');
    exit;
}

$usuario_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    
    $sql = "UPDATE usuarios SET 
            nombre = '$nombre',
            email = '$email',
            direccion = '$direccion',
            telefono = '$telefono'
            WHERE id = $usuario_id";
    
    if ($conexion->query($sql)) {
        header('Location: gestion_usuarios.php?mensaje=Usuario actualizado correctamente');
        exit;
    } else {
        $error = "Error al actualizar usuario: " . $conexion->error;
    }
}

// Obtener información del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id = $usuario_id";
$result_usuario = $conexion->query($sql_usuario);
$usuario = $result_usuario->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Farmacia Online</title>
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
            <h2>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
            <a href="gestion_usuarios.php" class="btn">Volver a Usuarios</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post">
                <div class="campo">
                    <label for="nombre">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                
                <div class="campo">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                
                <div class="campo">
                    <label for="direccion">Dirección:</label>
                    <textarea id="direccion" name="direccion" required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                </div>
                
                <div class="campo">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                </div>
                
                <div class="acciones-form">
                    <button type="submit" class="btn btn-confirmar">Actualizar Usuario</button>
                    <a href="gestion_usuarios.php" class="btn">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>