#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import datetime

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if "fecha" in form and "dias" in form:
    fecha_str = form.getvalue("fecha")
    dias_str = form.getvalue("dias")
    
    if fecha_str and dias_str:
        try:
            fecha = datetime.datetime.strptime(fecha_str, "%Y-%m-%d")
            dias = int(dias_str)
            nueva_fecha = fecha + datetime.timedelta(days=dias)
            resultado = f"""
            <div class="result-card">
                <p><strong>Fecha original:</strong> {fecha_str}</p>
                <p><strong>Días sumados:</strong> {dias}</p>
                <p><strong>Nueva fecha:</strong> {nueva_fecha.strftime('%Y-%m-%d')}</p>
            </div>
            """
        except:
            resultado = "<div class='result-card error'>Por favor ingresa valores válidos</div>"
    else:
        resultado = "<div class='result-card error'>Por favor completa ambos campos</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 15 — Sumar días</title>
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
        input[type="date"], input[type="number"] {{
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
        input[type="date"]:focus, input[type="number"]:focus {{
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
        }}
        .result-card.error {{
            border-left-color: #f87171;
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
        <h1>➕ Ejercicio 15 — Sumar días</h1>
        <form method="post">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>
            <label for="dias">Número de días:</label>
            <input type="number" name="dias" id="dias" min="1" required>
            <input type="submit" value="Calcular">
        </form>
        {resultado}
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")