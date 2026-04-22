<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$tipo = $_SESSION['tipo'];
$nombre = $_SESSION['nombre'];

// Cargar datos según el tipo de usuario
$estudiantes = [];
$profesores = [];
$tareas = [];
$horario = [];

// DEBUG: Verificar archivos
$debug_info = "";
$debug_info .= "Archivo estudiantes: " . (file_exists('data/estudiantes.txt') ? "EXISTE" : "NO EXISTE") . "<br>";
$debug_info .= "Archivo profesores: " . (file_exists('data/profesores.txt') ? "EXISTE" : "NO EXISTE") . "<br>";

if (file_exists('data/estudiantes.txt')) {
    $estudiantes = file('data/estudiantes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $debug_info .= "Estudiantes cargados: " . count($estudiantes) . "<br>";
}

if (file_exists('data/profesores.txt')) {
    $profesores = file('data/profesores.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $debug_info .= "Profesores cargados: " . count($profesores) . "<br>";
    
    // DEBUG: Mostrar contenido de profesores
    foreach ($profesores as $index => $profesor) {
        $debug_info .= "Profesor $index: $profesor<br>";
    }
}

if (file_exists('data/tareas.txt')) {
    $tareas = file('data/tareas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// Cargar horario académico
if (file_exists('data/horario.txt')) {
    $horario_data = file('data/horario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($horario_data as $linea) {
        $datos = explode('|', $linea);
        if (count($datos) >= 2) {
            $horario[] = $datos;
        }
    }
} else {
    // Horario por defecto
    $horario = [
        ['15 Oct', 'Examen de Matemáticas'],
        ['20 Oct', 'Entrega Proyecto Ciencias'],
        ['25 Oct', 'Evaluación de Historia'],
        ['30 Oct', 'Examen de Lengua']
    ];
}

// Obtener materias únicas de los profesores
$materias_unicas = [];
foreach ($profesores as $profesor) {
    $datos = explode('|', $profesor);
    if (count($datos) >= 5 && !empty($datos[4])) {
        $materia = trim($datos[4]);
        if (!empty($materia)) {
            $materias_unicas[$materia] = true;
        }
    }
}
$materias_unicas = array_keys($materias_unicas);

// DEBUG: Mostrar materias encontradas
$debug_info .= "Materias únicas: " . implode(', ', $materias_unicas) . "<br>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduFuturo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #f39c12;
            --success: #27ae60;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            color: #333;
            min-height: 100vh;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .btn {
            background: var(--accent);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-success {
            background: var(--success);
        }

        .btn-success:hover {
            background: #219a52;
        }

        .dashboard-container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            text-align: center;
        }

        .welcome-section h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        .role-badge {
            background: linear-gradient(135deg, var(--accent), #e67e22);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 25px;
            transition: var(--transition);
            border: 1px solid #e9ecef;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h3 i {
            color: var(--accent);
        }

        .list-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-info-small {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-icon {
            width: 35px;
            height: 35px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: var(--primary);
        }

        /* Modal para tareas */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary);
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .tarea-item {
            background: var(--light);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 4px solid var(--accent);
        }

        .tarea-info {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .tarea-title {
            font-weight: 600;
            color: var(--primary);
        }

        .tarea-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .tarea-desc {
            color: #495057;
            line-height: 1.4;
        }

        .horario-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .horario-item:last-child {
            border-bottom: none;
        }

        .horario-fecha {
            font-weight: 600;
            color: var(--primary);
        }

        .horario-evento {
            color: #495057;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        .debug-info {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none; /* Ocultar en producción */
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="header-content">
            <h1><i class="fas fa-graduation-cap"></i> EduFuturo - Dashboard</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div>Bienvenido, <strong><?php echo $nombre; ?></strong></div>
                    <div style="font-size: 0.9rem; opacity: 0.8;"><?php echo ucfirst($tipo); ?></div>
                </div>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- DEBUG: Mostrar información de depuración -->
        <div class="debug-info">
            <strong>Información de Depuración:</strong><br>
            <?php echo $debug_info; ?>
        </div>

        <div class="welcome-section">
            <h2>Panel de Control Educativo</h2>
            <p>Gestiona tu experiencia educativa de manera eficiente</p>
            <div class="role-badge">
                <i class="fas fa-user-tag"></i>
                <?php echo ucfirst($tipo); ?>
            </div>
        </div>

        <?php if ($tipo === 'admin'): ?>
        <!-- Dashboard Administrador -->
        <div class="grid">
            <div class="card">
                <h3><i class="fas fa-users"></i> Listado de Estudiantes</h3>
                <div>
                    <?php if (count($estudiantes) > 0): ?>
                        <?php foreach ($estudiantes as $estudiante): 
                            $datos = explode('|', $estudiante);
                            if (count($datos) >= 4): ?>
                        <div class="user-item">
                            <div class="user-info-small">
                                <div class="user-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <div><?php echo htmlspecialchars($datos[2]); ?></div>
                                    <div style="font-size: 0.8rem; color: #6c757d;">@<?php echo htmlspecialchars($datos[0]); ?></div>
                                </div>
                            </div>
                            <span style="font-size: 0.8rem; background: #e9ecef; padding: 4px 8px; border-radius: 10px;">
                                <?php echo htmlspecialchars($datos[3]); ?>
                            </span>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-graduate"></i>
                            <p>No hay estudiantes registrados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-chalkboard-teacher"></i> Profesores Registrados</h3>
                <div>
                    <?php if (count($profesores) > 0): ?>
                        <?php foreach ($profesores as $profesor): 
                            $datos = explode('|', $profesor);
                            if (count($datos) >= 5): ?>
                        <div class="user-item">
                            <div class="user-info-small">
                                <div class="user-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <div><?php echo htmlspecialchars($datos[2]); ?></div>
                                    <div style="font-size: 0.8rem; color: #6c757d;"><?php echo htmlspecialchars($datos[4]); ?></div>
                                </div>
                            </div>
                            <span style="font-size: 0.8rem; background: #e9ecef; padding: 4px 8px; border-radius: 10px;">
                                <?php echo htmlspecialchars($datos[3]); ?>
                            </span>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-tie"></i>
                            <p>No hay profesores registrados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-tasks"></i> Tareas Activas</h3>
                <div>
                    <?php if (count($tareas) > 0): ?>
                        <?php foreach ($tareas as $tarea): 
                            $datos = explode('|', $tarea);
                            if (count($datos) >= 4): ?>
                        <div class="tarea-item">
                            <div class="tarea-info">
                                <div class="tarea-title"><?php echo htmlspecialchars($datos[1]); ?></div>
                                <div class="tarea-meta">Por: <?php echo htmlspecialchars($datos[0]); ?></div>
                            </div>
                            <div class="tarea-desc"><?php echo htmlspecialchars($datos[2]); ?></div>
                            <div class="tarea-meta">Entrega: <?php echo htmlspecialchars($datos[3]); ?></div>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>No hay tareas activas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Calendario Académico</h3>
                <button class="btn btn-success" onclick="abrirModalHorario()" style="width: 100%; margin-bottom: 15px;">
                    <i class="fas fa-edit"></i> Editar Horario
                </button>
                <div>
                    <?php if (count($horario) > 0): ?>
                        <?php foreach ($horario as $evento): ?>
                        <div class="horario-item">
                            <div>
                                <div class="horario-fecha"><?php echo htmlspecialchars($evento[0]); ?></div>
                                <div class="horario-evento"><?php echo htmlspecialchars($evento[1]); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No hay eventos en el calendario</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php elseif ($tipo === 'profesor'): ?>
        <!-- Dashboard Profesor -->
        <div class="grid">
            <div class="card">
                <h3><i class="fas fa-user-graduate"></i> Mis Estudiantes</h3>
                <div>
                    <?php if (count($estudiantes) > 0): ?>
                        <?php foreach ($estudiantes as $estudiante): 
                            $datos = explode('|', $estudiante);
                            if (count($datos) >= 4): ?>
                        <div class="user-item">
                            <div class="user-info-small">
                                <div class="user-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <div><?php echo htmlspecialchars($datos[2]); ?></div>
                                    <div style="font-size: 0.8rem; color: #6c757d;">@<?php echo htmlspecialchars($datos[0]); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-graduate"></i>
                            <p>No hay estudiantes registrados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-tasks"></i> Gestionar Tareas</h3>
                <button class="btn" onclick="abrirModalTarea()" style="width: 100%; margin-bottom: 15px;">
                    <i class="fas fa-plus"></i> Nueva Tarea
                </button>
                <div>
                    <?php 
                    $mis_tareas = array_filter($tareas, function($tarea) {
                        $datos = explode('|', $tarea);
                        return count($datos) >= 4 && $datos[0] === $_SESSION['usuario'];
                    });
                    
                    if (count($mis_tareas) > 0): 
                        foreach ($mis_tareas as $tarea): 
                            $datos = explode('|', $tarea);
                            if (count($datos) >= 4): ?>
                    <div class="tarea-item">
                        <div class="tarea-info">
                            <div class="tarea-title"><?php echo htmlspecialchars($datos[1]); ?></div>
                        </div>
                        <div class="tarea-desc"><?php echo htmlspecialchars($datos[2]); ?></div>
                        <div class="tarea-meta">Entrega: <?php echo htmlspecialchars($datos[3]); ?></div>
                    </div>
                    <?php endif; endforeach; 
                    else: ?>
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>No has creado tareas aún</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-book"></i> Mi Especialidad</h3>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 2rem; color: var(--accent); margin-bottom: 10px;">
                        <i class="fas fa-atom"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($_SESSION['especialidad'] ?? 'General'); ?></h4>
                    <p style="color: #6c757d; margin-top: 10px;">Área de especialización</p>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Calendario Académico</h3>
                <div>
                    <?php if (count($horario) > 0): ?>
                        <?php foreach ($horario as $evento): ?>
                        <div class="horario-item">
                            <div>
                                <div class="horario-fecha"><?php echo htmlspecialchars($evento[0]); ?></div>
                                <div class="horario-evento"><?php echo htmlspecialchars($evento[1]); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No hay eventos en el calendario</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Dashboard Estudiante -->
        <div class="grid">
            <div class="card">
                <h3><i class="fas fa-tasks"></i> Mis Tareas Pendientes</h3>
                <div>
                    <?php if (count($tareas) > 0): ?>
                        <?php foreach ($tareas as $tarea): 
                            $datos = explode('|', $tarea);
                            if (count($datos) >= 4): ?>
                        <div class="tarea-item">
                            <div class="tarea-info">
                                <div class="tarea-title"><?php echo htmlspecialchars($datos[1]); ?></div>
                                <div class="tarea-meta">Por: <?php echo htmlspecialchars($datos[0]); ?></div>
                            </div>
                            <div class="tarea-desc"><?php echo htmlspecialchars($datos[2]); ?></div>
                            <div class="tarea-meta">Entrega: <?php echo htmlspecialchars($datos[3]); ?></div>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>No hay tareas pendientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-book-open"></i> Mis Materias</h3>
                <div>
                    <?php if (count($materias_unicas) > 0): ?>
                        <?php foreach ($materias_unicas as $materia): ?>
                        <div class="list-item">
                            <?php 
                            $icono = match($materia) {
                                'Matemáticas' => 'fas fa-calculator',
                                'Ciencias' => 'fas fa-flask',
                                'Lengua' => 'fas fa-book',
                                'Historia' => 'fas fa-globe-americas',
                                'Inglés' => 'fas fa-language',
                                default => 'fas fa-book'
                            };
                            $color = match($materia) {
                                'Matemáticas' => '#e74c3c',
                                'Ciencias' => '#3498db',
                                'Lengua' => '#27ae60',
                                'Historia' => '#f39c12',
                                'Inglés' => '#9b59b6',
                                default => '#95a5a6'
                            };
                            ?>
                            <i class="<?php echo $icono; ?>" style="color: <?php echo $color; ?>;"></i>
                            <span><?php echo htmlspecialchars($materia); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <p>No hay materias disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-chart-line"></i> Mi Progreso</h3>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 3rem; color: var(--success); margin-bottom: 10px;">
                        85%
                    </div>
                    <p style="color: #6c757d;">Promedio general</p>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Calendario Académico</h3>
                <div>
                    <?php if (count($horario) > 0): ?>
                        <?php foreach ($horario as $evento): ?>
                        <div class="horario-item">
                            <div>
                                <div class="horario-fecha"><?php echo htmlspecialchars($evento[0]); ?></div>
                                <div class="horario-evento"><?php echo htmlspecialchars($evento[1]); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No hay eventos en el calendario</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal para crear tareas (solo para profesores) -->
    <?php if ($tipo === 'profesor'): ?>
    <div class="modal" id="modalTarea">
        <div class="modal-content">
            <h3><i class="fas fa-plus"></i> Crear Nueva Tarea</h3>
            <form action="crear_tarea.php" method="POST">
                <div class="form-group">
                    <label>Título de la tarea:</label>
                    <input type="text" name="titulo" required placeholder="Ej: Ejercicios de Matemáticas">
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="4" required placeholder="Describe los detalles de la tarea..."></textarea>
                </div>
                <div class="form-group">
                    <label>Fecha de entrega:</label>
                    <input type="date" name="fecha_entrega" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Crear Tarea
                    </button>
                    <button type="button" class="btn btn-danger" onclick="cerrarModalTarea()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal para editar horario (solo para administradores) -->
    <?php if ($tipo === 'admin'): ?>
    <div class="modal" id="modalHorario">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Editar Horario Académico</h3>
            <form action="guardar_horario.php" method="POST" id="formHorario">
                <div id="eventosHorario">
                    <?php foreach ($horario as $index => $evento): ?>
                    <div class="form-group evento-horario">
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="fechas[]" value="<?php echo htmlspecialchars($evento[0]); ?>" placeholder="Fecha (ej: 15 Oct)" required style="flex: 1;">
                            <input type="text" name="eventos[]" value="<?php echo htmlspecialchars($evento[1]); ?>" placeholder="Evento (ej: Examen Matemáticas)" required style="flex: 2;">
                            <button type="button" class="btn btn-danger" onclick="eliminarEvento(this)" style="padding: 10px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn" onclick="agregarEvento()" style="width: 100%; margin-bottom: 15px;">
                    <i class="fas fa-plus"></i> Agregar Evento
                </button>
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Horario
                    </button>
                    <button type="button" class="btn btn-danger" onclick="cerrarModalHorario()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Funciones para tareas
        function abrirModalTarea() {
            document.getElementById('modalTarea').style.display = 'flex';
        }

        function cerrarModalTarea() {
            document.getElementById('modalTarea').style.display = 'none';
        }

        // Funciones para horario
        function abrirModalHorario() {
            document.getElementById('modalHorario').style.display = 'flex';
        }

        function cerrarModalHorario() {
            document.getElementById('modalHorario').style.display = 'none';
        }

        function agregarEvento() {
            const contenedor = document.getElementById('eventosHorario');
            const nuevoEvento = document.createElement('div');
            nuevoEvento.className = 'form-group evento-horario';
            nuevoEvento.innerHTML = `
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="fechas[]" placeholder="Fecha (ej: 15 Oct)" required style="flex: 1;">
                    <input type="text" name="eventos[]" placeholder="Evento (ej: Examen Matemáticas)" required style="flex: 2;">
                    <button type="button" class="btn btn-danger" onclick="eliminarEvento(this)" style="padding: 10px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            contenedor.appendChild(nuevoEvento);
        }

        function eliminarEvento(boton) {
            const evento = boton.closest('.evento-horario');
            evento.remove();
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const modalTarea = document.getElementById('modalTarea');
            const modalHorario = document.getElementById('modalHorario');
            
            if (event.target === modalTarea) {
                modalTarea.style.display = 'none';
            }
            if (event.target === modalHorario) {
                modalHorario.style.display = 'none';
            }
        }
    </script>
</body>
</html>