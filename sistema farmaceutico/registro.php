<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    
    // Verificar si el email ya existe
    $sql_verificar = "SELECT id FROM usuarios WHERE email = '$email'";
    $result_verificar = $conexion->query($sql_verificar);
    
    if ($result_verificar->num_rows > 0) {
        $error = "El email ya está registrado";
    } else {
        $sql = "INSERT INTO usuarios (nombre, email, password, direccion, telefono) 
                VALUES ('$nombre', '$email', '$password', '$direccion', '$telefono')";
        
        if ($conexion->query($sql) === TRUE) {
            $_SESSION['usuario_id'] = $conexion->insert_id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['es_admin'] = false;
            header('Location: index.php');
        } else {
            $error = "Error al registrar: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Farmacia Online</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmacia Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2>Registro de Usuario</h2>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="campo">
                    <label for="nombre">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre completo">
                </div>
                
                <div class="campo">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>
                
                <div class="campo">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Crea una contraseña segura">
                </div>
                
                <div class="campo">
                    <label for="direccion">Dirección:</label>
                    <textarea id="direccion" name="direccion" required placeholder="Tu dirección completa para envíos"></textarea>
                </div>
                
                <div class="campo">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required placeholder="Tu número de teléfono">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>
            
            <p class="texto-centro" style="margin-top: 1.5rem;">
                ¿Ya tienes cuenta? <a href="login.php" style="color: var(--primary); font-weight: 600;">Inicia sesión aquí</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>