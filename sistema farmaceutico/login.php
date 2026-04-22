<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Verificar roles especiales
    $usuarios_especiales = [
        'admin@farmacia.com' => [
            'password' => 'victor1234',
            'rol' => 'admin',
            'nombre' => 'Administrador'
        ],
        'farmaceutico@farmacia.com' => [
            'password' => 'farma1234',
            'rol' => 'farmaceutico',
            'nombre' => 'Farmacéutico'
        ],
        'almacenista@farmacia.com' => [
            'password' => 'almacen1234',
            'rol' => 'almacenista',
            'nombre' => 'Almacenista'
        ],
        'asistente@farmacia.com' => [
            'password' => 'asistente1234',
            'rol' => 'asistente',
            'nombre' => 'Asistente de Farmacia'
        ],
        'basedatos@farmacia.com' => [
            'password' => 'bd1234',
            'rol' => 'encargado_base_datos',
            'nombre' => 'Encargado de Base de Datos'
        ],
        'proveedor@farmacia.com' => [
            'password' => 'proveedor1234',
            'rol' => 'proveedor',
            'nombre' => 'Proveedor Principal'
        ]
];
    
    // Verificar si es un usuario especial
    if (array_key_exists($email, $usuarios_especiales)) {
        $usuario_especial = $usuarios_especiales[$email];
        if ($password === $usuario_especial['password']) {
            $_SESSION['usuario_id'] = 0;
            $_SESSION['usuario_nombre'] = $usuario_especial['nombre'];
            $_SESSION['rol'] = $usuario_especial['rol'];
            $_SESSION['es_admin'] = ($usuario_especial['rol'] === 'admin');
            header('Location: admin/index.php');
            exit;
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        // Verificar usuario normal
        $sql = "SELECT * FROM usuarios WHERE email = '$email'";
        $result = $conexion->query($sql);
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = 'usuario';
                $_SESSION['es_admin'] = false;
                header('Location: index.php');
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Farmacia Online</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmacia Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="campo">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>
                
                <div class="campo">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Tu contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
            </form>
            
            <p class="texto-centro" style="margin-top: 1.5rem;">
                ¿No tienes cuenta? <a href="registro.php" style="color: var(--primary); font-weight: 600;">Regístrate aquí</a>
            </p>

            <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; text-align: center;">
                <p style="margin: 0; font-size: 0.9rem; color: #666;">
                    <strong>Credenciales de prueba:</strong><br>
                    Admin: admin@farmacia.com / victor1234<br>
                    Farmacéutico: farmaceutico@farmacia.com / farma1234<br>
                    Almacenista: almacenista@farmacia.com / almacen1234<br>
                    Asistente: asistente@farmacia.com / asistente1234<br>
                    Encargado BD: basedatos@farmacia.com / bd1234
                </p>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>