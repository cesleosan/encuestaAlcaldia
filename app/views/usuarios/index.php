<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<header class="top-header">
    <div class="page-title">
        <h1>Gestión de usuarios</h1>
        <p>Administración de accesos y roles del sistema</p>
    </div>
    <button class="btn-action"><i class="fa-solid fa-user-plus"></i>Nuevo usuario</button>
</header>

<div class="kpi-grid">
    <article class="card d-flex align-items-center justify-content-between">
        <div class="card-info"><p>Total de usuarios</p><h3><?php echo count($data['lista']); ?></h3></div>
        <div class="card-icon text-success"><i class="fa-solid fa-users"></i></div>
    </article>
    <article class="card d-flex align-items-center justify-content-between">
        <div class="card-info"><p>Roles configurados</p><h3><?php echo count(array_unique(array_column($data['lista'], 'rol'))); ?></h3></div>
        <div class="card-icon text-guinda"><i class="fa-solid fa-shield-halved"></i></div>
    </article>
</div>

<section class="card p-0 overflow-hidden">
    <div class="tc-toolbar">
        <div>
            <h5 class="fw-bold mb-1">Directorio de accesos</h5>
            <small class="text-muted">Usuarios visibles en este módulo</small>
        </div>
        <div class="position-relative" style="width:min(100%,360px);">
            <i class="fa-solid fa-magnifying-glass position-absolute" style="left:15px;top:14px;color:#94a3b8;"></i>
            <input id="buscarUsuario" class="form-control ps-5" placeholder="Buscar usuario, nombre o rol">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tablaUsuarios" style="min-width:760px;">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Último acceso</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['lista'] as $u): ?>
                <?php $textoBusqueda = strtolower($u['usuario'] . ' ' . $u['nombre'] . ' ' . $u['rol']); ?>
                <tr data-search="<?php echo htmlspecialchars($textoBusqueda, ENT_QUOTES, 'UTF-8'); ?>">
                    <td><span class="font-monospace fw-bold"><?php echo htmlspecialchars($u['usuario'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge badge-comite"><?php echo htmlspecialchars(strtoupper($u['rol']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['ultimo_acceso'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" title="Editar"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Desactivar"><i class="fa-solid fa-user-slash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.getElementById('buscarUsuario').addEventListener('input', function() {
    const texto = this.value.toLowerCase().trim();
    document.querySelectorAll('#tablaUsuarios tbody tr').forEach(fila => {
        fila.style.display = fila.dataset.search.includes(texto) ? '' : 'none';
    });
});
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
