<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>
<?php
    $usuarios = $data['lista'] ?? [];
    $resumen = $data['resumen'] ?? ['total' => 0, 'online' => 0, 'tierra' => 0, 'activos' => 0];

    $esc = function($valor) {
        return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
    };

    $fecha = function($valor) {
        if (empty($valor)) return 'Sin registro';
        $ts = strtotime((string)$valor);
        return $ts ? date('d/m/Y H:i', $ts) : (string)$valor;
    };

    $dispositivo = function($ua) {
        $ua = strtolower((string)$ua);
        if ($ua === '') return 'Sin dato';
        if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false) return 'Movil';
        if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) return 'Tablet';
        return 'Escritorio';
    };
?>

<header class="tc-hero mb-4">
    <div class="tc-hero-copy">
        <span class="tc-eyebrow"><i class="fa-solid fa-shield-halved"></i> Vista privada de Adan</span>
        <h1>Control de accesos</h1>
        <p>Monitoreo de usuarios, modulos, ultimo acceso y actividad reciente del sistema.</p>
    </div>
    <div class="tc-hero-actions">
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
            <i class="fa-solid fa-rotate me-1"></i>Actualizar
        </button>
    </div>
</header>

<section class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-primary">
            <div class="tc-kpi-icon"><i class="fa-solid fa-users"></i></div>
            <div><span>Total usuarios</span><strong><?php echo (int)$resumen['total']; ?></strong><small>Registrados en sistema</small></div>
        </article>
    </div>
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-success">
            <div class="tc-kpi-icon"><i class="fa-solid fa-signal"></i></div>
            <div><span>Online</span><strong><?php echo (int)$resumen['online']; ?></strong><small>Actividad ultimos 5 min</small></div>
        </article>
    </div>
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-info">
            <div class="tc-kpi-icon"><i class="fa-solid fa-seedling"></i></div>
            <div><span>Modulo Tierra</span><strong><?php echo (int)$resumen['tierra']; ?></strong><small>Usuarios asignados</small></div>
        </article>
    </div>
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-warning">
            <div class="tc-kpi-icon"><i class="fa-solid fa-user-check"></i></div>
            <div><span>Activos</span><strong><?php echo (int)$resumen['activos']; ?></strong><small>Con acceso permitido</small></div>
        </article>
    </div>
</section>

<section class="card p-0 overflow-hidden">
    <div class="tc-toolbar">
        <div>
            <h5 class="fw-bold text-guinda mb-1"><i class="fa-solid fa-user-clock me-2"></i>Monitor de usuarios</h5>
            <small class="text-muted">Este modulo solo esta disponible para aGuillen.</small>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-end" style="max-width:760px;width:100%;">
            <div class="position-relative flex-grow-1" style="min-width:260px;">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left:15px;top:14px;color:#94a3b8;"></i>
                <input id="buscarUsuario" class="form-control ps-5" placeholder="Buscar usuario, nombre, rol, modulo o IP">
            </div>
            <select id="filtroModulo" class="form-select" style="max-width:180px;">
                <option value="">Todos los modulos</option>
                <option value="TIERRA">TIERRA</option>
                <option value="VUT">VUT</option>
            </select>
            <select id="filtroEstado" class="form-select" style="max-width:170px;">
                <option value="">Todos</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaUsuarios" style="min-width:1180px;">
            <thead>
                <tr>
                    <th class="ps-3">Estado</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Modulo</th>
                    <th>Ultimo acceso</th>
                    <th>Ultima actividad</th>
                    <th>IP</th>
                    <th>Dispositivo</th>
                    <th class="text-center">Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                    <?php
                        $online = ((int)($u->sesiones_activas ?? 0) > 0);
                        $activo = ((int)($u->activo ?? 0) === 1);
                        $modulo = strtoupper((string)($u->modulo ?? ''));
                        $estadoTexto = $online ? 'online' : 'offline';
                        $activoTexto = $activo ? 'activo' : 'inactivo';
                        $textoBusqueda = strtolower(implode(' ', [
                            $u->usuario ?? '',
                            $u->nombre_completo ?? '',
                            $u->rol ?? '',
                            $u->modulo ?? '',
                            $u->ip ?? '',
                            $estadoTexto,
                            $activoTexto
                        ]));
                    ?>
                    <tr
                        data-search="<?php echo $esc($textoBusqueda); ?>"
                        data-modulo="<?php echo $esc($modulo); ?>"
                        data-estado="<?php echo $esc($estadoTexto); ?>"
                        data-activo="<?php echo $esc($activoTexto); ?>"
                    >
                        <td class="ps-3">
                            <?php if($online): ?>
                                <span class="badge rounded-pill text-bg-success"><i class="fa-solid fa-circle me-1" style="font-size:.45rem;"></i>Online</span>
                            <?php else: ?>
                                <span class="badge rounded-pill text-bg-light border text-muted"><i class="fa-regular fa-circle me-1" style="font-size:.45rem;"></i>Offline</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="font-monospace fw-bold text-guinda"><?php echo $esc($u->usuario); ?></span></td>
                        <td>
                            <div class="fw-semibold"><?php echo $esc($u->nombre_completo); ?></div>
                            <small class="text-muted">ID <?php echo (int)($u->id ?? 0); ?></small>
                        </td>
                        <td><span class="badge badge-comite"><?php echo $esc(strtoupper($u->rol ?? '')); ?></span></td>
                        <td><span class="badge text-bg-light border"><?php echo $esc($modulo ?: 'SIN MODULO'); ?></span></td>
                        <td class="text-muted"><?php echo $esc($fecha($u->ultimo_acceso ?? $u->ultimo_inicio ?? null)); ?></td>
                        <td>
                            <div><?php echo $esc($fecha($u->ultima_actividad ?? null)); ?></div>
                            <?php if(!empty($u->estado_sesion)): ?>
                                <small class="text-muted">Sesion <?php echo $esc($u->estado_sesion); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="font-monospace small"><?php echo $esc($u->ip ?: 'Sin dato'); ?></span></td>
                        <td><?php echo $esc($dispositivo($u->user_agent ?? '')); ?></td>
                        <td class="text-center">
                            <?php if($activo): ?>
                                <span class="badge text-bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer class="tc-table-footer d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <span id="usuariosInfo"><?php echo count($usuarios); ?> usuario(s) visibles</span>
        <small class="text-muted"><i class="fa-solid fa-lock me-1"></i>Consulta operativa sin botones de edicion.</small>
    </footer>
</section>

<script>
const inputUsuario = document.getElementById('buscarUsuario');
const filtroModulo = document.getElementById('filtroModulo');
const filtroEstado = document.getElementById('filtroEstado');
const filasUsuarios = Array.from(document.querySelectorAll('#tablaUsuarios tbody tr'));
const usuariosInfo = document.getElementById('usuariosInfo');

function filtrarUsuarios() {
    const texto = inputUsuario.value.toLowerCase().trim();
    const modulo = filtroModulo.value;
    const estado = filtroEstado.value;
    let visibles = 0;

    filasUsuarios.forEach(fila => {
        const pasaTexto = !texto || fila.dataset.search.includes(texto);
        const pasaModulo = !modulo || fila.dataset.modulo === modulo;
        const pasaEstado = !estado || fila.dataset.estado === estado || fila.dataset.activo === estado;
        const visible = pasaTexto && pasaModulo && pasaEstado;
        fila.style.display = visible ? '' : 'none';
        if (visible) visibles++;
    });

    usuariosInfo.textContent = `${visibles} usuario(s) visibles`;
}

inputUsuario.addEventListener('input', filtrarUsuarios);
filtroModulo.addEventListener('change', filtrarUsuarios);
filtroEstado.addEventListener('change', filtrarUsuarios);
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
