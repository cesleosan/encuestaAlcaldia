<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['titulo']) ? $data['titulo'] : 'Censo Tlalpan'; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --guinda: #9F2241;
            --dorado: #BC955C;
            --oscuro: #2C3E50;
            --gris-claro: #F4F6F9;
            --blanco: #ffffff;
            --sombra: 0 4px 6px rgba(0,0,0,0.05);
            --radio: 25px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gris-claro);
            margin: 0;
            display: flex;
            height: 100vh;
            color: var(--oscuro);
            overflow: hidden; /* Evita doble scroll */
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background: var(--blanco);
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e1e4e8;
            height: 100vh;
            position: fixed; /* Fijo a la izquierda */
            left: 0; top: 0;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 25px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .logo-text {
            font-weight: 800; color: var(--guinda); font-size: 18px; text-transform: uppercase; line-height: 1.2;
        }
        .menu { padding: 20px; flex: 1; overflow-y: auto; }
        
        .menu-item {
            display: flex; align-items: center; padding: 12px 15px;
            color: #64748b; text-decoration: none; border-radius: 25px;
            margin-bottom: 5px; transition: 0.2s; font-weight: 500;
        }
        .menu-item:hover { background-color: #FFF5F7; color: var(--guinda); }
        .menu-item i { margin-right: 12px; width: 20px; text-align: center; }
        
        /* Clase para resaltar menú activo (Opcional: requiere lógica extra en PHP) */
        .menu-item.active { background-color: #FFF5F7; color: var(--guinda); font-weight: 700; }

        .user-profile {
            padding: 20px; border-top: 1px solid #f0f0f0;
            display: flex; align-items: center; gap: 10px;
        }
        .avatar {
            width: 40px; height: 40px; background: var(--guinda); color: white;
            border-radius: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .user-info h4 { margin: 0; font-size: 14px; }
        .user-info p { margin: 0; font-size: 12px; color: #888; text-transform: uppercase; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: 260px; /* Espacio para el sidebar fijo */
            padding: 30px;
            width: calc(100% - 260px);
            height: 100vh;
            overflow-y: auto; /* Scroll solo en el contenido */
        }

        /* Estilos Generales Dashboard */
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .page-title p { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        
        .btn-action {
            background: var(--guinda); color: white; padding: 10px 20px;
            border-radius: 25px; text-decoration: none; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer;
        }
        .btn-action:hover { background: #821c35; }

        .card {
            background: var(--blanco); padding: 20px; border-radius: var(--radio);
            box-shadow: var(--sombra); margin-bottom: 20px;
        }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card-info h3 { margin: 0; font-size: 28px; font-weight: 700; color: var(--oscuro); }
        .card-info p { margin: 0; font-size: 13px; color: #64748b; font-weight: 500; }
        .card-icon { width: 45px; height: 45px; background: #f8f9fa; border-radius: 25px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        /* Responsive */
        @media (max-width: 900px) {
            .sidebar { width: 70px; }
            .logo-text, .menu-item span, .user-info { display: none; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
            .sidebar-header { justify-content: center; padding: 15px; }
            .menu-item { justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-leaf" style="color:var(--guinda); font-size: 24px;"></i>
            <div class="logo-text">Censo<br>Tlalpan</div>
        </div>
        
        <nav class="menu">
            <a href="<?php echo URLROOT; ?>/Dashboard" class="menu-item">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            
            <?php if($_SESSION['rol'] == 'root' || $_SESSION['rol'] == 'supervisor'): ?>
            <a href="<?php echo URLROOT; ?>/Productores" class="menu-item">
                <i class="fa-solid fa-users"></i> <span>Productores</span>
            </a>
            <a href="<?php echo URLROOT; ?>/Mapa" class="menu-item">
                <i class="fa-solid fa-map-location-dot"></i> <span>Mapa GPS</span>
            </a>
            <?php endif; ?>

            <?php if($_SESSION['rol'] == 'root'): ?>
            <a href="<?php echo URLROOT; ?>/Usuarios" class="menu-item">
                <i class="fa-solid fa-user-gear"></i> <span>Usuarios</span>
            </a>
            <?php endif; ?>
        </nav>

        <div class="user-profile">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?></div>
            <div class="user-info">
                <h4><?php echo $_SESSION['nombre']; ?></h4>
                <p><?php echo $_SESSION['rol']; ?></p>
            </div>
            <a href="<?php echo URLROOT; ?>/Auth/logout" style="margin-left:auto; color:#e74c3c;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>

    <div class="main-content">