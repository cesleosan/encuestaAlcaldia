<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    :root { --guinda:#773357; --fondo:#f4f6f9; }
    body { background:var(--fondo); font-family:'Montserrat',sans-serif; }
    .text-guinda { color:var(--guinda); }
    .consulta-card { border:0; border-radius:18px; box-shadow:0 5px 24px rgba(0,0,0,.08); }
    .table thead th { background:var(--guinda); color:#fff; font-size:.72rem; text-transform:uppercase; border:0; }
    .table td { vertical-align:middle; font-size:.82rem; }
    .badge-comite { background:#f3e7ed; color:var(--guinda); border:1px solid #dfc3d1; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-guinda mb-1">Tierra con CorazÃ³n</h2>
            <p class="text-muted mb-0">Consulta de encuestados enviados a ComitÃ©</p>
        </div>
        <a href="<?php echo URLROOT; ?>/Auth/logout" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Salir
        </a>
    </div>

    <div class="card consulta-card">
        <div class="card-header bg-white border-0 p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-guinda mb-0"><i class="fas fa-users me-2"></i>Tabla de encuestados</h5>
                </div>
                <div class="col-md-4">
                    <input id="buscarConsulta" class="form-control" placeholder="Buscar folio, nombre, CURP o colonia">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Nombre</th>
                        <th>CURP</th>
                        <th>Colonia</th>
                        <th>Actividad</th>
                        <th>Fecha</th>
                        <th>Estatus</th>
                        <th>Imágenes</th>
                    </tr>
                </thead>
                <tbody id="consultaBody"></tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-muted small" id="consultaConteo">Cargando...</div>
    </div>
</div>

<script>
let registrosConsulta = [];

function escapar(valor) {
    const div = document.createElement('div');
    div.textContent = valor ?? '';
    return div.innerHTML;
}

function renderConsulta(registros) {
    const body = document.getElementById('consultaBody');
    body.innerHTML = '';
    registros.forEach(reg => {
        const nombre = [reg.nombre, reg.apellido_paterno, reg.apellido_materno].filter(Boolean).join(' ');
        body.insertAdjacentHTML('beforeend', `
            <tr>
                <td><span class="badge text-bg-light border">${escapar(reg.folio)}</span></td>
                <td class="fw-semibold">${escapar(nombre)}</td>
                <td>${escapar(reg.curp)}</td>
                <td>${escapar(reg.colonia_nombre || '---')}</td>
                <td>${escapar(reg.actividad_principal || '---')}</td>
                <td>${escapar((reg.fecha_inicio || '').substring(0, 10))}</td>
                <td><span class="badge badge-comite">COMITÃ‰</span></td>
                <td><button class="btn btn-sm btn-outline-secondary" onclick="verImagenes(${Number(reg.id)})"><i class="fas fa-images me-1"></i> Ver</button></td>
            </tr>`);
    });
    if (!registros.length) {
        body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5">No hay registros en estatus ComitÃ©.</td></tr>';
    }
    document.getElementById('consultaConteo').textContent = `${registros.length} registro(s) visible(s)`;
}

fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
    .then(res => res.json())
    .then(data => {
        registrosConsulta = data.maestro || [];
        renderConsulta(registrosConsulta);
    })
    .catch(() => {
        document.getElementById('consultaBody').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger py-5">No fue posible cargar los registros.</td></tr>';
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
    contenido.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i>Cargando imágenes...</div>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('visorImagenesModal')).show();

    fetch(`<?php echo URLROOT; ?>/Encuesta/getEvidenciasConsulta/${id}`)
        .then(res => res.json())
        .then(data => {
            const grupos = [
                ['Formatos técnicos', data.formatos_tecnicos || []],
                ['Evidencias de campo', data.verificacion || []]
            ];
            contenido.innerHTML = grupos.map(([titulo, fotos]) => `
                <h6 class="fw-bold text-guinda mt-3">${titulo}</h6>
                <div class="row g-3">
                    ${fotos.length ? fotos.map(foto => `
                        <div class="col-6 col-md-4">
                            <a href="${foto.url}" target="_blank">
                                <img src="${foto.url}" alt="${titulo}" class="img-fluid rounded shadow-sm" style="width:100%;height:190px;object-fit:cover;">
                            </a>
                        </div>`).join('') : '<div class="col-12 text-muted small">Sin imágenes.</div>'}
                </div>`).join('');
        })
        .catch(() => {
            contenido.innerHTML = '<div class="alert alert-danger">No fue posible cargar las imágenes.</div>';
        });
}
</script>

<div class="modal fade" id="visorImagenesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-guinda"><i class="fas fa-images me-2"></i>Imágenes del expediente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="visorImagenesContenido"></div>
            <div class="modal-footer">
                <small class="text-muted me-auto"><i class="fas fa-eye me-1"></i>Vista de solo consulta</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
