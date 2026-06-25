<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<header class="top-header">
    <div class="page-title">
        <h1>Padrón de productores</h1>
        <p>Consulta y seguimiento de las unidades productivas censadas</p>
    </div>
    <button class="btn-action"><i class="fa-solid fa-file-excel"></i>Exportar base</button>
</header>

<section class="card">
    <div class="tc-toolbar">
        <div class="position-relative flex-grow-1" style="min-width:240px;">
            <i class="fa-solid fa-magnifying-glass position-absolute" style="left:15px;top:14px;color:#94a3b8;"></i>
            <input id="buscarProductor" class="form-control ps-5" placeholder="Buscar por nombre, folio, pueblo o actividad">
        </div>
        <select id="filtroPueblo" class="form-select" style="max-width:260px;">
            <option value="">Todos los pueblos</option>
            <option value="topilejo">San Miguel Topilejo</option>
            <option value="parres">Parres El Guarda</option>
            <option value="ajusco">San Miguel Ajusco</option>
        </select>
    </div>
</section>

<section class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tablaProductores" style="min-width:850px;">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Productor</th>
                    <th>Ubicación</th>
                    <th>Actividad</th>
                    <th>Estatus</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['lista'] as $fila): ?>
                <?php
                    $estadoClase = 'success';
                    if ($fila['estatus'] === 'Pendiente') $estadoClase = 'warning';
                    if ($fila['estatus'] === 'Revisión') $estadoClase = 'danger';
                    if ($fila['estatus'] === 'Nueva') $estadoClase = 'info';
                    $textoBusqueda = strtolower(implode(' ', [$fila['folio'], $fila['nombre'], $fila['pueblo'], $fila['actividad'], $fila['estatus']]));
                ?>
                <tr data-search="<?php echo htmlspecialchars($textoBusqueda, ENT_QUOTES, 'UTF-8'); ?>">
                    <td><span class="badge text-bg-light border text-guinda font-monospace"><?php echo htmlspecialchars($fila['folio'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td>
                        <div class="fw-bold"><?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($fila['fecha'], ENT_QUOTES, 'UTF-8'); ?></small>
                    </td>
                    <td><i class="fa-solid fa-location-dot text-guinda me-1"></i><?php echo htmlspecialchars($fila['pueblo'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge text-bg-light border"><?php echo htmlspecialchars($fila['actividad'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><span class="badge text-bg-<?php echo $estadoClase; ?>"><?php echo htmlspecialchars($fila['estatus'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" title="Ver detalle"><i class="fa-solid fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <footer class="tc-table-footer d-flex justify-content-between align-items-center">
        <span id="conteoProductores"><?php echo count($data['lista']); ?> registros</span>
        <small>Información de demostración</small>
    </footer>
</section>

<script>
document.getElementById('buscarProductor').addEventListener('input', function() {
    const texto = this.value.toLowerCase().trim();
    let visibles = 0;
    document.querySelectorAll('#tablaProductores tbody tr').forEach(fila => {
        const mostrar = fila.dataset.search.includes(texto);
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
    document.getElementById('conteoProductores').textContent = `${visibles} registro${visibles === 1 ? '' : 's'}`;
});
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
