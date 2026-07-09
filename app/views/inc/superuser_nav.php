<?php
    $puedeVerSuperNav = function_exists('tc_puede_ver_accesos_usuarios')
        ? tc_puede_ver_accesos_usuarios()
        : false;

    if (!$puedeVerSuperNav) {
        return;
    }

    $rutaSuperNav = strtolower($_SERVER['REQUEST_URI'] ?? '');
    $activoSuperNav = function($segmento) use ($rutaSuperNav) {
        return strpos($rutaSuperNav, strtolower($segmento)) !== false ? ' active' : '';
    };
?>

<nav class="tc-super-nav-card" aria-label="Navegacion principal de superusuario">
    <div class="tc-super-nav-copy">
        <span><i class="fa-solid fa-user-shield"></i> Superusuario</span>
        <small>Acceso operativo de aGuillen</small>
    </div>
    <div class="tc-super-nav-actions">
        <a href="<?php echo URLROOT; ?>/Dashboard/index" class="tc-super-nav-link<?php echo $activoSuperNav('/dashboard'); ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo URLROOT; ?>/Captura/index" class="tc-super-nav-link<?php echo $activoSuperNav('/captura'); ?>">
            <i class="fa-solid fa-folder-open"></i>
            <span>Captura</span>
        </a>
        <a href="<?php echo URLROOT; ?>/Usuarios" class="tc-super-nav-link<?php echo $activoSuperNav('/usuarios'); ?>">
            <i class="fa-solid fa-user-clock"></i>
            <span>Accesos</span>
        </a>
    </div>
</nav>
