<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - EduFuturo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #f39c12;
            --success: #27ae60;
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

        /* Header */
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
        }

        nav ul li a:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-nosotros {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.9), rgba(52, 152, 219, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            color: white;
            padding: 150px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-nosotros::before {
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

        .hero-nosotros h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            animation: slideUp 1s ease-out;
        }

        .hero-nosotros p {
            font-size: 1.5rem;
            max-width: 700px;
            margin: 0 auto;
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

        /* Main Content */
        .main-content {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .section {
            margin-bottom: 80px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease-out forwards;
        }

        .section:nth-child(1) { animation-delay: 0.3s; }
        .section:nth-child(2) { animation-delay: 0.5s; }
        .section:nth-child(3) { animation-delay: 0.7s; }
        .section:nth-child(4) { animation-delay: 0.9s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.8rem;
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
            width: 100px;
            height: 4px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            border-radius: 2px;
        }

        .section-title p {
            color: #7f8c8d;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Historia Section */
        .historia-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .historia-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        .historia-text p {
            margin-bottom: 20px;
        }

        .historia-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .historia-image:hover {
            transform: translateY(-10px) scale(1.02);
        }

        .historia-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 30px;
            transform: translateY(100%);
            transition: var(--transition);
        }

        .historia-image:hover .image-overlay {
            transform: translateY(0);
        }

        /* Misión Visión Grid */
        .mv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .mv-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .mv-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
        }

        .mv-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .mv-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
            transition: var(--transition);
        }

        .mv-card:hover .mv-icon {
            transform: rotate(360deg) scale(1.1);
        }

        .mv-card h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .mv-card p {
            color: #7f8c8d;
            line-height: 1.7;
            font-size: 1.1rem;
        }

        /* Valores Section */
        .valores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .valor-card {
            background: white;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border-left: 4px solid var(--accent);
        }

        .valor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .valor-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .valor-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .valor-card p {
            color: #7f8c8d;
            line-height: 1.6;
        }

        /* Equipo Section */
        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .miembro-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            text-align: center;
        }

        .miembro-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .miembro-image {
            width: 100%;
            height: 250px;
            position: relative;
            overflow: hidden;
        }

        .miembro-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: var(--transition);
        }

        .miembro-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(243, 156, 18, 0.1));
            z-index: 1;
            opacity: 0;
            transition: var(--transition);
        }

        .miembro-card:hover .miembro-image::before {
            opacity: 1;
        }

        .miembro-card:hover .miembro-image img {
            transform: scale(1.05);
        }

        /* Fallback para mostrar icono si la imagen no carga */
        .miembro-image:not(:has(img)) {
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }

        .miembro-info {
            padding: 30px 25px;
        }

        .miembro-info h4 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 1.4rem;
        }

        .miembro-rol {
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .miembro-desc {
            color: #7f8c8d;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }

        .stat-item {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease-out forwards;
        }

        .stat-item:nth-child(1) { animation-delay: 0.3s; }
        .stat-item:nth-child(2) { animation-delay: 0.5s; }
        .stat-item:nth-child(3) { animation-delay: 0.7s; }
        .stat-item:nth-child(4) { animation-delay: 0.9s; }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #fff, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--accent), #e67e22);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .cta-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: var(--accent);
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 20px;
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

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-nosotros h1 {
                font-size: 2.5rem;
            }

            .historia-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .section-title h2 {
                font-size: 2.2rem;
            }

            nav ul {
                gap: 15px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }

        /* Efectos de partículas */
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
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>EduFuturo</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="index.php#login"><i class="fas fa-sign-in-alt"></i> Acceso</a></li>
                    <li><a href="nosotros.php"><i class="fas fa-users"></i> Nosotros</a></li>
                    <li><a href="#contacto"><i class="fas fa-envelope"></i> Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-nosotros">
        <div class="particles" id="particles"></div>
        <div class="container">
            <h1>Nuestra Historia</h1>
            <p>Conoce la trayectoria y valores que nos definen como institución educativa</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Historia Section -->
            <section class="section">
                <div class="section-title">
                    <h2>Nuestra Historia</h2>
                    <p>Una trayectoria de excelencia educativa desde 2010</p>
                </div>
                <div class="historia-content">
                    <div class="historia-text">
                        <p>EduFuturo nació en el año 2010 con una visión clara: transformar la educación tradicional mediante la integración de tecnología innovadora y metodologías pedagógicas modernas. Fundada por un grupo de educadores apasionados, nuestra institución comenzó como un pequeño proyecto con grandes aspiraciones.</p>
                        <p>A lo largo de los años, hemos crecido hasta convertirnos en una plataforma educativa líder, conectando a miles de estudiantes, profesores y administradores en un entorno de aprendizaje colaborativo y dinámico.</p>
                        <p>Nuestro compromiso con la calidad educativa y la innovación tecnológica nos ha permitido adaptarnos a las cambiantes necesidades del mundo moderno, siempre manteniendo nuestro enfoque principal: el éxito y desarrollo integral de cada estudiante.</p>
                    </div>
                    <div class="historia-image">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80" alt="Historia EduFuturo">
                        <div class="image-overlay">
                            <h3>2010 - Nuestros Inicios</h3>
                            <p>El comienzo de una revolución educativa</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Misión y Visión Section -->
            <section class="section">
                <div class="section-title">
                    <h2>Misión y Visión</h2>
                    <p>Los pilares que guían nuestro camino educativo</p>
                </div>
                <div class="mv-grid">
                    <div class="mv-card">
                        <div class="mv-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Nuestra Misión</h3>
                        <p>Facilitar el acceso a educación de calidad mediante herramientas tecnológicas modernas y un enfoque centrado en el estudiante. Buscamos formar personas íntegras, críticas y preparadas para los desafíos del futuro.</p>
                    </div>
                    <div class="mv-card">
                        <div class="mv-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Nuestra Visión</h3>
                        <p>Ser la plataforma educativa líder en innovación tecnológica y excelencia académica para 2025, reconocida por nuestro impacto positivo en la transformación educativa y el desarrollo de competencias del siglo XXI.</p>
                    </div>
                </div>
            </section>

            <!-- Valores Section -->
            <section class="section">
                <div class="section-title">
                    <h2>Nuestros Valores</h2>
                    <p>Principios que definen nuestra identidad institucional</p>
                </div>
                <div class="valores-grid">
                    <div class="valor-card">
                        <div class="valor-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Excelencia Académica</h4>
                        <p>Comprometidos con los más altos estándares de calidad educativa y formación integral.</p>
                    </div>
                    <div class="valor-card">
                        <div class="valor-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Pasión por Enseñar</h4>
                        <p>Educadores dedicados que inspiran el amor por el aprendizaje continuo.</p>
                    </div>
                    <div class="valor-card">
                        <div class="valor-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Trabajo en Equipo</h4>
                        <p>Fomentamos la colaboración entre estudiantes, profesores y familias.</p>
                    </div>
                    <div class="valor-card">
                        <div class="valor-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4>Innovación Constante</h4>
                        <p>Implementamos las últimas tecnologías y metodologías educativas.</p>
                    </div>
                </div>
            </section>

            <!-- Equipo Section -->
            <section class="section">
                <div class="section-title">
                    <h2>Equipo Directivo</h2>
                    <p>Los profesionales que lideran nuestra institución</p>
                </div>
                <div class="equipo-grid">
                    <div class="miembro-card">
                        <div class="miembro-image">
                            <img src="../imgs/professional_male_doctor_educator_portrait_headshot.jpg" alt="Dr. Christian Jaimes - Director General" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="miembro-info">
                            <h4>Dr. Christian Jaimes</h4>
                            <div class="miembro-rol">Director General</div>
                            <p class="miembro-desc">PhD en Educación con más de 20 años de experiencia en gestión educativa y transformación digital.</p>
                        </div>
                    </div>
                    <div class="miembro-card">
                        <div class="miembro-image">
                            <img src="foto.jpg" alt="Ing. Victor Ortega - Director Tecnológico" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="miembro-info">
                            <h4>Ing. Victor Ortega</h4>
                            <div class="miembro-rol">Director Tecnológico</div>
                            <p class="miembro-desc">Experto en desarrollo de plataformas educativas y soluciones tecnológicas para el aprendizaje.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿Listo para unirte a nuestra comunidad?</h2>
                <p>Forma parte de la revolución educativa y descubre un nuevo mundo de oportunidades de aprendizaje.</p>
                <a href="index.php#login" class="btn">
                    <i class="fas fa-rocket"></i>
                    Comenzar Ahora
                </a>
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
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-envelope"></i> info@edufuturo.edu</p>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Educación 123, Ciudad Futuro</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces Rápidos</h3>
                    <a href="index.php"><i class="fas fa-chevron-right"></i> Inicio</a>
                    <a href="index.php#login"><i class="fas fa-chevron-right"></i> Acceso</a>
                    <a href="nosotros.php"><i class="fas fa-chevron-right"></i> Nosotros</a>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 EduFuturo. Todos los derechos reservados. | Desarrollado con <i class="fas fa-heart" style="color: #e74c3c;"></i> para la educación</p>
            </div>
        </div>
    </footer>
</body>
</html>