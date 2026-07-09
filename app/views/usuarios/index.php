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

    $puedeVerAccesosUsuarios = function_exists('tc_puede_ver_accesos_usuarios')
        ? tc_puede_ver_accesos_usuarios()
        : false;
?>

<header class="tc-hero mb-4">
    <div class="tc-hero-copy">
        <span class="tc-eyebrow"><i class="fa-solid fa-shield-halved"></i> Vista privada de Adan</span>
        <h1>Control de accesos</h1>
        <p>Monitoreo de usuarios, modulos, ultimo acceso y actividad reciente del sistema.</p>
    </div>
    <div class="tc-hero-actions">
        <?php if($puedeVerAccesosUsuarios): ?>
        <a href="<?php echo URLROOT; ?>/Dashboard/index" class="btn btn-outline-secondary">
            <i class="fa-solid fa-chart-pie me-1"></i>Dashboard
        </a>
        <a href="<?php echo URLROOT; ?>/Captura/index" class="btn btn-guinda">
            <i class="fa-solid fa-folder-open me-1"></i>Captura
        </a>
        <?php endif; ?>
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
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <small class="text-muted"><i class="fa-solid fa-lock me-1"></i>Consulta operativa sin botones de edicion.</small>
            <nav aria-label="Paginacion usuarios">
                <ul class="pagination pagination-sm mb-0" id="usuariosPaginacion"></ul>
            </nav>
        </div>
    </footer>
</section>

<script>
const inputUsuario = document.getElementById('buscarUsuario');
const filtroModulo = document.getElementById('filtroModulo');
const filtroEstado = document.getElementById('filtroEstado');
const filasUsuarios = Array.from(document.querySelectorAll('#tablaUsuarios tbody tr'));
const usuariosInfo = document.getElementById('usuariosInfo');
const usuariosPaginacion = document.getElementById('usuariosPaginacion');
const pageSizeUsuarios = 10;
let paginaUsuarios = 1;

function filtrarUsuarios() {
    const texto = inputUsuario.value.toLowerCase().trim();
    const modulo = filtroModulo.value;
    const estado = filtroEstado.value;

    const filtradas = filasUsuarios.filter(fila => {
        const pasaTexto = !texto || fila.dataset.search.includes(texto);
        const pasaModulo = !modulo || fila.dataset.modulo === modulo;
        const pasaEstado = !estado || fila.dataset.estado === estado || fila.dataset.activo === estado;
        return pasaTexto && pasaModulo && pasaEstado;
    });

    const totalPaginas = Math.max(1, Math.ceil(filtradas.length / pageSizeUsuarios));
    if (paginaUsuarios > totalPaginas) paginaUsuarios = totalPaginas;

    const inicio = (paginaUsuarios - 1) * pageSizeUsuarios;
    const fin = inicio + pageSizeUsuarios;
    const paginaActual = filtradas.slice(inicio, fin);

    filasUsuarios.forEach(fila => {
        fila.style.display = paginaActual.includes(fila) ? '' : 'none';
    });

    usuariosInfo.textContent = filtradas.length
        ? `Mostrando ${inicio + 1}-${Math.min(fin, filtradas.length)} de ${filtradas.length} usuario(s)`
        : '0 usuario(s) visibles';

    renderPaginacionUsuarios(totalPaginas);
}

function renderPaginacionUsuarios(totalPaginas) {
    usuariosPaginacion.innerHTML = '';
    if (totalPaginas <= 1) return;

    const crearItem = (label, page, disabled = false, active = false) => {
        const li = document.createElement('li');
        li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'page-link';
        button.innerHTML = label;
        button.disabled = disabled;
        button.addEventListener('click', () => {
            paginaUsuarios = page;
            filtrarUsuarios();
        });
        li.appendChild(button);
        usuariosPaginacion.appendChild(li);
    };

    crearItem('<i class="fa-solid fa-chevron-left"></i>', Math.max(1, paginaUsuarios - 1), paginaUsuarios === 1);

    const inicio = Math.max(1, paginaUsuarios - 2);
    const fin = Math.min(totalPaginas, paginaUsuarios + 2);

    if (inicio > 1) {
        crearItem('1', 1, false, paginaUsuarios === 1);
        if (inicio > 2) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link">...</span>';
            usuariosPaginacion.appendChild(li);
        }
    }

    for (let i = inicio; i <= fin; i++) {
        crearItem(String(i), i, false, paginaUsuarios === i);
    }

    if (fin < totalPaginas) {
        if (fin < totalPaginas - 1) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link">...</span>';
            usuariosPaginacion.appendChild(li);
        }
        crearItem(String(totalPaginas), totalPaginas, false, paginaUsuarios === totalPaginas);
    }

    crearItem('<i class="fa-solid fa-chevron-right"></i>', Math.min(totalPaginas, paginaUsuarios + 1), paginaUsuarios === totalPaginas);
}

function reiniciarFiltroUsuarios() {
    paginaUsuarios = 1;
    filtrarUsuarios();
}

inputUsuario.addEventListener('input', reiniciarFiltroUsuarios);
filtroModulo.addEventListener('change', reiniciarFiltroUsuarios);
filtroEstado.addEventListener('change', reiniciarFiltroUsuarios);
filtrarUsuarios();
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
