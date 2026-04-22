#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

impares = [str(i) for i in range(1, 101, 2)]

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 16 — Números impares</title>
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
            max-width: 700px;
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
        .number-grid {{
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            border-left: 6px solid #2dd4bf;
            font-family: monospace;
            word-wrap: break-word;
            line-height: 2;
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
        <h1>🔢 Ejercicio 16 — Números impares del 1 al 100</h1>
        <div class="number-grid">
            {' '.join(impares)}
        </div>
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")