<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?></title>
    <style>
        /* --- ESTILOS INSTITUCIONALES (Igual que en el cuestionario) --- */
        :root {
            --guinda: #773357;       
            --guinda-dark: #5a2540;
            --gris-texto: #4A4A4A;   
            --blanco: #ffffff;
        }

        body {
            background-color: var(--guinda);
            background: linear-gradient(135deg, var(--guinda) 0%, var(--guinda-dark) 100%);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--gris-texto);
        }

        /* Tarjeta Blanca Flotante */
        .card-moderna {
            background: var(--blanco);
            width: 90%;
            max-width: 400px; /* Un poco más angosta para login */
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            text-align: center;
        }

        /* Títulos */
        .titulo-login {
            color: var(--guinda);
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: 800;
        }
        
        .subtitulo {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
            display: block;
        }

        /* Inputs Redondos */
        .label-input {
            display: block;
            text-align: left;
            margin-left: 20px;
            margin-bottom: 5px;
            color: var(--gris-texto);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        .input-redondo {
            width: 100%;
            padding: 15px 25px;
            margin-bottom: 20px;
            border: 2px solid #f0f0f0;
            border-radius: 50px;
            font-size: 16px;
            color: var(--gris-texto);
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
            background: #fafafa;
        }

        .input-redondo:focus {
            border-color: var(--guinda);
            background: #fff;
            box-shadow: 0 0 10px rgba(119, 51, 87, 0.1);
        }

        /* Botón Guinda */
        .btn-guinda {
            background: var(--guinda);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-top: 10px;
        }

        .btn-guinda:hover {
            transform: scale(1.02);
            background: var(--guinda-dark);
        }

        /* Caja de error */
        .error-box {
            background-color: #fde8e8;
            color: #9b1c1c;
            padding: 10px;
            border-radius: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #f8b4b4;
        }

        /* Contenedor Captcha */
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .captcha-img-wrapper {
            position: relative;
            cursor: pointer;
        }
        
        .captcha-img-wrapper img {
            border-radius: 15px;
            height: 50px;
            border: 2px solid #f0f0f0;
        }
        
        .reload-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--guinda);
            opacity: 0;
            transition: 0.3s;
            font-size: 20px;
            font-weight: bold;
        }
        
        .captcha-img-wrapper:hover .reload-icon { opacity: 1; }
        .captcha-img-wrapper:hover img { opacity: 0.5; }

    </style>
</head>
<body>
    
    <div class="card-moderna">
        <h1 class="titulo-login">Acceso Institucional</h1>
        <span class="subtitulo">Alcaldía Tlalpan – Unidades Productivas</span>

        <?php if(isset($data['error']) && !empty($data['error'])): ?>
            <div class="error-box">
                <?= $data['error'] ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/Auth/validar" method="POST">
            
            <label class="label-input">Usuario</label>
            <input type="text" name="usuario" class="input-redondo" placeholder="Ej. jperez" required>

            <label class="label-input">Contraseña</label>
            <input type="password" name="password" class="input-redondo" placeholder="••••••••" required>

            <label class="label-input">Código de Seguridad</label>
            <div class="captcha-container">
                <div class="captcha-img-wrapper" onclick="recargarCaptcha()" title="Toca para recargar">
                    <img src="<?php echo URLROOT; ?>/Captcha/index" id="captcha-img" alt="Captcha">
                    <div class="reload-icon">↻</div>
                </div>
                <input type="text" name="captcha_input" class="input-redondo" style="margin-bottom: 0; text-align: center; letter-spacing: 2px; font-weight: bold; text-transform: uppercase;" placeholder="CÓDIGO" required autocomplete="off">
            </div>

            <button type="submit" class="btn-guinda">
                INGRESAR
            </button>
        </form>
    </div>

    <script>
        function recargarCaptcha() {
            document.getElementById('captcha-img').src = '<?php echo URLROOT; ?>/Captcha/index?' + Date.now();
        }
    </script>
</body>
</html>