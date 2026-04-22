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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estado = $_POST['estado'];
    $sql = "UPDATE compras SET estado = '$estado' WHERE id = $compra_id";
    if ($conexion->query($sql)) {
        header('Location: gestion_compras.php?mensaje=Estado actualizado correctamente');
        exit;
    } else {
        $error = "Error al actualizar estado";
    }
}

// Obtener información de la compra
$sql_compra = "SELECT * FROM compras WHERE id = $compra_id";
$result_compra = $conexion->query($sql_compra);
$compra = $result_compra->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Estado - Farmacia Online</title>
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
            <h2>Cambiar Estado de Compra #<?php echo $compra['id']; ?></h2>
            <a href="gestion_compras.php" class="btn">Volver a Compras</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post">
                <div class="campo">
                    <label for="estado">Estado actual: <?php echo ucfirst($compra['estado']); ?></label>
                    <select id="estado" name="estado" required>
                        <option value="pendiente" <?php echo $compra['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="completada" <?php echo $compra['estado'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="cancelada" <?php echo $compra['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                
                <div class="acciones-form">
                    <button type="submit" class="btn btn-confirmar">Actualizar Estado</button>
                    <a href="gestion_compras.php" class="btn">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>