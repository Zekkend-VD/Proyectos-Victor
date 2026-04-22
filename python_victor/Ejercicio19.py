#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if "pais" in form:
    pais = form.getvalue("pais")
    if pais:
        paises = {
            "peru": "Perú",
            "argentina": "Argentina", 
            "brasil": "Brasil",
            "chile": "Chile",
            "colombia": "Colombia",
            "ecuador": "Ecuador",
            "mexico": "México",
            "venezuela": "Venezuela"
        }
        pais_nombre = paises.get(pais, "Desconocido")
        resultado = f"<div class='result-card'><p>🌍 País seleccionado: <strong>{pais_nombre}</strong></p></div>"
    else:
        resultado = "<div class='result-card error'>No se ha seleccionado ningún país</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 19 — Seleccionar país</title>
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
        select {{
            background: #1e293b;
            color: #f1f5f9;
            border: 2px solid #334155;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1rem;
            width: 100%;
            margin-bottom: 16px;
            transition: border 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23f1f5f9' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }}
        select:focus {{
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
        <h1>🌎 Ejercicio 19 — Seleccionar país</h1>
        <form method="post">
            <label for="pais">País:</label>
            <select name="pais" id="pais">
                <option value="">Seleccione un país</option>
                <option value="peru">Perú</option>
                <option value="argentina">Argentina</option>
                <option value="brasil">Brasil</option>
                <option value="chile">Chile</option>
                <option value="colombia">Colombia</option>
                <option value="ecuador">Ecuador</option>
                <option value="mexico">México</option>
                <option value="venezuela">Venezuela</option>
            </select>
            <input type="submit" value="Ver país">
        </form>
        {resultado}
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")