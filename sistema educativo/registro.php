<?php
session_start();

// Verificar si ya hay 2 administradores
$admin_count = 0;
if (file_exists('data/admins.txt')) {
    $admins = file('data/admins.txt', FILE_IGNORE_NEW_LINES);
    $admin_count = count($admins);
}

$admin_limit_reached = $admin_count >= 2;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - EduFuturo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #f39c12;
            --light: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--secondary);
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }

        .role-selection {
            margin-bottom: 25px;
        }

        .role-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .role-option {
            position: relative;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: #fafafa;
        }

        .role-label i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--secondary);
        }

        .role-label span {
            font-weight: 600;
            color: var(--primary);
        }

        .role-option input[type="radio"]:checked + .role-label {
            border-color: var(--secondary);
            background: rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
        }

        .admin-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .admin-disabled .role-label {
            cursor: not-allowed;
        }

        .admin-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: var(--primary);
            transform: translateX(-5px);
        }

        .specialty-field {
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            .role-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>
            <i class="fas fa-user-plus"></i>
            Registro de Usuario
        </h2>
        
        <?php if ($admin_limit_reached): ?>
        <div class="admin-notice">
            <i class="fas fa-info-circle"></i>
            El límite de administradores ha sido alcanzado. Solo se permiten 2 administradores.
        </div>
        <?php endif; ?>
        
        <form action="procesar_registro.php" method="POST">
            <!-- Selección de tipo de usuario -->
            <div class="role-selection">
                <label><i class="fas fa-users"></i> Tipo de usuario:</label>
                <div class="role-options">
                    <div class="role-option <?php echo $admin_limit_reached ? 'admin-disabled' : ''; ?>">
                        <input type="radio" name="tipo" value="admin" id="admin" <?php echo $admin_limit_reached ? 'disabled' : ''; ?> required>
                        <label for="admin" class="role-label">
                            <i class="fas fa-user-shield"></i>
                            <span>Administrador</span>
                        </label>
                    </div>
                    
                    <div class="role-option">
                        <input type="radio" name="tipo" value="profesor" id="profesor" required>
                        <label for="profesor" class="role-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Profesor</span>
                        </label>
                    </div>
                    
                    <div class="role-option">
                        <input type="radio" name="tipo" value="estudiante" id="estudiante" required>
                        <label for="estudiante" class="role-label">
                            <i class="fas fa-user-graduate"></i>
                            <span>Estudiante</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nombre completo:</label>
                <input type="text" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Sexo:</label>
                <select name="sexo" required>
                    <option value="">Seleccionar</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-at"></i> Usuario:</label>
                <input type="text" name="usuario" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group specialty-field" id="specialtyField">
                <label><i class="fas fa-book"></i> Especialidad:</label>
                <input type="text" name="especialidad" placeholder="Ej: Matemáticas, Ciencias...">
            </div>
            
            <button type="submit">
                <i class="fas fa-user-plus"></i>
                Completar Registro
            </button>
        </form>
        
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
        </div>
    </div>

    <script>
        // Mostrar/ocultar campo de especialidad según el tipo de usuario
        document.querySelectorAll('input[name="tipo"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const specialtyField = document.getElementById('specialtyField');
                const specialtyInput = specialtyField.querySelector('input[name="especialidad"]');
                
                if (this.value === 'profesor') {
                    specialtyField.style.display = 'block';
                    specialtyInput.required = true;
                } else {
                    specialtyField.style.display = 'none';
                    specialtyInput.required = false;
                    specialtyInput.value = '';
                }
            });
        });

        // Verificar el estado inicial
        const checkedRadio = document.querySelector('input[name="tipo"]:checked');
        if (checkedRadio && checkedRadio.value === 'profesor') {
            document.getElementById('specialtyField').style.display = 'block';
            document.querySelector('input[name="especialidad"]').required = true;
        }
    </script>
</body>
</html>