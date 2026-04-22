<?php
session_start();

// --- Funciones de Manejo de Usuarios (DB Simulado) ---
function leer_usuarios() {
    $usuarios = [];
    $filename_clientes = 'usuarios.txt';
    $admin_config = @require 'admin_config.php'; 

    // 1. Cargar la cuenta del administrador desde PHP
    if (is_array($admin_config) && isset($admin_config['email'])) {
        $email = trim($admin_config['email']);
        $hash = trim($admin_config['hash']);
        $rol = trim($admin_config['rol']);
        $usuarios[$email] = ['hash' => $hash, 'rol' => $rol];
    }
    
    // 2. Cargar las cuentas de clientes desde TXT
    if (file_exists($filename_clientes)) {
        $file = fopen($filename_clientes, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if (empty($line)) continue;
                
                @list($email, $hash, $rol) = explode('|', $line); 
                
                if ($email && $hash && $rol) { 
                    $usuarios[trim($email)] = ['hash' => trim($hash), 'rol' => trim($rol)];
                }
            }
            fclose($file);
        }
    }
    return $usuarios;
}

function guardar_usuario($email, $password, $rol = 'cliente') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($rol !== 'admin') {
        $data = "$email|$hash|$rol\n";
        
        // Asegurar que el archivo existe
        if (!file_exists('usuarios.txt')) {
            file_put_contents('usuarios.txt', '');
        }
        
        file_put_contents('usuarios.txt', $data, FILE_APPEND | LOCK_EX);
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $error = "Por favor, complete todos los campos.";
        } else {
            $usuarios = leer_usuarios();

            if (isset($usuarios[$email]) && password_verify($password, $usuarios[$email]['hash'])) {
                $_SESSION['user_email'] = $email;
                $_SESSION['user_rol'] = $usuarios[$email]['rol'];
                
                if ($usuarios[$email]['rol'] === 'admin') {
                    header('Location: admin_panel.php');
                } else {
                    header('Location: cliente.php');
                }
                exit;
            } else {
                $error = "Email o contraseña incorrectos.";
            }
        }
    } elseif (isset($_POST['registro'])) {
        $email = trim($_POST['reg_email']);
        $password = $_POST['reg_password'];
        
        if (empty($email) || empty($password)) {
            $error = "Por favor, complete todos los campos.";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            $usuarios = leer_usuarios();

            if (isset($usuarios[$email])) {
                $error = "El email ya está registrado.";
            } else {
                guardar_usuario($email, $password, 'cliente');
                $_SESSION['user_email'] = $email;
                $_SESSION['user_rol'] = 'cliente';
                $success = "Cuenta creada exitosamente. Redirigiendo...";
                echo "<script>setTimeout(function(){ window.location.href = 'cliente.php'; }, 2000);</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Perfumes - Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .perfume-icon {
            font-size: 2rem;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 10px;
        }
        
        .hero-section {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.8), rgba(248, 250, 255, 0.6));
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-top: 15px;
            line-height: 1.8;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .feature {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <h1>
                <span class="perfume-icon">🌸</span>
                Sistema de Perfumes Exclusivos
                <span class="perfume-icon">✨</span>
            </h1>
            <p>Descubre fragancias únicas y sofisticadas para cada ocasión especial</p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🥀</div>
                    <h4>Fragancias Premium</h4>
                    <p>Las mejores marcas del mundo</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💎</div>
                    <h4>Calidad Garantizada</h4>
                    <p>Perfumes 100% originales</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🚚</div>
                    <h4>Envío Rápido</h4>
                    <p>Entrega segura y discreta</p>
                </div>
            </div>
        </div>
        
        <?php if ($error): ?>
            <p class='error'><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class='mensaje-alerta'><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <div class="form-section">
            <h2>🔐 Iniciar Sesión</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="📧 Email" required>
                <input type="password" name="password" placeholder="🔒 Contraseña" required>
                <button type="submit" name="login">✨ Entrar al Catálogo</button>
            </form>
        </div>

        <div class="form-section">
            <h2>👤 Crear Cuenta Nueva</h2>
            <form method="POST">
                <input type="email" name="reg_email" placeholder="📧 Email" required>
                <input type="password" name="reg_password" placeholder="🔒 Contraseña (mín. 6 caracteres)" required>
                <button type="submit" name="registro">🌟 Registrarse</button>
            </form>
        </div>
        
        <div class="admin-link">
            <p><strong>👨‍💼 Administrador:</strong> admin@empresa.com | <strong>🔑 Contraseña:</strong> password</p>
        </div>
    </div>
</body>
</html>