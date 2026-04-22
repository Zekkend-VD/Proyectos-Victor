<?php
session_start();
require 'conexion.php';

// Obtener las enfermedades para el filtro
$sql_enfermedades = "SELECT * FROM enfermedades";
$result_enfermedades = $conexion->query($sql_enfermedades);

// Obtener los medicamentos (si hay un filtro, aplicarlo)
$enfermedad_id = isset($_GET['enfermedad_id']) ? intval($_GET['enfermedad_id']) : 0;
if ($enfermedad_id > 0) {
    $sql_medicamentos = "SELECT * FROM medicamentos WHERE enfermedad_id = $enfermedad_id";
    $sql_enfermedad_info = "SELECT * FROM enfermedades WHERE id = $enfermedad_id";
    $result_enfermedad_info = $conexion->query($sql_enfermedad_info);
    $enfermedad_info = $result_enfermedad_info->fetch_assoc();
} else {
    $sql_medicamentos = "SELECT * FROM medicamentos";
    $enfermedad_info = null;
}
$result_medicamentos = $conexion->query($sql_medicamentos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Farmacia Online - Inicio</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmacia Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Inicio</a></li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li><a href="carrito.php">Carrito</a></li>
                        <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']): ?>
                            <li><a href="admin/index.php">Panel Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Iniciar Sesión</a></li>
                        <li><a href="registro.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <aside class="filtros">
            <h3>Filtrar por enfermedad</h3>
            <ul>
                <li><a href="index.php" class="<?php echo $enfermedad_id == 0 ? 'active' : ''; ?>">Todos los medicamentos</a></li>
                <?php while($enfermedad = $result_enfermedades->fetch_assoc()): ?>
                    <li><a href="index.php?enfermedad_id=<?php echo $enfermedad['id']; ?>" class="<?php echo $enfermedad_id == $enfermedad['id'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($enfermedad['nombre']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <section class="contenido-principal">
            <?php if ($enfermedad_info): ?>
                <div class="info-enfermedad">
                    <h2><?php echo htmlspecialchars($enfermedad_info['nombre']); ?></h2>
                    <div class="detalles-enfermedad">
                        <h3>Descripción</h3>
                        <p><?php echo htmlspecialchars($enfermedad_info['descripcion']); ?></p>
                        
                        <h3>Causas</h3>
                        <p><?php echo htmlspecialchars($enfermedad_info['causas']); ?></p>
                        
                        <h3>Síntomas</h3>
                        <p><?php echo htmlspecialchars($enfermedad_info['sintomas']); ?></p>
                        
                        <h3>Tratamientos</h3>
                        <p><?php echo htmlspecialchars($enfermedad_info['tratamientos']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h2><?php echo $enfermedad_info ? 'Medicamentos para ' . htmlspecialchars($enfermedad_info['nombre']) : 'Todos los medicamentos'; ?></h2>
                <p>Encuentra los mejores productos para tu salud</p>
            </div>
            
            <div class="medicamentos">
                <?php if ($result_medicamentos->num_rows > 0): ?>
                    <?php while($medicamento = $result_medicamentos->fetch_assoc()): ?>
                        <div class="medicamento">
                            <div class="imagen-medicamento">
                                <img src="images/<?php echo $medicamento['imagen']; ?>" alt="<?php echo htmlspecialchars($medicamento['nombre']); ?>">
                            </div>
                            <h3><?php echo htmlspecialchars($medicamento['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($medicamento['descripcion']); ?></p>
                            <p class="precio">$<?php echo number_format($medicamento['precio'], 2); ?></p>
                            <p class="stock">Disponible: <?php echo $medicamento['stock']; ?> unidades</p>
                            
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="medicamento_id" value="<?php echo $medicamento['id']; ?>">
                                    <div class="cantidad">
                                        <label for="cantidad_<?php echo $medicamento['id']; ?>">Cantidad:</label>
                                        <input type="number" id="cantidad_<?php echo $medicamento['id']; ?>" name="cantidad" value="1" min="1" max="<?php echo $medicamento['stock']; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-agregar">Agregar al carrito</button>
                                </form>
                            <?php else: ?>
                                <p class="mensaje-login">Inicia sesión para comprar</p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="mensaje-vacio">
                        <div class="icono-vacio">💊</div>
                        <h3>No hay medicamentos disponibles</h3>
                        <p>Prueba con otro filtro o vuelve más tarde</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>