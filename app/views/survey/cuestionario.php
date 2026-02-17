<?php require APPROOT . '/views/layout/header.php'; ?>

<?php
// Preparamos los datos para JS
$bancoJson = htmlspecialchars(json_encode($banco), ENT_QUOTES, 'UTF-8');
?>

<style>

    :root {
        --radio-global: 25px;
        --guinda: #773357;
    }

    /* Estilo de la Barra Superior */
    .top-nav-survey {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .brand-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* Logo Horizontal en Nav */
    .logo-nav-horizontal {
        height: 40px;
        width: auto;
    }

    /* Píldora de Usuario con Radio 25px */
    .user-pill {
        display: flex;
        align-items: center;
        background: #fdf2f7;
        padding: 8px 20px;
        border-radius: var(--radio-global); /* 25px */
        border: 1px solid rgba(119, 51, 87, 0.1);
    }

    .status-indicator {
        height: 10px;
        width: 10px;
        background-color: #28a745;
        border-radius: 50%;
        margin-right: 10px;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
    }

    .user-name-text {
        font-size: 13px;
        font-weight: 700;
        color: var(--guinda);
    }

    /* Botón Salir con Radio 25px */
    .btn-exit-modern {
        background: #ffffff;
        color: #666;
        border: 1px solid #ddd;
        padding: 8px 20px;
        border-radius: var(--radio-global); /* 25px */
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-exit-modern:hover {
        background: var(--guinda);
        color: white;
        border-color: var(--guinda);
        box-shadow: 0 8px 15px rgba(119, 51, 87, 0.2);
        transform: translateY(-2px);
    }

    /* Barra de Progreso con Radio 25px */
    .progress-minimal {
        background: #f0f0f0; 
        height: 12px; 
        border-radius: var(--radio-global); /* 25px */
        margin-bottom: 35px; 
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Marca de Agua (El logo que me pasaste) */
    .watermark-gold {
        position: fixed;
        bottom: 25px;
        right: 25px;
        opacity: 0.15;
        width: 130px;
        pointer-events: none;
        z-index: -1;
        /* Aplicamos también radio aquí por si tuviera fondo */
        border-radius: var(--radio-global); /* 25px */
    }

    /* Tarjeta Principal con Radio 25px */
    .card-moderna {
        border-radius: var(--radio-global) !important; /* 25px */
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
</style>

<img src="<?php echo URLROOT; ?>/logos/Logo AT Vertical N 100PX.png" 
     class="watermark-gold" 
     alt="Tierra con Corazón Logo">

<div id="survey-app" class="card-moderna card-xl" data-banco='<?php echo json_encode($banco); ?>'>
    
    <div class="top-nav-survey">
        <div class="brand-section">
            <img src="<?php echo URLROOT; ?>/logos/Logo AT Vertical guinda 100 PX.png"
                 class="logo-nav-horizontal" 
                 alt="Tierra con Corazón">
        </div>

        <div style="display: flex; gap: 10px;">
            <div class="user-pill">
                <span class="status-indicator"></span>
                <span class="user-name-text"><?php echo $data['nombre_tecnico']; ?></span>
            </div>

            <a href="<?php echo URLROOT; ?>/Auth/logout" 
               class="btn-exit-modern"
               onclick="return confirm('¿Seguro que quieres cerrar sesión?')">
               <span>SALIR</span> <i>⏻</i>
            </a>
        </div>
    </div>

    <div id="progress-container" class="progress-minimal">
        <div id="progress-bar" style="background: var(--guinda); width: 0%; height: 100%; transition: width 0.5s ease;"></div>
    </div>

    <div class="card-body">
        <h2 id="pregunta-titulo" class="titulo-login" style="font-size: 24px; margin-bottom: 30px;"></h2>
        
        <div id="opciones-container">
            </div>
    </div>
</div>

<script>
    // Puente PHP -> JS
    const URLROOT = "<?php echo URLROOT; ?>"; 
    const TECNICO_LOGUEADO = "<?php echo $data['nombre_tecnico']; ?>";
    const FOLIO_AUTO = "<?php echo $data['folio_automatico']; ?>";
    const COLONIAS_PREVIEW = <?php echo json_encode($data['colonias_iniciales']); ?>;
</script>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/survey-engine.js"></script>

</body>
</html>