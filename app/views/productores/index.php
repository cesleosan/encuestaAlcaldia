<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<div class="top-header">
    <div class="page-title">
        <h1>Padrón de Productores</h1>
        <p>Gestión y seguimiento de las unidades productivas censadas</p>
    </div>
    <button class="btn-action">
        <i class="fa-solid fa-file-excel"></i> Exportar Base
    </button>
</div>

<div class="card" style="margin-bottom: 20px; padding: 15px; display: flex; gap: 15px; flex-wrap: wrap;">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" placeholder="Buscar por nombre, folio o actividad..." 
               style="width: 100%; padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-family:inherit;">
    </div>
    <div style="flex: 1; min-width: 150px;">
        <select style="width: 100%; padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-family:inherit; color:#64748b;">
            <option value="">Todos los Pueblos</option>
            <option value="topilejo">San Miguel Topilejo</option>
            <option value="parres">Parres El Guarda</option>
            <option value="ajusco">San Miguel Ajusco</option>
        </select>
    </div>
    <button class="btn-action" style="background: var(--dorado); border: none;">
        <i class="fa-solid fa-magnifying-glass"></i> Buscar
    </button>
</div>

<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #eef2f6;">
                <tr>
                    <th style="padding: 15px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Folio</th>
                    <th style="padding: 15px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Productor</th>
                    <th style="padding: 15px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Ubicación</th>
                    <th style="padding: 15px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Actividad</th>
                    <th style="padding: 15px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Estatus</th>
                    <th style="padding: 15px 20px; text-align: center; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['lista'] as $fila): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    
                    <td style="padding: 15px 20px;">
                        <span style="font-weight: 600; color: var(--guinda); font-family: monospace; font-size: 14px;">
                            <?php echo $fila['folio']; ?>
                        </span>
                    </td>

                    <td style="padding: 15px 20px;">
                        <div style="font-weight: 600; color: var(--oscuro);"><?php echo $fila['nombre']; ?></div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
                            <i class="fa-regular fa-calendar"></i> <?php echo $fila['fecha']; ?>
                        </div>
                    </td>

                    <td style="padding: 15px 20px; color: #475569;">
                        <?php echo $fila['pueblo']; ?>
                    </td>

                    <td style="padding: 15px 20px;">
                        <span style="background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #dbeafe;">
                            <?php echo $fila['actividad']; ?>
                        </span>
                    </td>

                    <td style="padding: 15px 20px;">
                        <?php 
                            // Lógica simple para colores
                            $color = '#10b981'; // Verde por defecto (Completa)
                            if($fila['estatus'] == 'Pendiente') $color = '#f59e0b'; // Naranja
                            if($fila['estatus'] == 'Revisión') $color = '#ef4444'; // Rojo
                            if($fila['estatus'] == 'Nueva') $color = '#3b82f6'; // Azul
                        ?>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: <?php echo $color; ?>;"></span>
                            <span style="font-weight: 500; font-size: 13px; color: <?php echo $color; ?>;">
                                <?php echo $fila['estatus']; ?>
                            </span>
                        </div>
                    </td>

                    <td style="padding: 15px 20px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 8px;">
                            <button title="Ver Detalle" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='var(--guinda)'; this.style.color='var(--guinda)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b';">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button title="Descargar PDF" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b';">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="padding: 15px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 13px;">
        <div>Mostrando 1 a 6 de 1,250 registros</div>
        <div style="display: flex; gap: 5px;">
            <button style="padding: 5px 10px; border: 1px solid #e2e8f0; background: white; border-radius: 4px; cursor: pointer;">Anterior</button>
            <button style="padding: 5px 10px; border: 1px solid var(--guinda); background: var(--guinda); color: white; border-radius: 4px; cursor: pointer;">1</button>
            <button style="padding: 5px 10px; border: 1px solid #e2e8f0; background: white; border-radius: 4px; cursor: pointer;">2</button>
            <button style="padding: 5px 10px; border: 1px solid #e2e8f0; background: white; border-radius: 4px; cursor: pointer;">Siguiente</button>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>