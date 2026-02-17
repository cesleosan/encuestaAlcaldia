<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tierra con Corazón - Acceso</title>
    <style>
        :root {
            --guinda: #773357;       
            --guinda-dark: #5a2540;
            --gris-texto: #4A4A4A;   
            --blanco: #ffffff;
            --sombra: rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, var(--guinda) 0%, var(--guinda-dark) 100%);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--gris-texto);
        }

        /* Animación de entrada */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-moderna {
            background: var(--blanco);
            width: 90%;
            max-width: 420px;
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        /* Contenedor del Logo */
        .logo-container {
            margin-bottom: 25px;
        }

        .logo-img {
            max-width: 180px; /* Ajustado para que el logo vertical luzca */
            height: auto;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .brand-title {
            color: var(--guinda);
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .subtitulo {
            color: #888;
            font-size: 14px;
            margin-bottom: 35px;
            display: block;
            font-weight: 400;
        }

        /* Formulario y Inputs */
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .label-input {
            display: block;
            margin-left: 15px;
            margin-bottom: 8px;
            color: var(--gris-texto);
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-redondo {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #eee;
            border-radius: 25px;
            font-size: 15px;
            color: var(--gris-texto);
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
            background: #fdfdfd;
        }

        .input-redondo:focus {
            border-color: var(--guinda);
            background: #fff;
            box-shadow: 0 5px 15px rgba(119, 51, 87, 0.1);
        }

        /* Captcha Mejorado */
        .captcha-wrapper {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 25px;
            border: 1px dashed #ddd;
            margin-bottom: 25px;
        }

        .captcha-container {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }
        
        .captcha-img-wrapper {
            position: relative;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .captcha-img-wrapper img {
            border-radius: 25px;
            height: 48px;
            border: 1px solid #eee;
        }
        
        .reload-icon {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.9);
            width: 30px; height: 30px;
            border-radius: 25px;
            display: flex; align-items: center; justify-content: center;
            color: var(--guinda);
            opacity: 0;
            transition: 0.3s;
            font-size: 18px;
        }
        
        .captcha-img-wrapper:hover .reload-icon { opacity: 1; }

        /* Botón Principal */
        .btn-guinda {
            background: var(--guinda);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(119, 51, 87, 0.2);
        }

        .btn-guinda:hover {
            background: var(--guinda-dark);
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(119, 51, 87, 0.3);
        }

        .error-box {
            background-color: #fff5f5;
            color: #c53030;
            padding: 12px;
            border-radius: 25px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid #f56565;
            text-align: left;
        }
    </style>
</head>
<body>
    
    <div class="card-moderna">
        <div class="logo-container">
            <img src="<?php echo URLROOT; ?>/logos/Logo AT Vertical guinda 100 PX.png" 
                 class="logo-img" 
                 alt="Tierra con Corazón Logo">
        </div>

        <h1 class="brand-title">Tierra con Corazón</h1>

        <?php if(isset($data['error']) && !empty($data['error'])): ?>
            <div class="error-box">
                <strong>Error:</strong> <?= $data['error'] ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/Auth/validar" method="POST">
            
            <div class="form-group">
                <label class="label-input">Usuario</label>
                <input type="text" name="usuario" class="input-redondo" placeholder="Nombre de usuario" required autofocus>
            </div>

            <div class="form-group">
                <label class="label-input">Contraseña</label>
                <input type="password" name="password" class="input-redondo" placeholder="••••••••" required>
            </div>

            <div class="captcha-wrapper">
                <label class="label-input" style="text-align:center; margin-left:0;">Verificación de Seguridad</label>
                <div class="captcha-container">
                    <div class="captcha-img-wrapper" onclick="recargarCaptcha()" title="Recargar código">
                        <img src="<?php echo URLROOT; ?>/Captcha/index" id="captcha-img" alt="Captcha">
                        <div class="reload-icon">↻</div>
                    </div>
                    <input type="text" name="captcha_input" class="input-redondo" 
                           style="width: 120px; text-align: center; letter-spacing: 3px; font-weight: 800; text-transform: uppercase;" 
                           placeholder="CÓDIGO" required autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn-guinda">
                INGRESAR AL SISTEMA
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