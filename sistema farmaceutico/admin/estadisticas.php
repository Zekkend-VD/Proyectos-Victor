<?php
session_start();
require '../conexion.php';

// Verificar permisos - solo admin puede ver estadísticas
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    mostrar_error_permisos('estadisticas');
    exit;
}

// Obtener estadísticas generales
$sql_estadisticas = "SELECT 
                    COUNT(*) as total_usuarios,
                    COUNT(DISTINCT compras.usuario_id) as usuarios_con_compras,
                    (SELECT COUNT(*) FROM enfermedades) as total_enfermedades,
                    (SELECT COUNT(*) FROM medicamentos) as total_medicamentos,
                    (SELECT SUM(stock) FROM medicamentos) as total_stock,
                    (SELECT COUNT(*) FROM compras) as total_compras,
                    (SELECT SUM(total) FROM compras WHERE estado = 'completada') as ingresos_totales,
                    (SELECT AVG(total) FROM compras WHERE estado = 'completada') as promedio_compra
                    FROM usuarios 
                    LEFT JOIN compras ON usuarios.id = compras.usuario_id";
$result_estadisticas = $conexion->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();

// Obtener compras por mes (últimos 6 meses)
$sql_compras_mes = "SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m') as mes,
                    COUNT(*) as total_compras,
                    SUM(total) as ingresos_mes
                    FROM compras 
                    WHERE fecha_compra >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY mes 
                    ORDER BY mes DESC 
                    LIMIT 6";
$result_compras_mes = $conexion->query($sql_compras_mes);
$compras_por_mes = [];
while($row = $result_compras_mes->fetch_assoc()) {
    $compras_por_mes[$row['mes']] = $row;
}

// Obtener medicamentos más vendidos
$sql_medicamentos_vendidos = "SELECT 
                            m.nombre,
                            SUM(cd.cantidad) as total_vendido,
                            COUNT(DISTINCT cd.compra_id) as veces_comprado
                            FROM compras_detalle cd
                            JOIN medicamentos m ON cd.medicamento_id = m.id
                            GROUP BY m.id, m.nombre
                            ORDER BY total_vendido DESC 
                            LIMIT 10";
$result_medicamentos_vendidos = $conexion->query($sql_medicamentos_vendidos);
$medicamentos_vendidos = [];
while($row = $result_medicamentos_vendidos->fetch_assoc()) {
    $medicamentos_vendidos[] = $row;
}

// Obtener enfermedades con más medicamentos
$sql_enfermedades_populares = "SELECT 
                              e.nombre,
                              COUNT(m.id) as total_medicamentos,
                              (SELECT COUNT(*) FROM compras_detalle cd 
                               JOIN medicamentos m2 ON cd.medicamento_id = m2.id 
                               WHERE m2.enfermedad_id = e.id) as veces_comprado
                              FROM enfermedades e
                              LEFT JOIN medicamentos m ON e.id = m.enfermedad_id
                              GROUP BY e.id, e.nombre
                              ORDER BY total_medicamentos DESC, veces_comprado DESC";
$result_enfermedades_populares = $conexion->query($sql_enfermedades_populares);
$enfermedades_populares = [];
while($row = $result_enfermedades_populares->fetch_assoc()) {
    $enfermedades_populares[] = $row;
}

// Obtener estados de compras
$sql_estados_compras = "SELECT 
                       estado,
                       COUNT(*) as total,
                       (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM compras)) as porcentaje
                       FROM compras 
                       GROUP BY estado";
$result_estados_compras = $conexion->query($sql_estados_compras);
$estados_compras = [];
while($row = $result_estados_compras->fetch_assoc()) {
    $estados_compras[] = $row;
}

// Calcular porcentajes para gráficos
$max_ventas = 0;
if (!empty($medicamentos_vendidos)) {
    $max_ventas = max(array_column($medicamentos_vendidos, 'total_vendido'));
}

$max_compras_mes = 0;
if (!empty($compras_por_mes)) {
    $max_compras_mes = max(array_column($compras_por_mes, 'total_compras'));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <style>
        .graficos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .grafico-container {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9);
        }
        
        .grafico-container h3 {
            margin-bottom: 1rem;
            color: var(--secondary);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
        }
        
        .barra-grafico {
            margin-bottom: 1rem;
        }
        
        .barra-etiqueta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }
        
        .barra-contenedor {
            background: #f0f0f0;
            border-radius: 10px;
            height: 25px;
            overflow: hidden;
            position: relative;
        }
        
        .barra-progreso {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
            background: var(--gradient-primary);
        }
        
        .estadisticas-rapidas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .tarjeta-estadistica {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9);
        }
        
        .tarjeta-estadistica .numero {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .tarjeta-estadistica .etiqueta {
            color: #666;
            font-size: 0.9rem;
        }
        
        .grafico-circular {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1rem 0;
        }
        
        .grafico-torta {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: conic-gradient(
                #3498db 0% 40%,
                #2ecc71 40% 70%,
                #e74c3c 70% 100%
            );
        }
        
        .leyenda-grafico {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .item-leyenda {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .color-leyenda {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }
        
        .meses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .mes-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .mes-nombre {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .mes-valor {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
    </style>
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
                    <li><a href="gestion_inventario.php">Gestión de Inventario</a></li>
                    <li><a href="estadisticas.php" class="active">Estadísticas</a></li>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Estadísticas del Sistema</h2>
            <p>Métricas y análisis de rendimiento de la farmacia</p>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="estadisticas-rapidas">
            <div class="tarjeta-estadistica">
                <div class="numero"><?php echo $estadisticas['total_usuarios']; ?></div>
                <div class="etiqueta">Usuarios Registrados</div>
            </div>
            <div class="tarjeta-estadistica">
                <div class="numero">$<?php echo number_format($estadisticas['ingresos_totales'] ?? 0, 2); ?></div>
                <div class="etiqueta">Ingresos Totales</div>
            </div>
            <div class="tarjeta-estadistica">
                <div class="numero"><?php echo $estadisticas['total_compras']; ?></div>
                <div class="etiqueta">Compras Realizadas</div>
            </div>
            <div class="tarjeta-estadistica">
                <div class="numero"><?php echo $estadisticas['total_medicamentos']; ?></div>
                <div class="etiqueta">Medicamentos</div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="graficos-grid">
            <!-- Gráfico de medicamentos más vendidos -->
            <div class="grafico-container">
                <h3>Medicamentos Más Vendidos</h3>
                <?php foreach($medicamentos_vendidos as $medicamento): 
                    $porcentaje = $max_ventas > 0 ? ($medicamento['total_vendido'] / $max_ventas) * 100 : 0;
                ?>
                    <div class="barra-grafico">
                        <div class="barra-etiqueta">
                            <span><?php echo htmlspecialchars($medicamento['nombre']); ?></span>
                            <span><?php echo $medicamento['total_vendido']; ?> unidades</span>
                        </div>
                        <div class="barra-contenedor">
                            <div class="barra-progreso" style="width: <?php echo $porcentaje; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($medicamentos_vendidos)): ?>
                    <p class="texto-centro">No hay datos de ventas disponibles</p>
                <?php endif; ?>
            </div>

            <!-- Gráfico de compras por mes -->
            <div class="grafico-container">
                <h3>Compras por Mes (Últimos 6 meses)</h3>
                <?php if(!empty($compras_por_mes)): ?>
                    <?php foreach($compras_por_mes as $mes => $datos): 
                        $porcentaje = $max_compras_mes > 0 ? ($datos['total_compras'] / $max_compras_mes) * 100 : 0;
                    ?>
                        <div class="barra-grafico">
                            <div class="barra-etiqueta">
                                <span><?php echo date('M Y', strtotime($mes . '-01')); ?></span>
                                <span><?php echo $datos['total_compras']; ?> compras</span>
                            </div>
                            <div class="barra-contenedor">
                                <div class="barra-progreso" style="width: <?php echo $porcentaje; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="texto-centro">No hay compras en los últimos 6 meses</p>
                <?php endif; ?>
            </div>

            <!-- Gráfico de estados de compras -->
            <div class="grafico-container">
                <h3>Estados de Compras</h3>
                <?php if(!empty($estados_compras)): ?>
                    <?php foreach($estados_compras as $estado): ?>
                        <div class="barra-grafico">
                            <div class="barra-etiqueta">
                                <span><?php echo ucfirst($estado['estado']); ?></span>
                                <span><?php echo $estado['total']; ?> (<?php echo number_format($estado['porcentaje'], 1); ?>%)</span>
                            </div>
                            <div class="barra-contenedor">
                                <div class="barra-progreso" style="width: <?php echo $estado['porcentaje']; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="texto-centro">No hay compras registradas</p>
                <?php endif; ?>
            </div>

            <!-- Gráfico de enfermedades populares -->
            <div class="grafico-container">
                <h3>Enfermedades con Más Medicamentos</h3>
                <?php foreach($enfermedades_populares as $enfermedad): ?>
                    <div class="barra-grafico">
                        <div class="barra-etiqueta">
                            <span><?php echo htmlspecialchars($enfermedad['nombre']); ?></span>
                            <span><?php echo $enfermedad['total_medicamentos']; ?> medicamentos</span>
                        </div>
                        <div class="barra-contenedor">
                            <div class="barra-progreso" style="width: <?php echo min($enfermedad['total_medicamentos'] * 20, 100); ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($enfermedades_populares)): ?>
                    <p class="texto-centro">No hay enfermedades registradas</p>
                <?php endif; ?>
            </div>

            <!-- Resumen de métricas -->
            <div class="grafico-container">
                <h3>Resumen de Métricas</h3>
                <div class="meses-grid">
                    <div class="mes-item">
                        <div class="mes-nombre">Usuarios Activos</div>
                        <div class="mes-valor"><?php echo $estadisticas['usuarios_con_compras']; ?></div>
                    </div>
                    <div class="mes-item">
                        <div class="mes-nombre">Enfermedades</div>
                        <div class="mes-valor"><?php echo $estadisticas['total_enfermedades']; ?></div>
                    </div>
                    <div class="mes-item">
                        <div class="mes-nombre">Stock Total</div>
                        <div class="mes-valor"><?php echo $estadisticas['total_stock']; ?></div>
                    </div>
                    <div class="mes-item">
                        <div class="mes-nombre">Promedio Compra</div>
                        <div class="mes-valor">$<?php echo number_format($estadisticas['promedio_compra'] ?? 0, 2); ?></div>
                    </div>
                </div>
            </div>

            <!-- Información de rendimiento -->
            <div class="grafico-container">
                <h3>Rendimiento del Sistema</h3>
                <div class="barra-grafico">
                    <div class="barra-etiqueta">
                        <span>Tasa de Conversión</span>
                        <span><?php echo $estadisticas['total_usuarios'] > 0 ? number_format(($estadisticas['usuarios_con_compras'] / $estadisticas['total_usuarios']) * 100, 1) : 0; ?>%</span>
                    </div>
                    <div class="barra-contenedor">
                        <div class="barra-progreso" style="width: <?php echo $estadisticas['total_usuarios'] > 0 ? ($estadisticas['usuarios_con_compras'] / $estadisticas['total_usuarios']) * 100 : 0; ?>%"></div>
                    </div>
                </div>
                
                <div class="barra-grafico">
                    <div class="barra-etiqueta">
                        <span>Inventario Utilizado</span>
                        <span>
                            <?php 
                            $stock_inicial = 1000; // Este valor debería venir de la base de datos
                            $porcentaje_utilizado = $stock_inicial > 0 ? (($stock_inicial - $estadisticas['total_stock']) / $stock_inicial) * 100 : 0;
                            echo number_format($porcentaje_utilizado, 1); 
                            ?>%
                        </span>
                    </div>
                    <div class="barra-contenedor">
                        <div class="barra-progreso" style="width: <?php echo $porcentaje_utilizado; ?>%"></div>
                    </div>
                </div>
                
                <div class="barra-grafico">
                    <div class="barra-etiqueta">
                        <span>Completitud de Catálogo</span>
                        <span>
                            <?php 
                            $enfermedades_con_medicamentos = 0;
                            foreach($enfermedades_populares as $enfermedad) {
                                if ($enfermedad['total_medicamentos'] > 0) {
                                    $enfermedades_con_medicamentos++;
                                }
                            }
                            $porcentaje_completitud = $estadisticas['total_enfermedades'] > 0 ? ($enfermedades_con_medicamentos / $estadisticas['total_enfermedades']) * 100 : 0;
                            echo number_format($porcentaje_completitud, 1); 
                            ?>%
                        </span>
                    </div>
                    <div class="barra-contenedor">
                        <div class="barra-progreso" style="width: <?php echo $porcentaje_completitud; ?>%"></div>
                    </div>
                </div>
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