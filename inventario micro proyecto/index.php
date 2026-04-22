<?php
session_start();
include 'config.php'; // Incluye el archivo de configuración con credenciales

// Si ya está logueado, redirige según el rol
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: cliente.php');
    }
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Verificar si es el administrador (usando config.php)
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['user_id'] = 1; // ID fijo para el admin
        $_SESSION['user_name'] = 'Administrador';
        $_SESSION['role'] = 'admin';
        header('Location: admin.php');
        exit;
    }

    // 2. Verificar clientes regulares (usando clientes.txt)
    $clientes = file('clientes.txt', FILE_IGNORE_NEW_LINES);
    $authenticated = false;

    foreach ($clientes as $line) {
        $data = explode('|', $line);
        // ID|Nombre|Email|Password|Rol
        if (isset($data[2]) && isset($data[3]) && $data[2] === $email && $data[3] === $password) { 
            $_SESSION['user_id'] = $data[0];
            $_SESSION['user_name'] = $data[1];
            $_SESSION['role'] = $data[4];
            $authenticated = true;
            header('Location: cliente.php');
            exit;
        }
    }

    if (!$authenticated) {
        $error = 'Email o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Login - Sistema de Inventario</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h1>Iniciar Sesión</h1>
    <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Ingresar">
    </form>
    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a> (Clientes)</p>
</div>
</body>
</html>