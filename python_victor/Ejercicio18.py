#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
resultado = ""

# Parámetros
altura = form.getvalue("altura", "5")
caracter = form.getvalue("caracter", "*")
orientacion = form.getvalue("orientacion", "normal")

if altura:
    try:
        altura_int = int(altura)
        if altura_int > 0:
            # Construir triángulo según orientación
            lineas = []
            if orientacion == "normal":
                # Triángulo normal (punta arriba)
                for i in range(1, altura_int + 1):
                    espacios = "&nbsp;" * (altura_int - i)
                    chars = caracter * (2 * i - 1)
                    lineas.append(f"{espacios}{chars}")
            elif orientacion == "invertido":
                # Triángulo invertido (punta abajo)
                for i in range(altura_int, 0, -1):
                    espacios = "&nbsp;" * (altura_int - i)
                    chars = caracter * (2 * i - 1)
                    lineas.append(f"{espacios}{chars}")
            elif orientacion == "derecha":
                # Triángulo hacia la derecha (media pirámide)
                for i in range(1, altura_int + 1):
                    lineas.append(caracter * i)
                for i in range(altura_int - 1, 0, -1):
                    lineas.append(caracter * i)
            elif orientacion == "izquierda":
                # Triángulo hacia la izquierda
                for i in range(1, altura_int + 1):
                    espacios = "&nbsp;" * (altura_int - i)
                    lineas.append(espacios + caracter * i)
                for i in range(altura_int - 1, 0, -1):
                    espacios = "&nbsp;" * (altura_int - i)
                    lineas.append(espacios + caracter * i)

            resultado = f"""
            <div class="result-card">
                <p><strong>Triángulo de altura {altura_int} (carácter '{caracter}'):</strong></p>
                <div class="triangulo">
                    {"<br>".join(lineas)}
                </div>
            </div>
            """
        else:
            resultado = "<div class='result-card error'>La altura debe ser positiva</div>"
    except ValueError:
        resultado = "<div class='result-card error'>Por favor ingresa una altura válida</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 18 — Triángulo de caracteres</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}
        body {{
            background-color: #0f172a;
            color: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }}
        .container {{
            max-width: 600px;
            width: 100%;
            background: #1e293b;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
            border: 1px solid #334155;
        }}
        h1 {{
            font-size: 2rem;
            font-weight: 600;
            background: linear-gradient(135deg, #2dd4bf, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
        }}
        form {{
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
        }}
        label {{
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
        }}
        input[type="number"], input[type="text"], select {{
            background: #1e293b;
            color: #f1f5f9;
            border: 2px solid #334155;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1rem;
            width: 100%;
            margin-bottom: 16px;
            transition: border 0.2s;
        }}
        input[type="number"]:focus, input[type="text"]:focus, select:focus {{
            border-color: #2dd4bf;
            outline: none;
        }}
        input[type="submit"] {{
            background: linear-gradient(135deg, #2dd4bf, #14b8a6);
            color: #0f172a;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }}
        input[type="submit"]:hover {{
            background: linear-gradient(135deg, #f472b6, #ec4899);
            color: white;
            transform: translateY(-2px);
        }}
        .result-card {{
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            border-left: 6px solid #2dd4bf;
            overflow-x: auto;
        }}
        .result-card.error {{
            border-left-color: #f87171;
        }}
        .triangulo {{
            font-family: monospace;
            font-size: 1.2rem;
            line-height: 1.4;
            color: #f472b6;
            text-shadow: 0 0 5px #f472b6;
            background: #0f172a;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            letter-spacing: 2px;
        }}
        .back-link {{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #334155;
            color: #f1f5f9;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid #475569;
            margin-top: 24px;
        }}
        .back-link:hover {{
            background: #2dd4bf;
            color: #0f172a;
            border-color: #2dd4bf;
            transform: translateY(-2px);
        }}
        .back-link:hover .arrow {{
            transform: translateX(-4px);
        }}
        .arrow {{
            font-size: 1.4rem;
            transition: transform 0.2s;
        }}
    </style>
</head>
<body>
    <div class="container">
        <h1>▲ Ejercicio 18 — Triángulo de caracteres</h1>
        <form method="post">
            <label for="altura">Altura del triángulo:</label>
            <input type="number" name="altura" id="altura" min="1" max="30" value="{altura}" required>

            <label for="caracter">Carácter a usar:</label>
            <input type="text" name="caracter" id="caracter" maxlength="1" value="{caracter}" required placeholder="Ej: *, #, @, A">

            <label for="orientacion">Orientación:</label>
            <select name="orientacion" id="orientacion">
                <option value="normal" {"selected" if orientacion == "normal" else ""}>Normal (punta arriba)</option>
                <option value="invertido" {"selected" if orientacion == "invertido" else ""}>Invertido (punta abajo)</option>
                <option value="derecha" {"selected" if orientacion == "derecha" else ""}>Hacia la derecha</option>
                <option value="izquierda" {"selected" if orientacion == "izquierda" else ""}>Hacia la izquierda</option>
            </select>

            <input type="submit" value="Dibujar">
        </form>
        {resultado}
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")