#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()

# Procesar redirección según el formulario enviado
if "pagina_python" in form:
    pagina = form.getvalue("pagina_python")
    print(f"""<html><head><meta http-equiv="refresh" content="0;url={pagina}"></head><body>Redirigiendo...</body></html>""")
elif "pagina_expo" in form:
    pagina = form.getvalue("pagina_expo")
    print(f"""<html><head><meta http-equiv="refresh" content="0;url={pagina}"></head><body>Redirigiendo...</body></html>""")
elif "pagina_propio" in form:
    pagina = form.getvalue("pagina_propio")
    print(f"""<html><head><meta http-equiv="refresh" content="0;url={pagina}"></head><body>Redirigiendo...</body></html>""")
elif "pagina_creativo" in form:
    pagina = form.getvalue("pagina_creativo")
    print(f"""<html><head><meta http-equiv="refresh" content="0;url={pagina}"></head><body>Redirigiendo...</body></html>""")
else:
    print("""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio de Ejercicios - Neon Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background-color:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px; }
        .container { max-width:800px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px rgba(0,0,0,0.5); border:1px solid #334155; }
        h1 { font-size:2.2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:32px; text-align:center; }
        .seccion { background:#0f172a; border-radius:20px; padding:24px; margin-bottom:32px; border:1px solid #334155; }
        .seccion h2 { color:#f472b6; font-size:1.5rem; margin-bottom:16px; border-left:6px solid #2dd4bf; padding-left:16px; }
        form { display:flex; flex-direction:column; gap:16px; }
        select, input[type="submit"] { background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:14px 20px; border-radius:16px; font-size:1rem; font-family:inherit; cursor:pointer; transition:all 0.2s ease; appearance:none; }
        select { background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23f1f5f9' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 20px center; background-size:16px; }
        select:hover, select:focus { border-color:#2dd4bf; outline:none; box-shadow:0 0 0 3px rgba(45,212,191,0.2); }
        input[type="submit"] { background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; font-weight:600; border:none; letter-spacing:0.5px; }
        input[type="submit"]:hover { background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); box-shadow:0 10px 20px -5px rgba(244,114,182,0.4); }
        .footer-note { text-align:center; color:#94a3b8; font-size:0.9rem; margin-top:16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✨ Portafolio de Ejercicios ✨</h1>

        <!-- Sección Python (original) -->
        <div class="seccion">
            <h2>🐍 Ejercicios Python</h2>
            <form action="" method="post">
                <select name="pagina_python">
                    <option value="Ejercicio01.py">Ejercicio 01 — Hora del servidor</option>
                    <option value="Ejercicio02.py">Ejercicio 02 — Hora de Lima</option>
                    <option value="Ejercicio03.py">Ejercicio 03 — Zonas horarias</option>
                    <option value="Ejercicio04.py">Ejercicio 04 — Conversión de número</option>
                    <option value="Ejercicio05.py">Ejercicio 05 — Preguntas personalizadas</option>
                    <option value="Ejercicio06.py">Ejercicio 06 — Buscar palabra</option>
                    <option value="Ejercicio07.py">Ejercicio 07 — Eliminar espacios</option>
                    <option value="Ejercicio08.py">Ejercicio 08 — Concatenar strings</option>
                    <option value="Ejercicio09.py">Ejercicio 09 — Contar letra</option>
                    <option value="Ejercicio10.py">Ejercicio 10 — Comparar strings</option>
                    <option value="Ejercicio11.py">Ejercicio 11 — Días entre fechas</option>
                    <option value="Ejercicio12.py">Ejercicio 12 — ¿Es verano?</option>
                    <option value="Ejercicio13.py">Ejercicio 13 — Fin de semana</option>
                    <option value="Ejercicio14.py">Ejercicio 14 — Número perfecto</option>
                    <option value="Ejercicio15.py">Ejercicio 15 — Sumar días</option>
                    <option value="Ejercicio16.py">Ejercicio 16 — Números impares</option>
                    <option value="Ejercicio17.py">Ejercicio 17 — Números pares</option>
                    <option value="Ejercicio18.py">Ejercicio 18 — Triángulo de asteriscos</option>
                    <option value="Ejercicio19.py">Ejercicio 19 — Seleccionar país</option>
                    <option value="Ejercicio20.py">Ejercicio 20 — Juntar color y número</option>
                </select>
                <input type="submit" value="Ir al ejercicio">
            </form>
        </div>

        <!-- Sección Ejercicios de Exposiciones -->
        <div class="seccion">
            <h2>📢 Ejercicios de Exposiciones</h2>
            <form action="" method="post">
                <select name="pagina_expo">
                    <option value="Ejercicio21.py">Ejercicio 21 — Calculadora</option>
                    <option value="Ejercicio22.py">Ejercicio 22 — Ahorcado</option>
                    <option value="Ejercicio22.5.py">Ejercicio 22.5 — misael</option>
                </select>
                <input type="submit" value="Ir al ejercicio">
            </form>
        </div>

        <!-- Sección Ejercicios Propios -->
        <div class="seccion">
            <h2>💡 Ejercicios Propios</h2>
            <form action="" method="post">
                <select name="pagina_propio">
                    <option value="Ejercicio23.py">Ejercicio 23 — Conversor de temperatura</option>
                    <option value="Ejercicio24.py">Ejercicio 24 — Generador de contraseñas</option>
                    <option value="Ejercicio25.py">Ejercicio 25 — Tabla de multiplicar</option>
                    <option value="Ejercicio26.py">Ejercicio 26 — Verificador de palíndromos</option>
                    <option value="Ejercicio27.py">Ejercicio 27 — Generador de chistes</option>
                    <option value="Ejercicio28.py">Ejercicio 28 — Conversor de monedas</option>
                    <option value="Ejercicio29.py">Ejercicio 29 — Piedra, papel o tijera</option>
                    <option value="Ejercicio30.py">Ejercicio 30 — Acertijo matemático</option>
                    <option value="Ejercicio31.py">Ejercicio 31 — Frases motivadoras</option>
                    <option value="Ejercicio32.py">Ejercicio 32 — Numeros romanos</option>
                    <option value="Ejercicio33.py">Ejercicio 33 — Dados</option>
                    <option value="Ejercicio34.py">Ejercicio 34 — Masa corporal</option>
                </select>
                <input type="submit" value="Ir al ejercicio">
            </form>
        </div>

        <div class="footer-note">
            Selecciona un ejercicio y haz clic en "Ir al ejercicio".
        </div>
    </div>
</body>
</html>""")