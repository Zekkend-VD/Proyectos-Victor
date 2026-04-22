<?php
session_start();

// Lógica de conteo de administradores ya no necesaria en el index
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFuturo - Plataforma Educativa</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header animado */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: var(--primary);
            padding: 15px 0;
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        .header.scrolled {
            padding: 10px 0;
            background: rgba(255, 255, 255, 0.98);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .logo:hover .logo-icon {
            transform: rotate(360deg) scale(1.1);
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav ul li a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 25px;
            transition: var(--transition);
            position: relative;
        }

        nav ul li a:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }

        /* Hero section mejorada */
        .hero {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.9), rgba(52, 152, 219, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            color: white;
            padding: 180px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,0 1000,1000"/></svg>');
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero h2 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            animation: slideUp 1s ease-out;
        }

        .hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
            animation: slideUp 1s ease-out 0.2s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--accent), #e67e22);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
            animation: slideUp 1s ease-out 0.4s both;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(243, 156, 18, 0.4);
        }

        /* Login section mejorada */
        .login-section {
            padding: 100px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            border-radius: 2px;
        }

        .section-title p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        /* Login container ya no es necesario - se usa un solo login centrado */

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px 30px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
        }

        .login-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .login-card h3 {
            color: var(--primary);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-card h3 i {
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: #fafafa;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--secondary);
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .register-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .register-link a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        /* Footer mejorado */
        footer {
            background: linear-gradient(135deg, var(--primary), #1a252f);
            color: white;
            padding: 60px 0 20px;
            position: relative;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: var(--accent);
            position: relative;
            display: inline-block;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .footer-column p, .footer-column a {
            color: #bdc3c7;
            margin-bottom: 12px;
            display: block;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-column a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h2 {
                font-size: 2.5rem;
            }
            
            /* Responsive ya no necesario para login-container */
            
            nav ul {
                gap: 15px;
            }
        }

        /* Efectos de partículas para el hero */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header id="mainHeader">
        <div class="container header-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>EduFuturo</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#inicio"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#login"><i class="fas fa-sign-in-alt"></i> Acceso</a></li>
                    <li><a href="nosotros.php"><i class="fas fa-users"></i> Nosotros</a></li>
                    <li><a href="#contacto"><i class="fas fa-envelope"></i> Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="particles" id="particles"></div>
        <div class="container">
            <h2>Transformando la Educación del Futuro</h2>
            <p>Una plataforma educativa innovadora que conecta estudiantes, profesores y administradores en un entorno de aprendizaje colaborativo y moderno.</p>
            <a href="#login" class="btn">
                <i class="fas fa-rocket"></i>
                Comenzar Ahora
            </a>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="login">
        <div class="container">
            <div class="section-title">
                <h2>Acceso a la Plataforma</h2>
                <p>Inicia sesión con tus credenciales para continuar</p>
            </div>
            <div style="max-width: 500px; margin: 0 auto;">
                <!-- Login Unificado -->
                <div class="login-card">
                    <h3><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h3>
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="usuario"><i class="fas fa-user"></i> Usuario</label>
                            <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </button>
                    </form>
                    <div class="register-link">
                        <a href="registro.php">
                            <i class="fas fa-user-plus"></i>
                            ¿No tienes cuenta? Regístrate aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>EduFuturo</h3>
                    <p>Transformando la educación mediante tecnología innovadora y enfoques pedagógicos modernos.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-envelope"></i> info@edufuturo.edu</p>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Educación 123, Ciudad Futuro</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces Rápidos</h3>
                    <a href="#inicio"><i class="fas fa-chevron-right"></i> Inicio</a>
                    <a href="#login"><i class="fas fa-chevron-right"></i> Acceso</a>
                    <a href="nosotros.php"><i class="fas fa-chevron-right"></i> Nosotros</a>
                    <a href="#contacto"><i class="fas fa-chevron-right"></i> Contacto</a>
                </div>
                <div class="footer-column">
                    <h3>Soporte</h3>
                    <a href="#"><i class="fas fa-question-circle"></i> Centro de Ayuda</a>
                    <a href="#"><i class="fas fa-headset"></i> Soporte Técnico</a>
                    <a href="#"><i class="fas fa-comments"></i> Contactar Soporte</a>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 EduFuturo. Todos los derechos reservados. | Desarrollado con <i class="fas fa-heart" style="color: #e74c3c;"></i> para la educación</p>
            </div>
        </div>
    </footer>

</body>
</html>