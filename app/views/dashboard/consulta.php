<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#f5f7fa">
    <title>Consulta · Tierra con Corazón</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/tierracorazon-ui.css?v=20260625-2">
    <style>
        body { min-height: 100vh; font-family: 'Montserrat', sans-serif; }
        .consulta-shell { max-width: 1680px; margin: 0 auto; padding: 30px clamp(16px, 3vw, 42px); }
        .consulta-brand { display:flex; align-items:center; gap:14px; }
        .consulta-brand-icon {
            width:52px; height:52px; display:grid; place-items:center; border-radius:16px;
            color:#fff; background:linear-gradient(145deg, var(--tc-primary), var(--tc-primary-dark));
            box-shadow:0 9px 22px rgba(119,51,87,.22); font-size:1.25rem;
        }
        .consulta-brand h1 { margin:0; color:var(--tc-primary); font-size:clamp(1.55rem,3vw,2.25rem); font-weight:800; letter-spacing:-.04em; }
        .consulta-brand p { margin:4px 0 0; color:var(--tc-muted); font-size:.92rem; }
        .consulta-card { overflow:hidden; }
        .consulta-card .table-responsive { min-height:310px; }
        .consulta-card .table { min-width:1040px; }
        .consulta-card .table tbody tr { cursor:default; }
        .consulta-card .table td { font-size:.8rem; }
        .badge-comite { background:var(--tc-primary-soft); color:var(--tc-primary); border:1px solid #e2c8d5; }
        .consulta-count {
            display:inline-flex; align-items:center; gap:7px; padding:7px 11px;
            background:#f3f5f8; border-radius:999px; color:var(--tc-muted); font-size:.78rem; font-weight:700;
        }
        .consulta-note { display:flex; gap:10px; align-items:flex-start; color:var(--tc-muted); font-size:.8rem; }
        .consulta-note i { color:var(--tc-primary); margin-top:2px; }
        @media (max-width: 700px) {
            .consulta-shell { padding-top:20px; }
            .consulta-header { align-items:flex-start !important; }
            .consulta-brand-icon { width:44px; height:44px; }
            .consulta-card tbody td[colspan] .tc-empty-state {
                position: sticky;
                left: 0;
                width: calc(100vw - 34px);
                max-width: none;
            }
        }
    </style>
</head>
<body>
<main class="consulta-shell">
    <header class="consulta-header d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
        <div class="consulta-brand">
            <div class="consulta-brand-icon"><i class="fas fa-seedling"></i></div>
            <div>
                <h1>Tierra con Corazón</h1>
                <p>Consulta de expedientes enviados a Comité</p>
            </div>
        </div>
        <a href="<?php echo URLROOT; ?>/Auth/logout" class="btn btn-danger px-3">
            <i class="fas fa-arrow-right-from-bracket me-2"></i>Salir
        </a>
    </header>

    <section class="card consulta-card">
        <div class="tc-toolbar">
            <div>
                <h5 class="fw-bold text-guinda mb-1"><i class="fas fa-users me-2"></i>Tabla de encuestados</h5>
                <div class="consulta-note">
                    <i class="fas fa-shield-halved"></i>
                    <span>Vista de solo lectura. Únicamente aparecen registros con estatus Comité.</span>
                </div>
            </div>
            <div class="position-relative" style="width:min(100%, 480px);">
                <i class="fas fa-magnifying-glass position-absolute top-50 translate-middle-y text-muted" style="left:15px;"></i>
                <input id="buscarConsulta" class="form-control ps-5" placeholder="Buscar folio, nombre, CURP o colonia">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Nombre</th>
                        <th>CURP</th>
                        <th>Colonia</th>
                        <th>Actividad</th>
                        <th>Fecha</th>
                        <th>Estatus</th>
                        <th class="text-center">Imágenes</th>
                    </tr>
                </thead>
                <tbody id="consultaBody">
                    <tr>
                        <td colspan="8">
                            <div class="tc-empty-state">
                                <div class="tc-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div>
                                <div class="fw-semibold">Cargando expedientes...</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <footer class="tc-table-footer d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <span class="consulta-count" id="consultaConteo"><i class="fas fa-list"></i> Cargando...</span>
            <small>Los datos e imágenes no pueden modificarse desde este perfil.</small>
        </footer>
    </section>
</main>

<div class="modal fade" id="visorImagenesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div class="modal-header border-0 px-4 py-3" style="background:var(--tc-primary);color:#fff;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-images me-2"></i>Imágenes del expediente</h5>
                    <small class="opacity-75">Vista de solo consulta</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-4" id="visorImagenesContenido"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let registrosConsulta = [];

function escapar(valor) {
    const div = document.createElement('div');
    div.textContent = valor ?? '';
    return div.innerHTML;
}

function estadoVacio(mensaje, icono = 'fa-folder-open') {
    return `<tr><td colspan="8"><div class="tc-empty-state">
        <div class="tc-empty-state-icon"><i class="fas ${icono}"></i></div>
        <div class="fw-semibold">${mensaje}</div>
        <small>Cuando Captura envíe un expediente a Comité aparecerá aquí.</small>
    </div></td></tr>`;
}

function renderConsulta(registros) {
    const body = document.getElementById('consultaBody');
    body.innerHTML = '';
    registros.forEach(reg => {
        const nombre = [reg.nombre, reg.apellido_paterno, reg.apellido_materno].filter(Boolean).join(' ');
        body.insertAdjacentHTML('beforeend', `
            <tr>
                <td><span class="badge text-bg-light border text-guinda">${escapar(reg.folio)}</span></td>
                <td class="fw-semibold">${escapar(nombre)}</td>
                <td><span class="font-monospace">${escapar(reg.curp)}</span></td>
                <td>${escapar(reg.colonia_nombre || 'Sin dato')}</td>
                <td>${escapar(reg.actividad_principal || 'Sin dato')}</td>
                <td>${escapar((reg.fecha_inicio || '').substring(0, 10))}</td>
                <td><span class="badge badge-comite"><i class="fas fa-people-group me-1"></i>COMITÉ</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="verImagenes(${Number(reg.id)})">
                        <i class="fas fa-images me-1"></i>Ver
                    </button>
                </td>
            </tr>`);
    });
    if (!registros.length) body.innerHTML = estadoVacio('No hay registros en estatus Comité');
    document.getElementById('consultaConteo').innerHTML =
        `<i class="fas fa-list"></i> ${registros.length} registro${registros.length === 1 ? '' : 's'} visible${registros.length === 1 ? '' : 's'}`;
}

fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
    .then(res => {
        if (!res.ok) throw new Error('No fue posible obtener los registros');
        return res.json();
    })
    .then(data => {
        registrosConsulta = data.maestro || [];
        renderConsulta(registrosConsulta);
    })
    .catch(() => {
        document.getElementById('consultaBody').innerHTML = estadoVacio('No fue posible cargar los registros', 'fa-triangle-exclamation');
        document.getElementById('consultaConteo').innerHTML = '<i class="fas fa-circle-exclamation"></i> Sin conexión';
    });

document.getElementById('buscarConsulta').addEventListener('input', function() {
    const texto = this.value.toLowerCase().trim();
    const filtrados = registrosConsulta.filter(reg => [
        reg.folio, reg.nombre, reg.apellido_paterno, reg.apellido_materno,
        reg.curp, reg.colonia_nombre, reg.actividad_principal
    ].join(' ').toLowerCase().includes(texto));
    renderConsulta(filtrados);
});

function verImagenes(id) {
    const contenido = document.getElementById('visorImagenesContenido');
    contenido.innerHTML = '<div class="tc-empty-state"><div class="tc-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div>Cargando imágenes...</div>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('visorImagenesModal')).show();

    fetch(`<?php echo URLROOT; ?>/Encuesta/getEvidenciasConsulta/${id}`)
        .then(res => {
            if (!res.ok) throw new Error();
            return res.json();
        })
        .then(data => {
            const grupos = [
                ['Formatos técnicos', data.formatos_tecnicos || []],
                ['Evidencias de campo', data.verificacion || []]
            ];
            contenido.innerHTML = grupos.map(([titulo, fotos]) => `
                <section class="card p-3 mb-3">
                    <h6 class="fw-bold text-guinda mb-3">${titulo} <span class="badge text-bg-light border ms-1">${fotos.length}</span></h6>
                    <div class="row g-3">
                        ${fotos.length ? fotos.map(foto => `
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="${foto.url}" target="_blank" class="d-block">
                                    <img src="${foto.url}" alt="${titulo}" class="img-fluid rounded shadow-sm" style="width:100%;height:190px;object-fit:cover;">
                                </a>
                            </div>`).join('') : '<div class="col-12 text-muted small">Sin imágenes disponibles.</div>'}
                    </div>
                </section>`).join('');
        })
        .catch(() => {
            contenido.innerHTML = '<div class="alert alert-danger mb-0">No fue posible cargar las imágenes.</div>';
        });
}
</script>
</body>
</html>
