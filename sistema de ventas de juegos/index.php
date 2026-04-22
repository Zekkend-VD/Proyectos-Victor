<?php
session_start();
include 'config.php';
include 'auth_improved.php';

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
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validar campos vacíos
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        // Usar función mejorada de autenticación
        $auth_result = authenticate_user($email, $password);
        
        if ($auth_result['success']) {
            $_SESSION['user_id'] = $auth_result['user_id'];
            $_SESSION['user_name'] = $auth_result['user_name'];
            $_SESSION['role'] = $auth_result['role'];
            
            if ($auth_result['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: cliente.php');
            }
            exit;
        } else {
            $error = isset($auth_result['error']) ? $auth_result['error'] : 'Error de autenticación';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario PS5</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    🎮
                </div>
                <h1>Bienvenido</h1>
                <p>Sistema de Inventario PS5</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="login-btn">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="login-footer">
                <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <div class="demo-credentials">
                    <small>
                        <strong>Demo:</strong> admin@gmail.com / contraseña1<br>
                        <strong>Cliente:</strong> cliente@mail.com / cliente_password
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>