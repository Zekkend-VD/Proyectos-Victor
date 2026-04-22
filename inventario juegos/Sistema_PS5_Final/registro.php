<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validaciones
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'Por favor, completa todos los campos.';
    } elseif ($password !== $confirm_password) {
        $message = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verificar si el email ya existe
        $clientes = file_exists('clientes.txt') ? file('clientes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $email_exists = false;

        foreach ($clientes as $line) {
            $data = explode('|', $line);
            if (isset($data[2]) && $data[2] === $email) {
                $email_exists = true;
                break;
            }
        }

        if ($email_exists) {
            $message = 'Este email ya está registrado.';
        } else {
            // Obtener el último ID
            $last_id = 0;
            foreach ($clientes as $line) {
                $data = explode('|', $line);
                $last_id = max($last_id, (int)$data[0]);
            }
            $new_id = $last_id + 1;
            $role = 'cliente';

            // Crear la nueva línea de usuario
            $new_user_line = "$new_id|$name|$email|$password|$role\n";

            // Añadir al archivo
            file_put_contents('clientes.txt', $new_user_line, FILE_APPEND);
            $message = '¡Cuenta creada con éxito! <a href="index.php">Inicia Sesión</a>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema PS5</title>
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
                <h1>Crear Cuenta</h1>
                <p>Únete a nuestra comunidad gamer</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'éxito') !== false ? 'alert-success' : 'alert-error'; ?>">
                    <span class="alert-icon"><?php echo strpos($message, 'éxito') !== false ? '✅' : '⚠️'; ?></span>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="input-group">
                    <label for="name">Nombre Completo</label>
                    <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <small>Mínimo 6 caracteres</small>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="login-btn">
                    Crear Cuenta
                </button>
            </form>
            
            <div class="login-footer">
                <p>¿Ya tienes cuenta? <a href="index.php">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>