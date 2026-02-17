<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<div class="top-header">
    <div class="page-title">
        <h1>Gestión de Usuarios</h1>
        <p>Administración de accesos y roles del sistema</p>
    </div>
    <button class="btn-action">
        <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
    </button>
</div>

<div class="kpi-grid">
    <div class="card">
        <div class="card-info"><p>Total Usuarios</p><h3>4</h3></div>
        <div class="card-icon" style="color:#2ecc71;"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="card">
        <div class="card-info"><p>Encuestadores</p><h3>2</h3></div>
        <div class="card-icon" style="color:#3498db;"><i class="fa-solid fa-mobile-screen"></i></div>
    </div>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse;">
        <thead style="background:#f8f9fa;">
            <tr>
                <th style="padding:15px; text-align:left; color:#64748b;">USUARIO</th>
                <th style="padding:15px; text-align:left; color:#64748b;">NOMBRE</th>
                <th style="padding:15px; text-align:left; color:#64748b;">ROL</th>
                <th style="padding:15px; text-align:left; color:#64748b;">ÚLTIMO ACCESO</th>
                <th style="padding:15px; text-align:center; color:#64748b;">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['lista'] as $u): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:15px; font-weight:bold;"><?php echo $u['usuario']; ?></td>
                <td style="padding:15px;"><?php echo $u['nombre']; ?></td>
                <td style="padding:15px;">
                    <?php 
                        $bg = ($u['rol']=='root') ? '#e74c3c' : (($u['rol']=='supervisor') ? '#f39c12' : '#3498db');
                    ?>
                    <span style="background:<?php echo $bg; ?>; color:white; padding:4px 10px; border-radius:12px; font-size:11px; text-transform:uppercase;">
                        <?php echo $u['rol']; ?>
                    </span>
                </td>
                <td style="padding:15px; color:#888;"><?php echo $u['ultimo_acceso']; ?></td>
                <td style="padding:15px; text-align:center;">
                    <button style="border:none; background:none; color:#f39c12; cursor:pointer;"><i class="fa-solid fa-pen"></i></button>
                    <button style="border:none; background:none; color:#e74c3c; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>