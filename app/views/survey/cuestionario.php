<?php require APPROOT . '/views/layout/header.php'; ?>

<?php
// Preparamos los datos para JS
$bancoJson = htmlspecialchars(json_encode($banco), ENT_QUOTES, 'UTF-8');
?>

<div id="survey-app" class="card-moderna card-xl" data-banco='<?php echo json_encode($banco); ?>'>
    
    <div id="progress-container" style="background: #f0f0f0; height: 10px; border-radius: 10px; margin-bottom: 25px; overflow: hidden;">
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
    const URLROOT = "<?php echo URLROOT; ?>"; // 🔥 ESTA ES LA LÍNEA QUE FALTA
    const TECNICO_LOGUEADO = "<?php echo $data['nombre_tecnico']; ?>";
    const FOLIO_AUTO = "<?php echo $data['folio_automatico']; ?>";
    const COLONIAS_PREVIEW = <?php echo json_encode($data['colonias_iniciales']); ?>;
</script>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/survey-engine.js"></script>

</div> </body>
</html>