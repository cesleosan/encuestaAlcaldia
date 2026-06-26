<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#f5f7fa">
    <title>Consulta Comit&eacute; &middot; Tierra con Coraz&oacute;n</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/tierracorazon-ui.css?v=20260626-1">
    <style>
        body {
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            background: #f5f7fa !important;
            color: #263238 !important;
        }

        .consulta-shell {
            max-width: 1740px;
            margin: 0 auto;
            padding: 30px clamp(16px, 3vw, 42px);
        }

        .consulta-hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            background:
                radial-gradient(circle at top left, rgba(119, 51, 87, .12), transparent 34%),
                linear-gradient(135deg, #fff, #f9fbfd);
            border: 1px solid #eef1f5;
            border-radius: 24px;
            box-shadow: 0 14px 34px rgba(20, 32, 54, .08);
            padding: clamp(20px, 3vw, 34px);
            overflow: hidden;
        }

        .consulta-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .consulta-brand-icon {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            color: #fff;
            background: linear-gradient(145deg, var(--tc-primary), var(--tc-primary-dark));
            box-shadow: 0 12px 24px rgba(119, 51, 87, .25);
            font-size: 1.35rem;
            flex: 0 0 auto;
        }

        .consulta-brand h1 {
            margin: 0;
            color: var(--tc-primary);
            font-size: clamp(1.55rem, 3vw, 2.55rem);
            font-weight: 800;
            letter-spacing: -.045em;
            line-height: 1.04;
        }

        .consulta-brand p {
            margin: 6px 0 0;
            color: var(--tc-muted);
            font-size: .95rem;
        }

        .consulta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--tc-primary-soft);
            color: var(--tc-primary);
            border: 1px solid #e5cbd8;
            font-size: .76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .consulta-kpi {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(20, 32, 54, .07);
            min-height: 112px;
        }

        .consulta-kpi .card-body {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .consulta-kpi-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            color: var(--tc-primary);
            background: var(--tc-primary-soft);
            flex: 0 0 auto;
        }

        .consulta-kpi span {
            display: block;
            color: var(--tc-muted);
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .consulta-kpi strong {
            color: #1f2937;
            display: block;
            font-size: 1.65rem;
            line-height: 1;
            margin-top: 4px;
        }

        .consulta-board {
            overflow: hidden;
            border: 0;
            border-radius: 22px;
            box-shadow: 0 14px 34px rgba(20, 32, 54, .08);
        }

        .consulta-board .table-responsive {
            min-height: 360px;
        }

        .consulta-board .table {
            min-width: 1320px;
        }

        .consulta-board .table td {
            font-size: .79rem;
            vertical-align: middle;
        }

        .consulta-board .table tbody tr:hover {
            background: #fff8fb !important;
        }

        .consulta-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            padding: 18px;
            border-bottom: 1px solid #edf0f4;
            background: #fff;
        }

        .consulta-toolbar-title {
            min-width: 260px;
        }

        .consulta-toolbar-title h5 {
            margin: 0;
            color: var(--tc-primary);
            font-weight: 800;
        }

        .consulta-toolbar-title small {
            color: var(--tc-muted);
        }

        .consulta-toolbar-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1 1 600px;
        }

        .consulta-search {
            position: relative;
            flex: 1 1 320px;
            max-width: 520px;
        }

        .consulta-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--tc-muted);
        }

        .consulta-search input {
            padding-left: 40px;
        }

        .badge-comite {
            background: var(--tc-primary-soft);
            color: var(--tc-primary);
            border: 1px solid #e2c8d5;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .badge-readonly {
            background: #eef6ff;
            color: #0b5ed7;
            border: 1px solid #cde4ff;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: .68rem;
            font-weight: 800;
        }

        .case-title {
            color: #1f2937;
            font-weight: 800;
        }

        .case-subtitle {
            color: var(--tc-muted);
            font-size: .72rem;
        }

        .detalle-pill {
            height: 100%;
            border: 1px solid #eef0f4;
            background: #fff;
            border-radius: 16px;
            padding: 13px;
        }

        .detalle-pill .label {
            color: var(--tc-muted);
            font-size: .67rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .detalle-pill .value {
            color: #1f2937;
            font-size: .88rem;
            font-weight: 800;
            margin-top: 4px;
            word-break: break-word;
        }

        .cedula-section {
            border: 1px solid #eef0f4;
            border-radius: 18px;
            background: #fff;
            padding: 16px;
            margin-bottom: 16px;
        }

        .cedula-section-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--tc-primary);
            font-weight: 800;
            font-size: .9rem;
            margin-bottom: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .cedula-section-title i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-grid;
            place-items: center;
            background: var(--tc-primary-soft);
        }

        .consulta-footer {
            background: #fff;
            border-top: 1px solid #edf0f4;
            color: var(--tc-muted);
            padding: 15px 18px;
        }

        .image-card {
            display: block;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #eef0f4;
            background: #fff;
            box-shadow: 0 8px 18px rgba(20, 32, 54, .08);
            text-decoration: none;
        }

        .image-card img {
            width: 100%;
            height: 185px;
            object-fit: cover;
            display: block;
        }

        .image-card span {
            display: block;
            padding: 9px 10px;
            color: var(--tc-muted);
            font-size: .72rem;
            font-weight: 700;
        }

        @media (prefers-color-scheme: dark) {
            body,
            .consulta-hero,
            .consulta-toolbar,
            .consulta-footer,
            .consulta-board,
            .card,
            .modal-content,
            .detalle-pill {
                background-color: #fff !important;
                color: #263238 !important;
            }

            .consulta-brand h1,
            .consulta-toolbar-title h5,
            .text-guinda {
                color: var(--tc-primary) !important;
            }

            .table,
            .form-control,
            .form-select {
                background-color: #fff !important;
                color: #263238 !important;
            }
        }

        @media (max-width: 760px) {
            .consulta-shell {
                padding-top: 18px;
            }

            .consulta-brand {
                align-items: flex-start;
            }

            .consulta-brand-icon {
                width: 48px;
                height: 48px;
            }

            .consulta-toolbar-controls {
                justify-content: stretch;
            }

            .consulta-search,
            .consulta-toolbar-controls .form-select,
            .consulta-toolbar-controls .btn {
                width: 100%;
                max-width: none;
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
<main class="consulta-shell">
    <header class="consulta-hero mb-4">
        <div class="consulta-brand">
            <div class="consulta-brand-icon"><i class="fas fa-people-group"></i></div>
            <div>
                <span class="consulta-pill mb-2"><i class="fas fa-shield-halved"></i> Sin editar c&eacute;dula</span>
                <h1>Bandeja de Comit&eacute;</h1>
                <p>Consulta de folios enviados por Captura a Comit&eacute;. Puede dictaminar estatus sin modificar datos, documentos ni im&aacute;genes.</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Actualizar
            </button>
            <a href="<?php echo URLROOT; ?>/Auth/logout" class="btn btn-danger">
                <i class="fas fa-arrow-right-from-bracket me-2"></i>Salir
            </a>
        </div>
    </header>

    <section class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card consulta-kpi">
                <div class="card-body">
                    <div class="consulta-kpi-icon"><i class="fas fa-folder-open"></i></div>
                    <div>
                        <span>Casos visibles</span>
                        <strong id="kpiCasos">0</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card consulta-kpi">
                <div class="card-body">
                    <div class="consulta-kpi-icon"><i class="fas fa-images"></i></div>
                    <div>
                        <span>Con evidencias</span>
                        <strong id="kpiEvidencias">0</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card consulta-kpi">
                <div class="card-body">
                    <div class="consulta-kpi-icon"><i class="fas fa-map-location-dot"></i></div>
                    <div>
                        <span>Con coordenadas</span>
                        <strong id="kpiCoordenadas">0</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card consulta-kpi">
                <div class="card-body">
                    <div class="consulta-kpi-icon"><i class="fas fa-route"></i></div>
                    <div>
                        <span>Bandeja actual</span>
                        <strong style="font-size:1.25rem;">COMIT&Eacute;</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card consulta-board">
        <div class="consulta-toolbar">
            <div class="consulta-toolbar-title">
                <h5><i class="fas fa-table-list me-2"></i>Folios y casos para consulta</h5>
                <small>Vista homologada con admin, limitada a expedientes en Comit&eacute;.</small>
            </div>
            <div class="consulta-toolbar-controls">
                <div class="consulta-search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input id="buscarConsulta" class="form-control" placeholder="Buscar folio, productor, CURP, colonia o actividad">
                </div>
                <select id="filtroEstatusConsulta" class="form-select" style="max-width:230px;">
                    <option value="COMITE">Estatus: Comit&eacute;</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" id="btnLimpiarConsulta">
                    <i class="fas fa-eraser me-1"></i>Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Folio</th>
                        <th>Productor</th>
                        <th>CURP</th>
                        <th>Colonia / Pueblo</th>
                        <th>Actividad</th>
                        <th class="text-center">Superficie</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Estatus</th>
                        <th class="text-center">Im&aacute;genes</th>
                        <th class="text-center">Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody id="consultaBody">
                    <tr>
                        <td colspan="10">
                            <div class="tc-empty-state">
                                <div class="tc-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div>
                                <div class="fw-semibold">Cargando folios de Comit&eacute;...</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="consulta-footer d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <span id="consultaConteo"><i class="fas fa-list me-1"></i>Cargando...</span>
            <span><i class="fas fa-lock me-1"></i>Este perfil no edita datos ni archivos; solo puede dictaminar el estatus del caso.</span>
        </footer>
    </section>
</main>

<div class="modal fade" id="detalleConsultaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:22px;overflow:hidden;">
            <div class="modal-header border-0 px-4 py-3" style="background:var(--tc-primary);color:#fff;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-folder-open me-2"></i>Detalle del expediente <span id="modalFolio">---</span></h5>
                    <small class="opacity-75">Consulta de caso en Comit&eacute; &middot; sin edici&oacute;n</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <div id="detalleCasoGrid"></div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-route me-2"></i>Dictamen de estatus</h6>
                        <span class="badge-readonly"><i class="fas fa-check-to-slot me-1"></i>Comit&eacute;</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border mb-3">
                            <div class="fw-bold text-guinda mb-1">Este expediente fue enviado a Comit&eacute; por el perfil de Captura.</div>
                            <div class="small text-muted">Puedes cambiar el estatus del caso, sin modificar datos de la c&eacute;dula, documentos ni im&aacute;genes.</div>
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-lg-8">
                                <label class="small fw-bold text-muted text-uppercase mb-1" for="selectDictamenComite">Mover expediente a</label>
                                <select id="selectDictamenComite" class="form-select fw-bold">
                                    <option value="APROBADO">Aprobado</option>
                                    <option value="RECHAZADO">Rechazado</option>
                                    <option value="EN_REVISION">Devolver a revisi&oacute;n t&eacute;cnica</option>
                                </select>
                                <small class="text-muted">Al cambiarlo dejar&aacute; de aparecer en esta bandeja si ya no est&aacute; en Comit&eacute;.</small>
                            </div>
                            <div class="col-lg-4 d-grid">
                                <button type="button" class="btn btn-guinda" id="btnGuardarDictamenComite">
                                    <i class="fas fa-save me-1"></i>Guardar estatus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-images me-2"></i>Im&aacute;genes del expediente</h6>
                        <small class="text-muted" id="modalImagenesStatus">Consultando...</small>
                    </div>
                    <div class="card-body" id="visorImagenesContenido">
                        <div class="tc-empty-state">
                            <div class="tc-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div>
                            Cargando im&aacute;genes...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let registrosConsulta = [];
let registrosFiltrados = [];
let registroConsultaActual = null;

const URLROOT_CONSULTA = '<?php echo URLROOT; ?>';

function escapar(valor) {
    const div = document.createElement('div');
    div.textContent = repararTexto(valor ?? '');
    return div.innerHTML;
}

function repararTexto(valor) {
    return String(valor ?? '')
        .replace(/\u00c3\u00a1/g, '\u00e1')
        .replace(/\u00c3\u00a9/g, '\u00e9')
        .replace(/\u00c3\u00ad/g, '\u00ed')
        .replace(/\u00c3\u00b3/g, '\u00f3')
        .replace(/\u00c3\u00ba/g, '\u00fa')
        .replace(/\u00c3\u0081/g, '\u00c1')
        .replace(/\u00c3\u0089/g, '\u00c9')
        .replace(/\u00c3\u008d/g, '\u00cd')
        .replace(/\u00c3\u0093/g, '\u00d3')
        .replace(/\u00c3\u009a/g, '\u00da')
        .replace(/\u00c3\u00b1/g, '\u00f1')
        .replace(/\u00c3\u0091/g, '\u00d1')
        .replace(/\u00c2\u00bf/g, '\u00bf')
        .replace(/\u00c2\u00a1/g, '\u00a1')
        .replace(/\u00c2\u00b7/g, '\u00b7')
        .replace(/\u00c2/g, '');
}

function normalizar(valor) {
    return repararTexto(valor ?? '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

function nombreCompleto(reg) {
    return [reg.nombre, reg.apellido_paterno, reg.apellido_materno].filter(Boolean).join(' ').trim() || 'Sin nombre';
}

function valorCorto(valor, fallback = 'Sin dato') {
    const limpio = repararTexto(valor ?? '').trim();
    return limpio ? limpio : fallback;
}

function fechaCorta(valor) {
    return valor ? String(valor).substring(0, 10) : 'Sin fecha';
}

function tieneCoordenadas(reg) {
    return Boolean((reg.latitud_verif && reg.longitud_verif) || (reg.latitud && reg.longitud));
}

function estadoVacio(mensaje, ayuda = 'Cuando Captura env&iacute;e un expediente a Comit&eacute; aparecer&aacute; aqu&iacute;.', icono = 'fa-folder-open') {
    return `<tr><td colspan="10"><div class="tc-empty-state">
        <div class="tc-empty-state-icon"><i class="fas ${icono}"></i></div>
        <div class="fw-semibold">${mensaje}</div>
        <small>${ayuda}</small>
    </div></td></tr>`;
}

function badgeComite() {
    return '<span class="badge-comite"><i class="fas fa-people-group me-1"></i>COMIT&Eacute;</span>';
}

function actualizarKpis(registrosBase) {
    document.getElementById('kpiCasos').textContent = registrosBase.length;
    document.getElementById('kpiEvidencias').textContent = registrosBase.filter(reg => Number(reg.total_fotos || 0) > 0 || Number(reg.check_formatos_tecnicos || 0) === 1).length;
    document.getElementById('kpiCoordenadas').textContent = registrosBase.filter(tieneCoordenadas).length;
}

function renderConsulta(registros) {
    registrosFiltrados = registros;
    const body = document.getElementById('consultaBody');
    body.innerHTML = '';

    if (!registros.length) {
        body.innerHTML = estadoVacio('No hay folios visibles con estos filtros');
    } else {
        registros.forEach(reg => {
            const productor = nombreCompleto(reg);
            const superficie = Number(reg.superficie_total || 0);
            const imagenes = Number(reg.total_fotos || 0) + (Number(reg.check_formatos_tecnicos || 0) === 1 ? 1 : 0);

            body.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="ps-3">
                        <span class="badge text-bg-light border text-guinda">${escapar(reg.folio)}</span>
                    </td>
                    <td>
                        <div class="case-title">${escapar(productor)}</div>
                        <div class="case-subtitle">Captur&oacute;: ${escapar(reg.encuestador || 'Sin dato')}</div>
                    </td>
                    <td><span class="font-monospace">${escapar(reg.curp || 'Sin dato')}</span></td>
                    <td>${escapar(valorCorto(reg.colonia_nombre))}</td>
                    <td>${escapar(valorCorto(reg.actividad_principal || reg.linea_ayuda))}</td>
                    <td class="text-center">${Number.isFinite(superficie) ? superficie.toFixed(2) : '0.00'} ha</td>
                    <td class="text-center">${escapar(fechaCorta(reg.fecha_inicio))}</td>
                    <td class="text-center">${badgeComite()}</td>
                    <td class="text-center">
                        <span class="badge text-bg-light border"><i class="fas fa-images me-1"></i>${imagenes}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="abrirDetalle(${Number(reg.id)})">
                            <i class="fas fa-eye me-1"></i>Consultar
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    document.getElementById('consultaConteo').innerHTML =
        `<i class="fas fa-list me-1"></i>${registros.length} de ${registrosConsulta.length} folio${registrosConsulta.length === 1 ? '' : 's'} visible${registros.length === 1 ? '' : 's'}`;
}

function aplicarFiltrosConsulta() {
    const texto = normalizar(document.getElementById('buscarConsulta').value).trim();
    const filtrados = registrosConsulta.filter(reg => {
        const busqueda = normalizar([
            reg.folio,
            nombreCompleto(reg),
            reg.curp,
            reg.colonia_nombre,
            reg.actividad_principal,
            reg.linea_ayuda,
            reg.encuestador,
            reg.estatus,
            reg.fase_proceso
        ].join(' '));
        return !texto || busqueda.includes(texto);
    });
    renderConsulta(filtrados);
}

function cargarRegistrosConsulta() {
    fetch(`${URLROOT_CONSULTA}/Encuesta/getEstadisticas`, { cache: 'no-store' })
        .then(res => {
            if (!res.ok) throw new Error('No fue posible obtener los registros');
            return res.json();
        })
        .then(data => {
            registrosConsulta = Array.isArray(data.maestro) ? data.maestro : [];
            actualizarKpis(registrosConsulta);
            renderConsulta(registrosConsulta);
        })
        .catch(() => {
            document.getElementById('consultaBody').innerHTML = estadoVacio('No fue posible cargar los folios', 'Revisa la sesi&oacute;n o intenta actualizar la pantalla.', 'fa-triangle-exclamation');
            document.getElementById('consultaConteo').innerHTML = '<i class="fas fa-circle-exclamation me-1"></i>Sin conexi&oacute;n';
        });
}

function detalleItem(label, value) {
    return `<div class="col-md-4 col-xl-3">
        <div class="detalle-pill">
            <div class="label">${label}</div>
            <div class="value">${escapar(valorCorto(value))}</div>
        </div>
    </div>`;
}

function respuestasPlano(reg) {
    const plano = {};
    try {
        const parsed = typeof reg.respuestas_json === 'string' ? JSON.parse(reg.respuestas_json) : reg.respuestas_json;
        const recorrer = (nodo) => {
            if (Array.isArray(nodo)) {
                nodo.forEach(recorrer);
                return;
            }
            if (!nodo || typeof nodo !== 'object') return;
            if (nodo.name) {
                let valor = nodo.value;
                if (Array.isArray(valor)) {
                    valor = valor.map(item => typeof item === 'object' ? (item.value ?? item.label ?? JSON.stringify(item)) : item).join(', ');
                } else if (valor && typeof valor === 'object') {
                    valor = valor.value ?? valor.label ?? JSON.stringify(valor);
                }
                plano[nodo.name] = valor;
            }
            Object.values(nodo).forEach(recorrer);
        };
        recorrer(parsed);
    } catch (e) {
        console.warn('No fue posible leer respuestas_json', e);
    }
    return plano;
}

function pick(reg, plano, columnas, fallback = 'Sin dato') {
    for (const columna of columnas) {
        const valor = reg[columna] ?? plano[columna];
        if (valor !== undefined && valor !== null && String(valor).trim() !== '') return valor;
    }
    return fallback;
}

function siNo(valor) {
    return Number(valor || 0) === 1 ? 'SI' : 'NO';
}

function detalleSeccion(titulo, icono, items) {
    return `<section class="cedula-section">
        <div class="cedula-section-title"><i class="fas ${icono}"></i>${titulo}</div>
        <div class="row g-3">
            ${items.map(item => detalleItem(item[0], item[1])).join('')}
        </div>
    </section>`;
}

function renderDetalleCaso(reg) {
    const grid = document.getElementById('detalleCasoGrid');
    const plano = respuestasPlano(reg);

    const superficie = Number(pick(reg, plano, ['superficie_total', 'superficie_prod'], 0));
    const volumen = Number(pick(reg, plano, ['volumen_total', 'volumen_prod'], 0));
    const superficieDoc = Number(pick(reg, plano, ['superficie_documental'], 0));

    grid.innerHTML = [
        detalleSeccion('Identidad del solicitante', 'fa-id-card', [
            ['Folio', reg.folio],
            ['Productor', nombreCompleto(reg)],
            ['CURP', reg.curp],
            ['RFC', pick(reg, plano, ['rfc'])],
            ['Tipo de identificaci&oacute;n', pick(reg, plano, ['tipo_id'])],
            ['N&uacute;mero de identificaci&oacute;n', pick(reg, plano, ['numero_id'])],
            ['Fecha de nacimiento', pick(reg, plano, ['fecha_nacimiento'])],
            ['Sexo', pick(reg, plano, ['sexo'])]
        ]),
        detalleSeccion('Contacto y ubicaci&oacute;n', 'fa-location-dot', [
            ['Tel&eacute;fono particular', pick(reg, plano, ['tel_particular'])],
            ['Tel&eacute;fono de casa', pick(reg, plano, ['tel_casa'])],
            ['Tel&eacute;fono familiar/recados', pick(reg, plano, ['tel_familiar', 'tel_recados'])],
            ['Calle y n&uacute;mero', pick(reg, plano, ['calle', 'calle_numero'])],
            ['N&uacute;mero exterior', pick(reg, plano, ['numero_exterior'])],
            ['N&uacute;mero interior', pick(reg, plano, ['numero_interior'])],
            ['Colonia / Pueblo', pick(reg, plano, ['colonia_nombre', 'pueblo_colonia'])],
            ['C&oacute;digo postal', pick(reg, plano, ['codigo_postal', 'cp'])],
            ['Referencia', pick(reg, plano, ['referencia'])],
            ['Latitud original', pick(reg, plano, ['latitud'])],
            ['Longitud original', pick(reg, plano, ['longitud'])],
            ['Precisi&oacute;n GPS', pick(reg, plano, ['precision_gps'])]
        ]),
        detalleSeccion('Perfil social', 'fa-user-group', [
            ['Estado civil', pick(reg, plano, ['estado_civil'])],
            ['Escolaridad', pick(reg, plano, ['escolaridad', 'grado_estudios'])],
            ['Ocupaci&oacute;n', pick(reg, plano, ['ocupacion'])],
            ['Grupo &eacute;tnico', pick(reg, plano, ['grupo_etnico'])],
            ['Grupo &eacute;tnico - cu&aacute;l', pick(reg, plano, ['grupo_etnico_cual'])],
            ['Tiene discapacidad', pick(reg, plano, ['tiene_discapacidad'])],
            ['Discapacidad - cu&aacute;l', pick(reg, plano, ['cual_discapacidad'])],
            ['Residencia en Tlalpan', pick(reg, plano, ['tiempo_residencia_tlalpan', 'tiempo_residencia'])],
            ['Residencia en CDMX', pick(reg, plano, ['tiempo_residencia_cdmx'])]
        ]),
        detalleSeccion('Producci&oacute;n y unidad productiva', 'fa-seedling', [
            ['L&iacute;nea de ayuda', pick(reg, plano, ['linea_ayuda', 'tipo_produccion'])],
            ['Actividad principal', pick(reg, plano, ['actividad_principal'])],
            ['Cultivo / especie principal', pick(reg, plano, ['especie_cultivo_principal', 'cultivo_principal'])],
            ['Superficie productiva', Number.isFinite(superficie) ? `${superficie.toFixed(2)} ha` : pick(reg, plano, ['superficie_total', 'superficie_prod'])],
            ['Volumen de producci&oacute;n', Number.isFinite(volumen) ? volumen.toFixed(2) : pick(reg, plano, ['volumen_total', 'volumen_prod'])],
            ['Unidad de medida', pick(reg, plano, ['unidad_medida'])],
            ['N&uacute;mero de animales / colmenas', pick(reg, plano, ['numero_cabezas_colmenas', 'num_animales'])],
            ['Registro SINIIGA', pick(reg, plano, ['registro_siniiga', 'siniiga_status'])],
            ['Total de predios', pick(reg, plano, ['num_total_predios'])]
        ]),
        detalleSeccion('Tierra y documentaci&oacute;n', 'fa-file-signature', [
            ['Tipo documento propiedad', pick(reg, plano, ['tipo_documento_propiedad', 'tipo_documento_prop'])],
            ['Superficie documental', Number.isFinite(superficieDoc) ? `${superficieDoc.toFixed(4)} ha` : pick(reg, plano, ['superficie_documental'])],
            ['Pueblo / colonia UP', pick(reg, plano, ['pueblo_colonia_up'])],
            ['Parajes', pick(reg, plano, ['parajes'])],
            ['Tenencia de tierra', pick(reg, plano, ['tenencia_tierra'])],
            ['Solicitud', siNo(reg.check_solicitud)],
            ['Identidad', siNo(reg.check_identidad)],
            ['Domicilio', siNo(reg.check_domicilio)],
            ['CURP doc.', siNo(reg.check_curp_doc)],
            ['RFC doc.', siNo(reg.check_rfc_doc)],
            ['Manifiesto', siNo(reg.check_manifiesto)],
            ['Propiedad', siNo(reg.check_propiedad)],
            ['Finiquito', siNo(reg.check_finiquito)],
            ['SINIIGA doc.', siNo(reg.check_siniiga_doc)],
            ['Formatos t&eacute;cnicos', siNo(reg.check_formatos_tecnicos)]
        ]),
        detalleSeccion('Proceso y verificaci&oacute;n', 'fa-clipboard-check', [
            ['Capturista / T&eacute;cnico', reg.encuestador],
            ['Fecha de captura', fechaCorta(reg.fecha_inicio)],
            ['Fecha conclusi&oacute;n', fechaCorta(reg.fecha_conclusion)],
            ['Fase actual', 'COMITE'],
            ['Estatus operativo', reg.estatus || 'Comite'],
            ['Latitud verificada', pick(reg, plano, ['latitud_verif'])],
            ['Longitud verificada', pick(reg, plano, ['longitud_verif'])],
            ['Fotos de verificaci&oacute;n', reg.total_fotos || 0],
            ['Observaciones capturista', pick(reg, plano, ['observaciones_capturista'])]
        ])
    ].join('');
}

function renderGrupoImagenes(titulo, fotos, icono) {
    return `<section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-guinda mb-0"><i class="fas ${icono} me-2"></i>${titulo}</h6>
            <span class="badge text-bg-light border">${fotos.length}</span>
        </div>
        <div class="row g-3">
            ${fotos.length ? fotos.map((foto, index) => `
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="${escapar(foto.url)}" target="_blank" class="image-card">
                        <img src="${escapar(foto.url)}" alt="${escapar(titulo)} ${index + 1}">
                        <span><i class="fas fa-up-right-from-square me-1"></i>Abrir imagen ${index + 1}</span>
                    </a>
                </div>
            `).join('') : '<div class="col-12 text-muted small">Sin im&aacute;genes disponibles.</div>'}
        </div>
    </section>`;
}

function cargarImagenes(id) {
    const contenido = document.getElementById('visorImagenesContenido');
    const status = document.getElementById('modalImagenesStatus');
    contenido.innerHTML = '<div class="tc-empty-state"><div class="tc-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div>Cargando im&aacute;genes...</div>';
    status.textContent = 'Consultando...';

    fetch(`${URLROOT_CONSULTA}/Encuesta/getEvidenciasConsulta/${id}`, { cache: 'no-store' })
        .then(res => {
            if (!res.ok) throw new Error('No fue posible cargar imagenes');
            return res.json();
        })
        .then(data => {
            const formatos = data.formatos_tecnicos || [];
            const verificacion = data.verificacion || [];
            status.textContent = `${formatos.length + verificacion.length} imagen(es)`;
            contenido.innerHTML = [
                renderGrupoImagenes('Formatos t&eacute;cnicos', formatos, 'fa-file-image'),
                renderGrupoImagenes('Evidencias de campo', verificacion, 'fa-camera')
            ].join('');
        })
        .catch(() => {
            status.textContent = 'Error';
            contenido.innerHTML = '<div class="alert alert-danger mb-0">No fue posible cargar las im&aacute;genes del expediente.</div>';
        });
}

function abrirDetalle(id) {
    const reg = registrosConsulta.find(item => Number(item.id) === Number(id));
    if (!reg) return;

    registroConsultaActual = reg;
    document.getElementById('modalFolio').textContent = reg.folio || '---';
    renderDetalleCaso(reg);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('detalleConsultaModal')).show();
    cargarImagenes(id);
}

function cambiarDictamenComite() {
    if (!registroConsultaActual) return;

    const select = document.getElementById('selectDictamenComite');
    const fase = select.value;
    const etiqueta = select.options[select.selectedIndex]?.textContent || fase;

    if (!confirm(`Confirmas mover el expediente ${registroConsultaActual.folio} a "${etiqueta}"?`)) {
        return;
    }

    const boton = document.getElementById('btnGuardarDictamenComite');
    const textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

    fetch(`${URLROOT_CONSULTA}/Encuesta/cambiarFaseVerificacion`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: registroConsultaActual.id,
            fase
        })
    })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || data.status !== 'success') {
                throw new Error(data.msg || 'No fue posible guardar el estatus');
            }

            registrosConsulta = registrosConsulta.filter(item => Number(item.id) !== Number(registroConsultaActual.id));
            actualizarKpis(registrosConsulta);
            aplicarFiltrosConsulta();

            bootstrap.Modal.getOrCreateInstance(document.getElementById('detalleConsultaModal')).hide();
            alert('Estatus actualizado correctamente.');
        })
        .catch(error => {
            alert(error.message || 'No fue posible guardar el estatus.');
        })
        .finally(() => {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        });
}

document.getElementById('buscarConsulta').addEventListener('input', aplicarFiltrosConsulta);
document.getElementById('btnLimpiarConsulta').addEventListener('click', function() {
    document.getElementById('buscarConsulta').value = '';
    aplicarFiltrosConsulta();
});
document.getElementById('btnGuardarDictamenComite').addEventListener('click', cambiarDictamenComite);

cargarRegistrosConsulta();
</script>
</body>
</html>
