<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_enfermedades');

// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_enfermedades')) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $causas = $_POST['causas'];
    $sintomas = $_POST['sintomas'];
    $tratamientos = $_POST['tratamientos'];
    
    $sql = "INSERT INTO enfermedades (nombre, descripcion, causas, sintomas, tratamientos) 
            VALUES ('$nombre', '$descripcion', '$causas', '$sintomas', '$tratamientos')";
    
    if ($conexion->query($sql) === TRUE) {
        $enfermedad_id = $conexion->insert_id;
        
        // Procesar medicamentos si se enviaron
        if (isset($_POST['medicamentos'])) {
            foreach ($_POST['medicamentos'] as $medicamento) {
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
        
        $mensaje = "Enfermedad agregada correctamente";
        header('Location: gestion_enfermedades.php?mensaje=' . urlencode($mensaje));
        exit;
    } else {
        $error = "Error al agregar enfermedad: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Enfermedad - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <script>
        function agregarMedicamento() {
            const contenedor = document.getElementById('medicamentos');
            const indice = contenedor.children.length;
            
            const div = document.createElement('div');
            div.className = 'medicamento-form';
            div.innerHTML = `
                <h4>Medicamento ${indice + 1}</h4>
                <div class="campo">
                    <label>Nombre:</label>
                    <input type="text" name="medicamentos[${indice}][nombre]" required>
                </div>
                <div class="campo">
                    <label>Descripción:</label>
                    <textarea name="medicamentos[${indice}][descripcion]" required></textarea>
                </div>
                <div class="campo">
                    <label>Precio:</label>
                    <input type="number" step="0.01" name="medicamentos[${indice}][precio]" required>
                </div>
                <div class="campo">
                    <label>Stock:</label>
                    <input type="number" name="medicamentos[${indice}][stock]" required>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">Eliminar Medicamento</button>
            `;
            
            contenedor.appendChild(div);
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
            <h2>Agregar Nueva Enfermedad</h2>
            <a href="gestion_enfermedades.php" class="btn btn-outline">Volver a Enfermedades</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" class="form-enfermedad">
            <div class="campo">
                <label for="nombre">Nombre de la enfermedad:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej: Tos, Fiebre, Dolor de cabeza">
            </div>
            
            <div class="campo">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required placeholder="Describe la enfermedad..."></textarea>
            </div>
            
            <div class="campo">
                <label for="causas">Causas:</label>
                <textarea id="causas" name="causas" required placeholder="Principales causas de la enfermedad..."></textarea>
            </div>
            
            <div class="campo">
                <label for="sintomas">Síntomas:</label>
                <textarea id="sintomas" name="sintomas" required placeholder="Síntomas comunes..."></textarea>
            </div>
            
            <div class="campo">
                <label for="tratamientos">Tratamientos:</label>
                <textarea id="tratamientos" name="tratamientos" required placeholder="Tratamientos recomendados..."></textarea>
            </div>
            
            <h3>Medicamentos para esta enfermedad</h3>
            <div id="medicamentos">
                <!-- Los medicamentos se agregarán aquí dinámicamente -->
            </div>
            
            <div class="acciones" style="margin-top: 1rem;">
                <button type="button" onclick="agregarMedicamento()" class="btn btn-success">Agregar Medicamento</button>
            </div>
            
            <div class="acciones-form">
                <button type="submit" class="btn btn-confirmar">Guardar Enfermedad</button>
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