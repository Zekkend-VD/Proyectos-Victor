<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Simulación: Obtener el último ID
    $clientes = file_exists('clientes.txt') ? file('clientes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $last_id = 0;
    foreach ($clientes as $line) {
        $data = explode('|', $line);
        $last_id = max($last_id, (int)$data[0]);
    }
    $new_id = $last_id + 1;
    $role = 'cliente'; // Rol por defecto

    // Crear la nueva línea de usuario
    $new_user_line = "$new_id|$name|$email|$password|$role\n";

    // Añadir al archivo
    file_put_contents('clientes.txt', $new_user_line, FILE_APPEND);
    $message = 'Cuenta creada con éxito. <a href="index.php">Inicia Sesión</a>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Registro de Cliente</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h1>Registro de Cliente</h1>
    <?php if ($message): ?><p class="success"><?php echo $message; ?></p><?php endif; ?>
    <form method="POST">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Registrar">
    </form>
    <p><a href="index.php">Volver al Login</a></p>
</div>
</body>
</html>