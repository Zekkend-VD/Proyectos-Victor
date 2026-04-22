#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import datetime
import pytz

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

lima_tz = pytz.timezone('America/Lima')
ahora = datetime.datetime.now(lima_tz)
fecha = ahora.strftime("%d-%m-%Y")
hora = ahora.strftime("%H:%M:%S")

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 2 — Hora de Lima</title>
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
        .info-card {{
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            border-left: 6px solid #f472b6;
        }}
        .info-card p {{
            font-size: 1.2rem;
            margin: 8px 0;
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
            background: #f472b6;
            color: #0f172a;
            border-color: #f472b6;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(244,114,182,0.3);
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
        <h1>🇵🇪 Ejercicio 2 — Hora de Lima</h1>
        <div class="info-card">
            <p>📅 Fecha en Lima: <strong>{fecha}</strong></p>
            <p>⏰ Hora en Lima: <strong>{hora}</strong></p>
        </div>
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")