#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# ============================================
# Ejercicio26.py – Verificador de palíndromos (con explicación)
# ============================================
import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""
texto_ingresado = ""

if "texto" in form:
    texto_ingresado = form.getvalue("texto")
    if texto_ingresado:
        # Limpiar: quitar espacios, signos y pasar a minúsculas
        limpio = ''.join(c.lower() for c in texto_ingresado if c.isalnum())
        es_palindromo = limpio == limpio[::-1]
        if es_palindromo:
            mensaje = f"<p style='color:#2dd4bf; font-size:1.2rem;'>✨ ¡Sí! <strong>'{texto_ingresado}'</strong> es un palíndromo.</p>"
        else:
            mensaje = f"<p style='color:#f87171; font-size:1.2rem;'>❌ No, <strong>'{texto_ingresado}'</strong> no es un palíndromo.</p>"
        resultado = f"<div class='result-card'>{mensaje}</div>"
    else:
        resultado = "<div class='result-card error'>Por favor ingresa un texto</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 26 — Palíndromo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .explicacion {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #f472b6; }}
        .explicacion p {{ margin-bottom:10px; }}
        .explicacion .ejemplo {{ color:#facc15; font-style:italic; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input[type="text"] {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input[type="text"]:focus {{ border-color:#2dd4bf; outline:none; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 24px; border-radius:40px; font-weight:600; cursor:pointer; width:100%; }}
        input[type="submit"]:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); }}
        .result-card {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #2dd4bf; }}
        .result-card.error {{ border-left-color:#f87171; }}
        .back-link {{ display:inline-flex; align-items:center; gap:8px; background:#334155; color:#f1f5f9; text-decoration:none; padding:12px 24px; border-radius:40px; font-weight:500; transition:0.2s; border:1px solid #475569; margin-top:24px; }}
        .back-link:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Ejercicio 26 — ¿Es palíndromo?</h1>
        
        <div class="explicacion">
            <p>🤔 <strong>¿Qué es un palíndromo?</strong></p>
            <p>Es una palabra, frase o número que se lee igual de izquierda a derecha que de derecha a izquierda, ignorando espacios, signos de puntuación y mayúsculas.</p>
            <p class="ejemplo">🔹 Ejemplos: "reconocer", "anilina", "somos", "luz azul".<br>
            🔹 También frases: "La ruta natural", "Anita lava la tina".</p>
            <p>¡Pruébalo con cualquier texto!</p>
        </div>

        <form method="post">
            <label for="texto">Ingresa una palabra o frase:</label>
            <input type="text" name="texto" id="texto" value="{texto_ingresado}" placeholder="Ej: reconocer" required>
            <input type="submit" value="Comprobar">
        </form>

        {resultado}

        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")