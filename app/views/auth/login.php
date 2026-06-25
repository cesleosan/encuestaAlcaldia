<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#773357">
    <title>Tierra con Corazón · Acceso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --guinda:#773357;
            --guinda-dark:#51213a;
            --guinda-soft:#f7edf2;
            --dorado:#b08b4f;
            --texto:#263142;
            --muted:#727b89;
            --borde:#e2e6ec;
        }
        * { box-sizing:border-box; }
        body {
            margin:0;
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
            color:var(--texto);
            font-family:'Montserrat',sans-serif;
            background:
                radial-gradient(circle at 15% 15%, rgba(176,139,79,.17), transparent 23rem),
                radial-gradient(circle at 90% 5%, rgba(255,255,255,.12), transparent 28rem),
                linear-gradient(135deg,var(--guinda-dark),var(--guinda));
        }
        .login-shell {
            width:min(100%,1020px);
            min-height:610px;
            display:grid;
            grid-template-columns:1.05fr .95fr;
            overflow:hidden;
            border:1px solid rgba(255,255,255,.15);
            border-radius:28px;
            background:#fff;
            box-shadow:0 30px 80px rgba(25,12,22,.32);
        }
        .login-brand {
            position:relative;
            overflow:hidden;
            padding:55px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            color:#fff;
            background:
                linear-gradient(145deg,rgba(81,33,58,.94),rgba(119,51,87,.9)),
                url('<?php echo URLROOT; ?>/logos/Logo AT Vertical guinda 100 PX.png') center/cover;
        }
        .login-brand::after {
            content:"";
            position:absolute;
            width:340px;height:340px;
            right:-180px;bottom:-170px;
            border:1px solid rgba(255,255,255,.16);
            border-radius:50%;
            box-shadow:0 0 0 55px rgba(255,255,255,.035),0 0 0 110px rgba(255,255,255,.025);
        }
        .brand-mark {
            width:58px;height:58px;
            display:grid;place-items:center;
            border-radius:18px;
            color:var(--guinda);
            background:#fff;
            font-size:1.45rem;
            box-shadow:0 10px 25px rgba(0,0,0,.18);
        }
        .brand-copy { position:relative;z-index:1; }
        .brand-copy h1 { margin:22px 0 10px;font-size:clamp(2rem,4vw,3.25rem);line-height:1.02;letter-spacing:-.055em; }
        .brand-copy p { max-width:390px;margin:0;color:rgba(255,255,255,.78);line-height:1.7; }
        .brand-foot { position:relative;z-index:1;font-size:.72rem;color:rgba(255,255,255,.62); }
        .login-panel { padding:52px clamp(30px,5vw,62px);display:flex;flex-direction:column;justify-content:center; }
        .login-panel h2 { margin:0;color:var(--guinda);font-size:1.75rem;font-weight:800;letter-spacing:-.035em; }
        .login-panel > p { margin:8px 0 30px;color:var(--muted);font-size:.9rem;line-height:1.6; }
        .field { margin-bottom:18px; }
        .field label { display:block;margin:0 0 7px;font-size:.69rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase; }
        .input-wrap { position:relative; }
        .input-wrap i { position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#929aa7; }
        .input {
            width:100%;height:48px;padding:0 15px 0 43px;
            border:1px solid var(--borde);border-radius:12px;
            color:var(--texto);background:#fbfcfd;font:inherit;outline:0;
            transition:.18s ease;
        }
        .input:focus { border-color:rgba(119,51,87,.65);background:#fff;box-shadow:0 0 0 4px rgba(119,51,87,.09); }
        .captcha-box { padding:14px;border:1px solid var(--borde);border-radius:14px;background:#fafbfc; }
        .captcha-row { display:flex;align-items:center;gap:10px; }
        .captcha-image { position:relative;flex:1;min-width:0;cursor:pointer; }
        .captcha-image img { display:block;width:100%;height:48px;object-fit:cover;border-radius:10px;border:1px solid var(--borde); }
        .captcha-image span { position:absolute;right:8px;top:8px;width:32px;height:32px;display:grid;place-items:center;border-radius:9px;color:var(--guinda);background:rgba(255,255,255,.92); }
        .captcha-code { width:135px;padding:0 10px;text-align:center;letter-spacing:.14em;font-weight:800;text-transform:uppercase; }
        .submit {
            width:100%;height:50px;margin-top:22px;border:0;border-radius:12px;
            color:#fff;background:linear-gradient(120deg,var(--guinda),var(--guinda-dark));
            font:700 .83rem 'Montserrat',sans-serif;letter-spacing:.04em;text-transform:uppercase;cursor:pointer;
            box-shadow:0 10px 22px rgba(119,51,87,.22);transition:.18s ease;
        }
        .submit:hover { transform:translateY(-1px);box-shadow:0 14px 26px rgba(119,51,87,.28); }
        .error-box { display:flex;gap:10px;align-items:flex-start;margin-bottom:20px;padding:12px 14px;border:1px solid #f2c4c9;border-radius:12px;color:#a82f3c;background:#fff4f5;font-size:.8rem; }
        @media(max-width:780px) {
            body { padding:14px;align-items:start; }
            .login-shell { min-height:0;grid-template-columns:1fr; }
            .login-brand { min-height:230px;padding:30px; }
            .brand-copy h1 { margin-top:16px; }
            .brand-foot { display:none; }
            .login-panel { padding:34px 25px; }
        }
        @media(max-width:420px) {
            .captcha-row { align-items:stretch;flex-direction:column; }
            .captcha-code { width:100%; }
        }
    </style>
</head>
<body>
<main class="login-shell">
    <section class="login-brand">
        <div class="brand-copy">
            <div class="brand-mark"><i class="fas fa-seedling"></i></div>
            <h1>Tierra con<br>Corazón</h1>
            <p>Plataforma de levantamiento, validación y seguimiento de expedientes productivos.</p>
        </div>
        <div class="brand-foot">Alcaldía Tlalpan · Acceso institucional</div>
    </section>

    <section class="login-panel">
        <h2>Bienvenido</h2>
        <p>Ingresa tus credenciales para continuar al módulo asignado.</p>

        <?php if(isset($data['error']) && !empty($data['error'])): ?>
            <div class="error-box"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?></span></div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/Auth/validar" method="POST">
            <div class="field">
                <label for="usuario">Usuario</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input id="usuario" type="text" name="usuario" class="input" placeholder="Nombre de usuario" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input id="password" type="password" name="password" class="input" placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                </div>
            </div>

            <div class="field">
                <label for="captcha_input">Verificación de seguridad</label>
                <div class="captcha-box">
                    <div class="captcha-row">
                        <div class="captcha-image" onclick="recargarCaptcha()" title="Generar otro código">
                            <img src="<?php echo URLROOT; ?>/Captcha/index" id="captcha-img" alt="Código de seguridad">
                            <span><i class="fas fa-rotate"></i></span>
                        </div>
                        <input id="captcha_input" type="text" name="captcha_input" class="input captcha-code" placeholder="Código" required autocomplete="off" maxlength="8">
                    </div>
                </div>
            </div>

            <button type="submit" class="submit"><i class="fas fa-arrow-right-to-bracket me-2"></i>Ingresar al sistema</button>
        </form>
    </section>
</main>

<script>
function recargarCaptcha() {
    document.getElementById('captcha-img').src = '<?php echo URLROOT; ?>/Captcha/index?' + Date.now();
}
</script>
</body>
</html>
