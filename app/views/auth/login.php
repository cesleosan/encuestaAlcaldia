<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#773357">
    <title>Tierra con Corazón - Acceso</title>
    <style>
        :root {
            color-scheme: only light;
            --guinda: #773357;
            --guinda-dark: #5a2540;
            --gris-texto: #4a4a4a;
            --blanco: #ffffff;
        }

        * { box-sizing: border-box; }

        html {
            color-scheme: light !important;
            background: var(--guinda);
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gris-texto);
            background: linear-gradient(135deg, var(--guinda), var(--guinda-dark)) !important;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-moderna {
            width: min(100%, 420px);
            padding: 46px 40px;
            border-radius: 25px;
            background: var(--blanco) !important;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .3);
            text-align: center;
            animation: fadeIn .55s ease-out;
        }

        .logo-container { margin-bottom: 22px; }

        .logo-img {
            width: auto;
            max-width: 180px;
            max-height: 150px;
            transition: transform .25s ease;
        }

        .logo-img:hover { transform: scale(1.03); }

        .brand-title {
            margin: 0 0 32px;
            color: var(--guinda);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .label-input {
            display: block;
            margin: 0 0 8px 15px;
            color: var(--gris-texto);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .input-redondo {
            width: 100%;
            min-height: 49px;
            padding: 13px 20px;
            border: 2px solid #eeeeee;
            border-radius: 25px;
            outline: none;
            color: var(--gris-texto) !important;
            background: #fdfdfd !important;
            font: inherit;
            font-size: 15px;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
            color-scheme: light !important;
        }

        .input-redondo:focus {
            border-color: var(--guinda);
            background: #ffffff !important;
            box-shadow: 0 5px 15px rgba(119, 51, 87, .12);
        }

        .input-redondo::placeholder {
            color: #989898;
            opacity: 1;
        }

        .captcha-wrapper {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px dashed #dddddd;
            border-radius: 25px;
            background: #f9f9f9 !important;
        }

        .captcha-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .captcha-img-wrapper {
            position: relative;
            flex-shrink: 0;
            cursor: pointer;
        }

        .captcha-img-wrapper img {
            display: block;
            width: 150px;
            height: 48px;
            object-fit: cover;
            border: 1px solid #eeeeee;
            border-radius: 25px;
            background: #ffffff;
        }

        .reload-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--guinda);
            background: rgba(255, 255, 255, .92);
            opacity: 0;
            transform: translate(-50%, -50%);
            transition: opacity .2s ease;
        }

        .captcha-img-wrapper:hover .reload-icon { opacity: 1; }

        .captcha-code {
            width: 120px;
            padding-inline: 10px;
            text-align: center;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .btn-guinda {
            width: 100%;
            min-height: 50px;
            padding: 15px;
            border: 0;
            border-radius: 25px;
            color: #ffffff !important;
            background: var(--guinda) !important;
            box-shadow: 0 10px 20px rgba(119, 51, 87, .22);
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .btn-guinda:hover {
            background: var(--guinda-dark) !important;
            box-shadow: 0 14px 24px rgba(119, 51, 87, .3);
            transform: translateY(-1px);
        }

        .error-box {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-left: 4px solid #f56565;
            border-radius: 14px;
            color: #c53030;
            background: #fff5f5 !important;
            font-size: 13px;
            text-align: left;
        }

        @media (prefers-color-scheme: dark) {
            .card-moderna,
            .input-redondo,
            .captcha-wrapper {
                color: var(--gris-texto) !important;
                background-color: #ffffff !important;
            }

            .captcha-wrapper { background-color: #f9f9f9 !important; }
            .input-redondo { background-color: #fdfdfd !important; }
        }

        @media (max-width: 520px) {
            body { padding: 14px; }
            .card-moderna { padding: 36px 22px; }
            .captcha-container { flex-direction: column; }
            .captcha-img-wrapper,
            .captcha-img-wrapper img,
            .captcha-code { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="card-moderna">
        <div class="logo-container">
            <img
                src="<?php echo URLROOT; ?>/logos/Logo AT Vertical guinda 100 PX.png"
                class="logo-img"
                alt="Tierra con Corazón"
            >
        </div>

        <h1 class="brand-title">Tierra con Corazón</h1>

        <?php if (isset($data['error']) && !empty($data['error'])): ?>
            <div class="error-box">
                <strong>Error:</strong>
                <?php echo htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/Auth/validar" method="POST">
            <div class="form-group">
                <label class="label-input" for="usuario">Usuario</label>
                <input
                    id="usuario"
                    type="text"
                    name="usuario"
                    class="input-redondo"
                    placeholder="Nombre de usuario"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label class="label-input" for="password">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="input-redondo"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="captcha-wrapper">
                <label class="label-input" for="captcha_input" style="margin-left:0;text-align:center;">
                    Verificación de seguridad
                </label>
                <div class="captcha-container">
                    <div class="captcha-img-wrapper" onclick="recargarCaptcha()" title="Generar otro código">
                        <img src="<?php echo URLROOT; ?>/Captcha/index" id="captcha-img" alt="Código de seguridad">
                        <div class="reload-icon">↻</div>
                    </div>
                    <input
                        id="captcha_input"
                        type="text"
                        name="captcha_input"
                        class="input-redondo captcha-code"
                        placeholder="Código"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-guinda">Ingresar al sistema</button>
        </form>
    </main>

    <script>
        function recargarCaptcha() {
            document.getElementById('captcha-img').src =
                '<?php echo URLROOT; ?>/Captcha/index?' + Date.now();
        }
    </script>
</body>
</html>
