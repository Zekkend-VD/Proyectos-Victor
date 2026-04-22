<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_enfermedades');

// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_enfermedades')) {
    header('Location: ../login.php');
    exit;
}

$enfermedad_id = intval($_GET['id']);

// Obtener información de la enfermedad
$sql_enfermedad = "SELECT * FROM enfermedades WHERE id = $enfermedad_id";
$result_enfermedad = $conexion->query($sql_enfermedad);

if ($result_enfermedad->num_rows === 0) {
    header('Location: gestion_enfermedades.php?error=Enfermedad no encontrada');
    exit;
}

$enfermedad = $result_enfermedad->fetch_assoc();

// Obtener medicamentos de esta enfermedad
$sql_medicamentos = "SELECT * FROM medicamentos WHERE enfermedad_id = $enfermedad_id";
$result_medicamentos = $conexion->query($sql_medicamentos);

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $causas = $_POST['causas'];
    $sintomas = $_POST['sintomas'];
    $tratamientos = $_POST['tratamientos'];
    
    // Actualizar la enfermedad
    $sql = "UPDATE enfermedades SET 
            nombre = '$nombre',
            descripcion = '$descripcion',
            causas = '$causas',
            sintomas = '$sintomas',
            tratamientos = '$tratamientos'
            WHERE id = $enfermedad_id";
    
    if ($conexion->query($sql) === TRUE) {
        // Procesar medicamentos existentes
        if (isset($_POST['medicamentos_existentes'])) {
            foreach ($_POST['medicamentos_existentes'] as $medicamento_id => $medicamento) {
                $nombre_med = $medicamento['nombre'];
                $descripcion_med = $medicamento['descripcion'];
                $precio = $medicamento['precio'];
                $stock = $medicamento['stock'];
                
                $sql_med = "UPDATE medicamentos SET 
                           nombre = '$nombre_med',
                           descripcion = '$descripcion_med',
                           precio = $precio,
                           stock = $stock
                           WHERE id = $medicamento_id AND enfermedad_id = $enfermedad_id";
                $conexion->query($sql_med);
            }
        }
        
        // Procesar nuevos medicamentos
        if (isset($_POST['nuevos_medicamentos'])) {
            foreach ($_POST['nuevos_medicamentos'] as $medicamento) {
                if (!empty($medicamento['nombre'])) {
                    $nombre_med = $medicamento['nombre'];
                    $descripcion_med = $medicamento['descripcion'];
                    $precio = $medicamento['precio'];
                    $stock = $medicamento['stock'];
                    
                    $sql_med = "INSERT INTO medicamentos (nombre, descripcion, precio, stock, enfermedad_id) 
                               VALUES ('$nombre_med', '$descripcion_med', $precio, $stock, $enfermedad_id)";
                    $conexion->query($sql_med);
                }
            }
        }
        
        $mensaje = "Enfermedad actualizada correctamente";
        header('Location: gestion_enfermedades.php?mensaje=' . urlencode($mensaje));
        exit;
    } else {
        $error = "Error al actualizar enfermedad: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Enfermedad - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <script>
        function agregarMedicamento() {
            const contenedor = document.getElementById('nuevos-medicamentos');
            const indice = contenedor.children.length;
            
            const div = document.createElement('div');
            div.className = 'medicamento-form';
            div.innerHTML = `
                <h4>Nuevo Medicamento</h4>
                <div class="campo">
                    <label>Nombre:</label>
                    <input type="text" name="nuevos_medicamentos[${indice}][nombre]" required>
                </div>
                <div class="campo">
                    <label>Descripción:</label>
                    <textarea name="nuevos_medicamentos[${indice}][descripcion]" required></textarea>
                </div>
                <div class="campo">
                    <label>Precio:</label>
                    <input type="number" step="0.01" name="nuevos_medicamentos[${indice}][precio]" required>
                </div>
                <div class="campo">
                    <label>Stock:</label>
                    <input type="number" name="nuevos_medicamentos[${indice}][stock]" required>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">Eliminar Medicamento</button>
            `;
            
            contenedor.appendChild(div);
        }

        function eliminarMedicamento(medicamento_id) {
            if (confirm('¿Está seguro de eliminar este medicamento?')) {
                window.location.href = 'eliminar_medicamento.php?id=' + medicamento_id + '&enfermedad_id=<?php echo $enfermedad_id; ?>';
            }
        }
    </script>
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
            <h2>Editar Enfermedad: <?php echo htmlspecialchars($enfermedad['nombre']); ?></h2>
            <a href="gestion_enfermedades.php" class="btn btn-outline">Volver a Enfermedades</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" class="form-enfermedad">
            <div class="campo">
                <label for="nombre">Nombre de la enfermedad:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($enfermedad['nombre']); ?>" required>
            </div>
            
            <div class="campo">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($enfermedad['descripcion']); ?></textarea>
            </div>
            
            <div class="campo">
                <label for="causas">Causas:</label>
                <textarea id="causas" name="causas" required><?php echo htmlspecialchars($enfermedad['causas']); ?></textarea>
            </div>
            
            <div class="campo">
                <label for="sintomas">Síntomas:</label>
                <textarea id="sintomas" name="sintomas" required><?php echo htmlspecialchars($enfermedad['sintomas']); ?></textarea>
            </div>
            
            <div class="campo">
                <label for="tratamientos">Tratamientos:</label>
                <textarea id="tratamientos" name="tratamientos" required><?php echo htmlspecialchars($enfermedad['tratamientos']); ?></textarea>
            </div>
            
            <h3>Medicamentos Existentes</h3>
            <div id="medicamentos-existentes">
                <?php if ($result_medicamentos->num_rows > 0): ?>
                    <?php while($medicamento = $result_medicamentos->fetch_assoc()): ?>
                        <div class="medicamento-form">
                            <h4>Medicamento: <?php echo htmlspecialchars($medicamento['nombre']); ?></h4>
                            <div class="campo">
                                <label>Nombre:</label>
                                <input type="text" name="medicamentos_existentes[<?php echo $medicamento['id']; ?>][nombre]" value="<?php echo htmlspecialchars($medicamento['nombre']); ?>" required>
                            </div>
                            <div class="campo">
                                <label>Descripción:</label>
                                <textarea name="medicamentos_existentes[<?php echo $medicamento['id']; ?>][descripcion]" required><?php echo htmlspecialchars($medicamento['descripcion']); ?></textarea>
                            </div>
                            <div class="campo">
                                <label>Precio:</label>
                                <input type="number" step="0.01" name="medicamentos_existentes[<?php echo $medicamento['id']; ?>][precio]" value="<?php echo $medicamento['precio']; ?>" required>
                            </div>
                            <div class="campo">
                                <label>Stock:</label>
                                <input type="number" name="medicamentos_existentes[<?php echo $medicamento['id']; ?>][stock]" value="<?php echo $medicamento['stock']; ?>" required>
                            </div>
                            <button type="button" onclick="eliminarMedicamento(<?php echo $medicamento['id']; ?>)" class="btn btn-danger btn-sm">Eliminar Medicamento</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="mensaje-vacio" style="padding: 1rem; margin: 1rem 0;">
                        <p>No hay medicamentos para esta enfermedad</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <h3>Agregar Nuevos Medicamentos</h3>
            <div id="nuevos-medicamentos">
                <!-- Los nuevos medicamentos se agregarán aquí dinámicamente -->
            </div>
            
            <div class="acciones" style="margin-top: 1rem;">
                <button type="button" onclick="agregarMedicamento()" class="btn btn-success">Agregar Nuevo Medicamento</button>
            </div>
            
            <div class="acciones-form">
                <button type="submit" class="btn btn-confirmar">Actualizar Enfermedad</button>
                <a href="gestion_enfermedades.php" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>