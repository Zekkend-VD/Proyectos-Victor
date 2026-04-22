#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# ============================================
# Ejercicio24.py – Generador de contraseñas
# ============================================
import cgi
import cgitb
import random
import string
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if "longitud" in form:
    try:
        longitud = int(form.getvalue("longitud"))
        if longitud < 4:
            resultado = "<div class='result-card error'>La longitud debe ser al menos 4</div>"
        else:
            # Definir caracteres
            minusculas = string.ascii_lowercase
            mayusculas = string.ascii_uppercase
            digitos = string.digits
            simbolos = "!@#$%^&*()_+-=[]{}|;:,.<>?"
            
            # Asegurar al menos uno de cada tipo
            password = [
                random.choice(minusculas),
                random.choice(mayusculas),
                random.choice(digitos),
                random.choice(simbolos)
            ]
            # Completar con caracteres aleatorios de todos los tipos
            todos = minusculas + mayusculas + digitos + simbolos
            password += random.choices(todos, k=longitud-4)
            random.shuffle(password)
            password = ''.join(password)
            resultado = f"<div class='result-card'><p>🔐 Contraseña generada: <strong>{password}</strong></p></div>"
    except ValueError:
        resultado = "<div class='result-card error'>Ingresa un número válido</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 24 — Generador de contraseñas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input[type="number"] {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input[type="number"]:focus {{ border-color:#2dd4bf; outline:none; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 24px; border-radius:40px; font-weight:600; cursor:pointer; width:100%; }}
        input[type="submit"]:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); }}
        .result-card {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #2dd4bf; word-break:break-all; }}
        .result-card.error {{ border-left-color:#f87171; }}
        .back-link {{ display:inline-flex; align-items:center; gap:8px; background:#334155; color:#f1f5f9; text-decoration:none; padding:12px 24px; border-radius:40px; font-weight:500; transition:0.2s; border:1px solid #475569; margin-top:24px; }}
        .back-link:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🔑 Ejercicio 24 — Generador de contraseñas</h1>
        <form method="post">
            <label for="longitud">Longitud de la contraseña (mínimo 4):</label>
            <input type="number" name="longitud" id="longitud" min="4" value="8" required>
            <input type="submit" value="Generar">
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")