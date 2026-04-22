#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
resultado = ""
color_fondo = ""  # Para guardar el color seleccionado y aplicarlo al fondo

# Lista de colores con nombres y códigos hexadecimales
colores = {
    "rojo": "#ef4444",
    "verde": "#10b981",
    "azul": "#3b82f6",
    "amarillo": "#f59e0b",
    "negro": "#1e293b",
    "blanco": "#f1f5f9",
    "morado": "#8b5cf6",
    "rosa": "#ec4899",
    "cian": "#06b6d4",
    "naranja": "#f97316",
    "gris": "#6b7280",
    "indigo": "#6366f1"
}

if "color" in form and "numero" in form:
    color_key = form.getvalue("color")
    numero = form.getvalue("numero")
    
    if color_key and numero and color_key in colores:
        color_nombre = color_key.capitalize()
        color_hex = colores[color_key]
        color_fondo = color_hex  # Se usará para el fondo
        resultado = f"""
        <div class="result-card" style="background-color: {color_hex}20; border-left-color: {color_hex};">
            <p style="font-size: 2rem; font-weight: bold; color: {color_hex};">{color_nombre} {numero}</p>
            <p style="color: {color_hex};">🎨 Color: {color_nombre} | Número: {numero}</p>
        </div>
        """
    else:
        resultado = "<div class='result-card error'>Por favor selecciona un color y escribe un número</div>"

# Generar opciones del select
opciones = ""
for color in colores.keys():
    selected = ' selected' if color == 'azul' else ''
    opciones += f'<option value="{color}"{selected}>{color.capitalize()}</option>'

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 20 — Color y número PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}
        body {{
            background-color: {color_fondo if color_fondo else '#0f172a'};
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background-color 0.5s ease;
        }}
        .container {{
            max-width: 600px;
            width: 100%;
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(10px);
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
            background: rgba(15, 23, 42, 0.8);
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
        }}
        label {{
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
        }}
        select, input[type="number"] {{
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
        select:focus, input[type="number"]:focus {{
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
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(5px);
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            border-left: 6px solid;
            transition: all 0.3s ease;
        }}
        .result-card.error {{
            border-left-color: #f87171;
        }}
        .color-preview {{
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            margin-left: 10px;
            vertical-align: middle;
            border: 2px solid white;
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
        <h1>🎨 Ejercicio 20 — Color y número PRO</h1>
        <form method="post">
            <label for="color">Color:</label>
            <select name="color" id="color">
                {opciones}
            </select>
            <label for="numero">Número (1-99):</label>
            <input type="number" name="numero" id="numero" min="1" max="99" value="42">
            <input type="submit" value="Combinar">
        </form>
        {resultado}
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")