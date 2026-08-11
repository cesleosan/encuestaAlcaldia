<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>
<?php
    $usuarios = $data['lista'] ?? [];
    $resumen = $data['resumen'] ?? ['total' => 0, 'online' => 0, 'tierra' => 0, 'activos' => 0, 'pausados' => 0, 'inactivos' => 0];

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
        if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false) return 'Móvil';
        if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) return 'Tablet';
        return 'Escritorio';
    };

    $estadoMeta = function($estado, $activo = 0) {
        $estado = strtolower(trim((string)($estado ?: (((int)$activo === 1) ? 'activo' : 'inactivo'))));
        $mapa = [
            'activo' => ['label' => 'Activo', 'class' => 'success', 'icon' => 'fa-user-check'],
            'pausado' => ['label' => 'Pausado', 'class' => 'warning', 'icon' => 'fa-user-clock'],
            'inactivo' => ['label' => 'Inactivo', 'class' => 'danger', 'icon' => 'fa-user-slash']
        ];
        return $mapa[$estado] ?? $mapa['inactivo'];
    };

?>

<header class="tc-hero mb-4">
    <div class="tc-hero-copy">
        <span class="tc-eyebrow"><i class="fa-solid fa-shield-halved"></i> Vista privada de Adán</span>
        <h1>Control de accesos</h1>
        <p>Monitoreo de usuarios, módulos, último acceso y actividad reciente del sistema.</p>
    </div>
    <div class="tc-hero-actions">
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
            <i class="fa-solid fa-rotate me-1"></i>Actualizar
        </button>
    </div>
</header>

<?php require APPROOT . '/views/inc/superuser_nav.php'; ?>

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
            <div><span>Online</span><strong><?php echo (int)$resumen['online']; ?></strong><small>Actividad últimos 5 min</small></div>
        </article>
    </div>
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-info">
            <div class="tc-kpi-icon"><i class="fa-solid fa-seedling"></i></div>
            <div><span>Módulo Tierra</span><strong><?php echo (int)$resumen['tierra']; ?></strong><small>Usuarios asignados</small></div>
        </article>
    </div>
    <div class="col-xl-3 col-md-6">
        <article class="card tc-kpi-card tc-kpi-warning">
            <div class="tc-kpi-icon"><i class="fa-solid fa-user-check"></i></div>
            <div><span>Activos</span><strong id="kpiUsuariosActivos"><?php echo (int)$resumen['activos']; ?></strong><small>Con acceso permitido</small></div>
        </article>
    </div>
</section>

<section class="card p-0 overflow-hidden">
    <div class="tc-toolbar">
        <div>
            <h5 class="fw-bold text-guinda mb-1"><i class="fa-solid fa-user-clock me-2"></i>Monitor de usuarios</h5>
            <small class="text-muted">Este módulo solo está disponible para aGuillen.</small>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-end" style="max-width:760px;width:100%;">
            <div class="position-relative flex-grow-1" style="min-width:260px;">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left:15px;top:14px;color:#94a3b8;"></i>
                <input id="buscarUsuario" class="form-control ps-5" placeholder="Buscar usuario, nombre, rol, módulo o IP">
            </div>
            <select id="filtroModulo" class="form-select" style="max-width:180px;">
                <option value="">Todos los módulos</option>
                <option value="TIERRA">TIERRA</option>
                <option value="VUT">VUT</option>
            </select>
            <select id="filtroEstado" class="form-select" style="max-width:170px;">
                <option value="">Todos</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="activo">Activo</option>
                <option value="pausado">Pausado</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaUsuarios" style="min-width:1320px;">
            <thead>
                <tr>
                    <th class="ps-3">Estado</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Módulo</th>
                    <th>Último acceso</th>
                    <th>Última actividad</th>
                    <th>IP</th>
                    <th>Dispositivo</th>
                    <th class="text-center">Acceso</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                    <?php
                        $online = ((int)($u->sesiones_activas ?? 0) > 0);
                        $activo = ((int)($u->activo ?? 0) === 1);
                        $modulo = strtoupper((string)($u->modulo ?? ''));
                        $estadoAcceso = strtolower((string)($u->estado_acceso ?? ($activo ? 'activo' : 'inactivo')));
                        $estadoAccesoMeta = $estadoMeta($estadoAcceso, $u->activo ?? 0);
                        $estadoTexto = $online ? 'online' : 'offline';
                        $textoBusqueda = strtolower(implode(' ', [
                            $u->usuario ?? '',
                            $u->nombre_completo ?? '',
                            $u->rol ?? '',
                            $u->modulo ?? '',
                            $u->ip ?? '',
                            $estadoTexto,
                            $estadoAcceso,
                            $estadoAccesoMeta['label']
                        ]));
                    ?>
                    <tr
                        data-search="<?php echo $esc($textoBusqueda); ?>"
                        data-modulo="<?php echo $esc($modulo); ?>"
                        data-estado="<?php echo $esc($estadoTexto); ?>"
                        data-activo="<?php echo $esc($estadoAcceso); ?>"
                        data-id="<?php echo (int)($u->id ?? 0); ?>"
                        data-usuario="<?php echo $esc($u->usuario ?? ''); ?>"
                        data-nombre="<?php echo $esc($u->nombre_completo ?? ''); ?>"
                        data-telefono="<?php echo $esc($u->telefono ?? ''); ?>"
                        data-rol="<?php echo $esc($u->rol ?? ''); ?>"
                        data-estado-acceso="<?php echo $esc($estadoAcceso); ?>"
                        data-ip="<?php echo $esc($u->ip ?? ''); ?>"
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
                            <div class="fw-semibold usuario-nombre-cell"><?php echo $esc($u->nombre_completo); ?></div>
                            <small class="text-muted">ID <?php echo (int)($u->id ?? 0); ?></small>
                        </td>
                        <td><span class="badge badge-comite usuario-rol-cell"><?php echo $esc(strtoupper($u->rol ?? '')); ?></span></td>
                        <td><span class="badge text-bg-light border usuario-modulo-cell"><?php echo $esc($modulo ?: 'SIN MÓDULO'); ?></span></td>
                        <td class="text-muted"><?php echo $esc($fecha($u->ultimo_acceso ?? $u->ultimo_inicio ?? null)); ?></td>
                        <td>
                            <div><?php echo $esc($fecha($u->ultima_actividad ?? null)); ?></div>
                            <?php if(!empty($u->estado_sesion)): ?>
                                <small class="text-muted">Sesión <?php echo $esc($u->estado_sesion); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="font-monospace small"><?php echo $esc($u->ip ?: 'Sin dato'); ?></span></td>
                        <td><?php echo $esc($dispositivo($u->user_agent ?? '')); ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="badge estado-acceso-badge text-bg-<?php echo $esc($estadoAccesoMeta['class']); ?>">
                                    <i class="fa-solid <?php echo $esc($estadoAccesoMeta['icon']); ?> me-1"></i><?php echo $esc($estadoAccesoMeta['label']); ?>
                                </span>
                                <select class="form-select form-select-sm estado-acceso-select" style="width:128px;" data-id="<?php echo (int)($u->id ?? 0); ?>" aria-label="Cambiar estado de acceso">
                                    <option value="activo" <?php echo $estadoAcceso === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="pausado" <?php echo $estadoAcceso === 'pausado' ? 'selected' : ''; ?>>Pausado</option>
                                    <option value="inactivo" <?php echo $estadoAcceso === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-usuario">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Editar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer class="tc-table-footer d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <span id="usuariosInfo"><?php echo count($usuarios); ?> usuario(s) visibles</span>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <small class="text-muted"><i class="fa-solid fa-lock me-1"></i>Edición limitada para aGuillen.</small>
            <nav aria-label="Paginación usuarios">
                <ul class="pagination pagination-sm mb-0" id="usuariosPaginacion"></ul>
            </nav>
        </div>
    </footer>
</section>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formEditarUsuario">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold text-guinda mb-0"><i class="fa-solid fa-user-gear me-2"></i>Editar usuario</h5>
                    <small class="text-muted" id="editarUsuarioSubtitulo">Actualiza los datos de acceso</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editarUsuarioId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <input type="text" class="form-control" name="nombre_completo" id="editarNombre" required maxlength="150">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" id="editarTelefono" maxlength="15" placeholder="Opcional">
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Rol</label>
                        <select class="form-select" name="rol" id="editarRol" required>
                            <option value="root">Root</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="consulta">Consulta</option>
                            <option value="capturista">Capturista</option>
                            <option value="encuestador">Encuestador</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Módulo</label>
                        <select class="form-select" name="modulo" id="editarModulo" required>
                            <option value="TIERRA">TIERRA</option>
                            <option value="VUT">VUT</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" name="estado_acceso" id="editarEstado" required>
                            <option value="activo">Activo</option>
                            <option value="pausado">Pausado</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="alert alert-light border small mt-3 mb-0">
                    <i class="fa-solid fa-circle-info me-1 text-guinda"></i>
                    Activo permite iniciar sesión. Pausado e Inactivo bloquean el acceso sin borrar el registro.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const URLROOT_USUARIOS = '<?php echo URLROOT; ?>';
const inputUsuario = document.getElementById('buscarUsuario');
const filtroModulo = document.getElementById('filtroModulo');
const filtroEstado = document.getElementById('filtroEstado');
const filasUsuarios = Array.from(document.querySelectorAll('#tablaUsuarios tbody tr'));
const usuariosInfo = document.getElementById('usuariosInfo');
const usuariosPaginacion = document.getElementById('usuariosPaginacion');
const pageSizeUsuarios = 10;
let paginaUsuarios = 1;
const modalEditarUsuarioEl = document.getElementById('modalEditarUsuario');
const formEditarUsuario = document.getElementById('formEditarUsuario');

const estadoAccesoUi = {
    activo: { label: 'Activo', className: 'success', icon: 'fa-user-check' },
    pausado: { label: 'Pausado', className: 'warning', icon: 'fa-user-clock' },
    inactivo: { label: 'Inactivo', className: 'danger', icon: 'fa-user-slash' }
};

function normalizarTextoUsuario(valor) {
    return String(valor || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function getModalEditarUsuario() {
    if (window.bootstrap && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(modalEditarUsuarioEl);
    }

    return {
        show() {
            modalEditarUsuarioEl.classList.add('show');
            modalEditarUsuarioEl.style.display = 'block';
            modalEditarUsuarioEl.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
        },
        hide() {
            modalEditarUsuarioEl.classList.remove('show');
            modalEditarUsuarioEl.style.display = 'none';
            modalEditarUsuarioEl.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        }
    };
}

function notificarUsuario(mensaje, ok = true) {
    if (window.Swal) {
        Swal.fire({
            icon: ok ? 'success' : 'error',
            title: ok ? 'Listo' : 'Atención',
            text: mensaje,
            timer: ok ? 1400 : undefined,
            showConfirmButton: !ok
        });
        return;
    }

    alert(mensaje);
}

function enviarUsuario(url, formData) {
    return fetch(url, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    }).then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.ok === false) {
            throw new Error(data.mensaje || 'No fue posible guardar los cambios');
        }
        return data;
    });
}

function filtrarUsuarios() {
    const texto = normalizarTextoUsuario(inputUsuario.value).trim();
    const modulo = filtroModulo.value;
    const estado = filtroEstado.value;

    const filtradas = filasUsuarios.filter(fila => {
        const pasaTexto = !texto || normalizarTextoUsuario(fila.dataset.search).includes(texto);
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

function actualizarBusquedaFila(fila) {
    const partes = [
        fila.dataset.usuario,
        fila.dataset.nombre,
        fila.dataset.rol,
        fila.dataset.modulo,
        fila.dataset.ip,
        fila.dataset.estado,
        fila.dataset.activo,
        estadoAccesoUi[fila.dataset.activo]?.label
    ];

    fila.dataset.search = normalizarTextoUsuario(partes.join(' '));
}

function actualizarKpiUsuarios() {
    const kpiActivos = document.getElementById('kpiUsuariosActivos');
    if (kpiActivos) {
        kpiActivos.textContent = filasUsuarios.filter(fila => fila.dataset.activo === 'activo').length;
    }
}

function pintarEstadoFila(fila, estado) {
    const meta = estadoAccesoUi[estado] || estadoAccesoUi.inactivo;
    const badge = fila.querySelector('.estado-acceso-badge');
    const select = fila.querySelector('.estado-acceso-select');

    fila.dataset.activo = estado;
    fila.dataset.estadoAcceso = estado;

    if (select) {
        select.value = estado;
        select.dataset.valorAnterior = estado;
    }

    if (badge) {
        badge.className = `badge estado-acceso-badge text-bg-${meta.className}`;
        badge.innerHTML = `<i class="fa-solid ${meta.icon} me-1"></i>${meta.label}`;
    }

    actualizarBusquedaFila(fila);
    actualizarKpiUsuarios();
    filtrarUsuarios();
}

function actualizarFilaEditada(fila) {
    const nombre = document.getElementById('editarNombre').value.trim();
    const telefono = document.getElementById('editarTelefono').value.trim();
    const rol = document.getElementById('editarRol').value;
    const modulo = document.getElementById('editarModulo').value;

    fila.dataset.nombre = nombre;
    fila.dataset.telefono = telefono;
    fila.dataset.rol = rol;
    fila.dataset.modulo = modulo;

    const nombreCell = fila.querySelector('.usuario-nombre-cell');
    const rolCell = fila.querySelector('.usuario-rol-cell');
    const moduloCell = fila.querySelector('.usuario-modulo-cell');

    if (nombreCell) nombreCell.textContent = nombre;
    if (rolCell) rolCell.textContent = rol.toUpperCase();
    if (moduloCell) moduloCell.textContent = modulo || 'SIN MÓDULO';

    actualizarBusquedaFila(fila);
}

inputUsuario.addEventListener('input', reiniciarFiltroUsuarios);
filtroModulo.addEventListener('change', reiniciarFiltroUsuarios);
filtroEstado.addEventListener('change', reiniciarFiltroUsuarios);

modalEditarUsuarioEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
    btn.addEventListener('click', () => getModalEditarUsuario().hide());
});

document.querySelectorAll('.estado-acceso-select').forEach(select => {
    select.addEventListener('focus', function() {
        this.dataset.valorAnterior = this.value;
    });

    select.addEventListener('change', function() {
        const fila = this.closest('tr');
        const nuevoEstado = this.value;
        const formData = new FormData();
        formData.append('id', this.dataset.id);
        formData.append('estado_acceso', nuevoEstado);
        this.disabled = true;

        enviarUsuario(`${URLROOT_USUARIOS}/Usuarios/estado`, formData)
            .then(data => {
                notificarUsuario(data.mensaje || 'Estado actualizado');
                pintarEstadoFila(fila, data.estado_acceso || nuevoEstado);
            })
            .catch(error => {
                this.value = this.dataset.valorAnterior || this.value;
                notificarUsuario(error.message, false);
            })
            .finally(() => {
                this.disabled = false;
            });
    });
});

document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
    btn.addEventListener('click', function() {
        const fila = this.closest('tr');
        document.getElementById('editarUsuarioId').value = fila.dataset.id || '';
        document.getElementById('editarUsuarioSubtitulo').textContent = `${fila.dataset.usuario || ''} · ID ${fila.dataset.id || ''}`;
        document.getElementById('editarNombre').value = fila.dataset.nombre || '';
        document.getElementById('editarTelefono').value = fila.dataset.telefono || '';
        document.getElementById('editarRol').value = fila.dataset.rol || 'encuestador';
        document.getElementById('editarModulo').value = fila.dataset.modulo || 'TIERRA';
        document.getElementById('editarEstado').value = fila.dataset.estadoAcceso || 'inactivo';
        getModalEditarUsuario().show();
    });
});

formEditarUsuario.addEventListener('submit', function(e) {
    e.preventDefault();
    const submit = this.querySelector('button[type="submit"]');
    submit.disabled = true;

    enviarUsuario(`${URLROOT_USUARIOS}/Usuarios/actualizar`, new FormData(this))
        .then(data => {
            const id = document.getElementById('editarUsuarioId').value;
            const fila = filasUsuarios.find(item => item.dataset.id === id);

            if (fila) {
                actualizarFilaEditada(fila);
                pintarEstadoFila(fila, data.estado_acceso || document.getElementById('editarEstado').value);
            }

            getModalEditarUsuario().hide();
            notificarUsuario(data.mensaje || 'Usuario actualizado');
        })
        .catch(error => {
            notificarUsuario(error.message, false);
        })
        .finally(() => {
            submit.disabled = false;
        });
});

filtrarUsuarios();
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
