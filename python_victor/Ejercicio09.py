#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if "string" in form and "letra" in form:
    texto = form.getvalue("string")
    letra = form.getvalue("letra")
    if texto and letra:
        conteo = texto.count(letra)
        resultado = f"""
        <div class="result-card">
            <p><strong>String:</strong> {texto}</p>
            <p><strong>Letra:</strong> '{letra}'</p>
            <p><strong>Número de apariciones:</strong> {conteo}</p>
        </div>
        """
    else:
        resultado = "<div class='result-card error'>Por favor completa ambos campos</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 9 — Contar letra</title>
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
        input[type="text"] {{
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
        input[type="text"]:focus {{
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
        <h1>🔢 Ejercicio 9 — Contar letra</h1>
        <form method="post">
            <label for="string">String:</label>
            <input type="text" name="string" id="string" required>
            <label for="letra">Letra (una sola):</label>
            <input type="text" name="letra" id="letra" maxlength="1" required>
            <input type="submit" value="Contar">
        </form>
        {resultado}
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")