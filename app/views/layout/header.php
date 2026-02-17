<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcaldía Tlalpan</title>
    <style>
        /* ESTILOS FIJOS - NO SE PIERDEN */
        :root {
            --guinda: #773357;       /* */
            --guinda-dark: #5a2540;
            --gris-texto: #4A4A4A;   /* */
            --blanco: #ffffff;
            --radio-borde: 25px;   
        }

        body {
            background-color: var(--guinda) !important;
            background: linear-gradient(135deg, var(--guinda) 0%, var(--guinda-dark) 100%);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
        }

        /* Tarjeta moderna flotante */
        .card-moderna {
            background: var(--blanco);
            width: 90%;
            max-width: 450px;
            padding: 40px;
            border-radius: var(--radio-borde);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            text-align: center;
        }

        /* Inputs muy redondos y limpios */
        .input-redondo {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 2px solid #f0f0f0;
            border-radius: var(--radio-borde); 
            font-size: 16px;
            color: var(--gris-texto);
            box-sizing: border-box; /* Para que no se salga del contenedor */
            outline: none;
            transition: 0.3s;
        }

        .input-redondo:focus {
            border-color: var(--guinda);
            box-shadow: 0 0 10px rgba(119, 51, 87, 0.2);
        }

        /* Botón estilo App móvil */
        .btn-guinda {
            background: var(--guinda);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: var(--radio-borde);
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-guinda:hover {
            transform: scale(1.02);
            background: var(--guinda-dark);
        }

        .titulo-login {
            color: var(--guinda);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 800;
        }
        
        .subtitulo {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
            display: block;
        }

        label {
            display: block;
            text-align: left;
            margin-left: 15px;
            margin-bottom: 5px;
            color: var(--gris-texto);
            font-weight: 600;
            font-size: 14px;
        }

        /* --- ESTILOS EXTRA PARA EL FORMULARIO DE ENCUESTA --- */

        /* Contenedor de Radio Buttons (Sexo: Mujer/Hombre) */
        .radio-group-container {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            margin-left: 10px;
        }

        /* Estilo del Label Radio */
        .radio-custom {
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            font-size: 16px;
            color: var(--gris-texto);
            user-select: none;
        }

        /* Ocultar el input original feo */
        .radio-custom input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* El círculo personalizado */
        .radio-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 22px;
            width: 22px;
            background-color: #eee;
            border-radius: var(--radio-borde);
            transition: 0.2s;
            border: 2px solid #ddd;
        }

        /* Hover */
        .radio-custom:hover input ~ .radio-checkmark {
            background-color: #ccc;
        }

        /* Cuando está marcado */
        .radio-custom input:checked ~ .radio-checkmark {
            background-color: var(--guinda);
            border-color: var(--guinda);
        }

        /* El puntito blanco interior */
        .radio-checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .radio-custom input:checked ~ .radio-checkmark:after {
            display: block;
        }

        .radio-custom .radio-checkmark:after {
            top: 6px;
            left: 6px;
            width: 6px;
            height: 6px;
            border-radius: var(--radio-borde);
            background: white;
        }
        /* Contenedor de Radio Buttons */
        .radio-group-container {
            display: flex;
            gap: 15px;            /* Reduje un poco el espacio para que quepan mejor */
            margin-bottom: 25px;
            margin-left: 10px;
            flex-wrap: wrap;      /* <--- ¡ESTA ES LA MAGIA! Permite que bajen de línea */
        }

        /* Opcional: Si quieres que en celulares se vean en 2 columnas ordenadas */
        @media (max-width: 600px) {
            .radio-custom {
                width: 45%;       /* Ocupan casi la mitad cada uno */
            }
        }
        /* CLASE NUEVA PARA HACER LA TARJETA MÁS ANCHA */
        .card-xl {
            max-width: 900px !important; /* Mucho más espacio (ideal para Mapas y Tablas) */
            width: 95%; /* En celulares usa casi toda la pantalla */
            transition: max-width 0.5s ease; /* Animación suave si cambia de tamaño */
        }

        /* Ajuste para que el mapa se vea bien en celulares */
        @media (max-width: 768px) {
            .card-xl {
                padding: 20px; /* Menos relleno en celular para ganar espacio */
            }
        }
        /* Estilos para CHECKBOX (Cuadraditos de selección múltiple) */
        .checkbox-custom {
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            font-size: 16px;
            color: var(--gris-texto);
            user-select: none;
            margin-bottom: 10px;
            width: 100%; /* Para que ocupen el ancho disponible */
        }
        .checkbox-custom input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .checkbox-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 22px;
            width: 22px;
            background-color: #eee;
            border-radius: var(--radio-borde); /* Borde suave pero CUADRADO */
            border: 2px solid #ddd;
            transition: 0.2s;
        }
        .checkbox-custom:hover input ~ .checkbox-checkmark { background-color: #ccc; }
        .checkbox-custom input:checked ~ .checkbox-checkmark { background-color: var(--guinda); border-color: var(--guinda); }
        .checkbox-checkmark:after { content: ""; position: absolute; display: none; }
        .checkbox-custom input:checked ~ .checkbox-checkmark:after { display: block; }
        /* La palomita blanca (Tick) */
        .checkbox-custom .checkbox-checkmark:after {
            left: 7px; top: 3px; width: 6px; height: 12px;
            border: solid white; border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
        /* Contenedor para alinear botones */
        .botones-navegacion {
            display: flex;
            justify-content: space-between; /* Uno a la izq, otro a la der */
            gap: 15px;
            margin-top: 25px;
        }

        /* Botón Siguiente (El principal) */
        .btn-encuesta {
            flex: 1; /* Ocupa el espacio disponible */
            background-color: var(--guinda);
            color: white;
            padding: 12px;
            border: none;
            border-radius: var(--radio-borde);
            font-size: 16px;
            cursor: pointer;
        }

        /* Botón Atrás (Secundario) */
        .btn-atras {
            width: 100px; /* Más pequeño */
            background-color: #e0e0e0;
            color: #333;
            padding: 12px;
            border: none;
            border-radius: var(--radio-borde);
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-atras:hover {
            background-color: #d5d5d5;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        /* Ajuste para que el mapa se vea bien en tu tarjeta */
        #mapa-interactivo { height: 300px; width: 100%; border-radius: var(--radio-borde); z-index: 0; }
        .cols-gps { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; }
        @media (max-width: 768px) { .cols-gps { grid-template-columns: 1fr; } } /* En celular, una sola columna */
    </style>
</head>
<body>
    <div style="width: 100%; display: flex; justify-content: center;">