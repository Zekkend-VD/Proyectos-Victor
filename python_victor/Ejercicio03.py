#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import datetime

cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
ciudad = form.getvalue("ciudad", "America/Lima")  # Valor por defecto: Lima

# Lista de países con sus capitales y zonas horarias (más de 60)
paises = [
    ("Perú (Lima)", "America/Lima"),
    ("Argentina (Buenos Aires)", "America/Argentina/Buenos_Aires"),
    ("México (Ciudad de México)", "America/Mexico_City"),
    ("Chile (Santiago)", "America/Santiago"),
    ("Colombia (Bogotá)", "America/Bogota"),
    ("Venezuela (Caracas)", "America/Caracas"),
    ("Brasil (Brasilia)", "America/Sao_Paulo"),
    ("Ecuador (Quito)", "America/Guayaquil"),
    ("Bolivia (La Paz)", "America/La_Paz"),
    ("Paraguay (Asunción)", "America/Asuncion"),
    ("Uruguay (Montevideo)", "America/Montevideo"),
    ("Estados Unidos (Washington D.C.)", "America/New_York"),
    ("Canadá (Ottawa)", "America/Toronto"),
    ("España (Madrid)", "Europe/Madrid"),
    ("Francia (París)", "Europe/Paris"),
    ("Alemania (Berlín)", "Europe/Berlin"),
    ("Italia (Roma)", "Europe/Rome"),
    ("Reino Unido (Londres)", "Europe/London"),
    ("Rusia (Moscú)", "Europe/Moscow"),
    ("China (Pekín)", "Asia/Shanghai"),
    ("Japón (Tokio)", "Asia/Tokyo"),
    ("India (Nueva Delhi)", "Asia/Kolkata"),
    ("Australia (Canberra)", "Australia/Sydney"),
    ("Nueva Zelanda (Wellington)", "Pacific/Auckland"),
    ("Sudáfrica (Pretoria)", "Africa/Johannesburg"),
    ("Egipto (El Cairo)", "Africa/Cairo"),
    ("Nigeria (Abuya)", "Africa/Lagos"),
    ("Kenia (Nairobi)", "Africa/Nairobi"),
    ("Marruecos (Rabat)", "Africa/Casablanca"),
    ("Israel (Jerusalén)", "Asia/Jerusalem"),
    ("Arabia Saudita (Riad)", "Asia/Riyadh"),
    ("Emiratos Árabes (Abu Dabi)", "Asia/Dubai"),
    ("Turquía (Ankara)", "Europe/Istanbul"),
    ("Grecia (Atenas)", "Europe/Athens"),
    ("Portugal (Lisboa)", "Europe/Lisbon"),
    ("Países Bajos (Ámsterdam)", "Europe/Amsterdam"),
    ("Bélgica (Bruselas)", "Europe/Brussels"),
    ("Suecia (Estocolmo)", "Europe/Stockholm"),
    ("Noruega (Oslo)", "Europe/Oslo"),
    ("Dinamarca (Copenhague)", "Europe/Copenhagen"),
    ("Finlandia (Helsinki)", "Europe/Helsinki"),
    ("Polonia (Varsovia)", "Europe/Warsaw"),
    ("República Checa (Praga)", "Europe/Prague"),
    ("Hungría (Budapest)", "Europe/Budapest"),
    ("Austria (Viena)", "Europe/Vienna"),
    ("Suiza (Berna)", "Europe/Zurich"),
    ("Irlanda (Dublín)", "Europe/Dublin"),
    ("Islandia (Reikiavik)", "Atlantic/Reykjavik"),
    ("Singapur (Singapur)", "Asia/Singapore"),
    ("Malasia (Kuala Lumpur)", "Asia/Kuala_Lumpur"),
    ("Tailandia (Bangkok)", "Asia/Bangkok"),
    ("Vietnam (Hanói)", "Asia/Ho_Chi_Minh"),
    ("Filipinas (Manila)", "Asia/Manila"),
    ("Indonesia (Yakarta)", "Asia/Jakarta"),
    ("Pakistán (Islamabad)", "Asia/Karachi"),
    ("Bangladesh (Daca)", "Asia/Dhaka"),
    ("Sri Lanka (Sri Jayawardenapura Kotte)", "Asia/Colombo"),
    ("Nepal (Katmandú)", "Asia/Kathmandu"),
    ("Irán (Teherán)", "Asia/Tehran"),
    ("Irak (Bagdad)", "Asia/Baghdad"),
    ("Ucrania (Kiev)", "Europe/Kiev"),
    ("Rumania (Bucarest)", "Europe/Bucharest"),
    ("Bulgaria (Sofía)", "Europe/Sofia"),
    ("Serbia (Belgrado)", "Europe/Belgrade"),
    ("Croacia (Zagreb)", "Europe/Zagreb"),
    ("Eslovenia (Liubliana)", "Europe/Ljubljana"),
    ("Eslovaquia (Bratislava)", "Europe/Bratislava"),
    ("Lituania (Vilna)", "Europe/Vilnius"),
    ("Letonia (Riga)", "Europe/Riga"),
    ("Estonia (Tallin)", "Europe/Tallinn"),
    ("Bielorrusia (Minsk)", "Europe/Minsk"),
    ("Georgia (Tiflis)", "Asia/Tbilisi"),
    ("Armenia (Ereván)", "Asia/Yerevan"),
    ("Azerbaiyán (Bakú)", "Asia/Baku"),
    ("Kazajistán (Nursultán)", "Asia/Almaty"),
    ("Corea del Sur (Seúl)", "Asia/Seoul"),
    ("Corea del Norte (Pionyang)", "Asia/Pyongyang"),
    ("Taiwán (Taipéi)", "Asia/Taipei"),
    ("Hong Kong (Hong Kong)", "Asia/Hong_Kong"),
    ("Camboya (Nom Pen)", "Asia/Phnom_Penh"),
    ("Laos (Vientián)", "Asia/Vientiane"),
    ("Madagascar (Antananarivo)", "Indian/Antananarivo"),
    ("Angola (Luanda)", "Africa/Luanda"),
    ("Mozambique (Maputo)", "Africa/Maputo"),
    ("Tanzania (Dodoma)", "Africa/Dar_es_Salaam"),
    ("Uganda (Kampala)", "Africa/Kampala"),
    ("Etiopía (Adís Abeba)", "Africa/Addis_Ababa"),
    ("Ghana (Acra)", "Africa/Accra"),
    ("Costa de Marfil (Yamusukro)", "Africa/Abidjan"),
    ("Senegal (Dakar)", "Africa/Dakar"),
    ("Argelia (Argel)", "Africa/Algiers"),
]

# Construir un diccionario para obtener el nombre a partir de la zona
nombre_por_zona = {zona: nombre for nombre, zona in paises}

try:
    import pytz
    tz = pytz.timezone(ciudad)
    hora_local = datetime.datetime.now(tz).strftime("%H:%M:%S")
    # Obtener el nombre de la ciudad seleccionada
    nombre_ciudad = nombre_por_zona.get(ciudad, ciudad)
except:
    hora_local = "No disponible (pytz requerido)"
    nombre_ciudad = ciudad

hora_servidor = datetime.datetime.now().strftime("%H:%M:%S")

# Generar las opciones del select, marcando la seleccionada
opciones = ""
for nombre, zona in paises:
    selected = " selected" if zona == ciudad else ""
    opciones += f'<option value="{zona}"{selected}>{nombre}</option>'

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 3 — Zonas horarias mundiales</title>
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
        select, input[type="submit"] {{
            background: #1e293b;
            color: #f1f5f9;
            border: 2px solid #334155;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            margin: 8px 0;
        }}
        select:hover, select:focus {{
            border-color: #2dd4bf;
            outline: none;
        }}
        input[type="submit"] {{
            background: linear-gradient(135deg, #2dd4bf, #14b8a6);
            color: #0f172a;
            font-weight: 600;
            border: none;
        }}
        input[type="submit"]:hover {{
            background: linear-gradient(135deg, #f472b6, #ec4899);
            color: white;
        }}
        .info-card {{
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            border-left: 6px solid #2dd4bf;
        }}
        .capital {{
            color: #f472b6;
            font-weight: 500;
            margin-bottom: 8px;
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
        <h1>🌍 Ejercicio 3 — Zonas horarias mundiales</h1>
        <form method="get">
            <select name="ciudad" size="10" style="height: auto;">
                {opciones}
            </select>
            <input type="submit" value="Ver hora">
        </form>
        <div class="info-card">
            <p class="capital">📍 {nombre_ciudad}</p>
            <p>🕒 Hora local: <strong>{hora_local}</strong></p>
            <p>🖥️ Hora del servidor: <strong>{hora_servidor}</strong></p>
        </div>
        <a href="index.py" class="back-link">
            <span class="arrow">←</span> Volver al inicio
        </a>
    </div>
</body>
</html>""")