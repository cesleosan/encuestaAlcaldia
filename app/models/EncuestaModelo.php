<?php
class EncuestaModelo {
    private $db;
    private $ultimoError; // Propiedad para capturar errores técnicos de MariaDB

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Captura y devuelve el último error ocurrido en la base de datos
     */
    public function getError() {
        return $this->ultimoError;
    }

    /**
     * 1. Verificar si ya existe el CURP
     * Regla: Solo una encuesta por persona
     */
    public function existeCurp($curp) {
        $this->db->query('SELECT folio FROM encuestas WHERE curp = :curp');
        $this->db->bind(':curp', $curp);
        $row = $this->db->single();
        return $row ? $row->folio : false;
    }

    /**
     * Obtiene colonias iniciales para precarga
     */
    public function getColoniasTlalpan($limit = 10) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Busca colonias por código postal
     */
    public function getColoniasPorCP($cp) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        WHERE codigo_postal = :cp 
                        ORDER BY nombre_asentamiento ASC");
        $this->db->bind(':cp', $cp);
        return $this->db->resultSet();
    }

    /**
     * 2. Guardar la Encuesta (Versión de Campo Original)
     */
    public function agregar($datos) {
        try {
            $sql = 'INSERT INTO encuestas (
                        folio, usuario_id, curp, nombre, apellido_paterno, apellido_materno,
                        fecha_nacimiento, sexo, tiempo_residencia_tlalpan, tiempo_residencia_cdmx,
                        calle, numero_exterior, colonia_id, colonia_nombre, latitud, longitud, 
                        actividad_principal, superficie_total, volumen_total, unidad_medida,
                        respuestas_json, estatus, fecha_conclusion
                    ) VALUES (
                        :folio, :usuario_id, :curp, :nombre, :paterno, :materno,
                        :nacimiento, :sexo, :res_tlalpan, :res_cdmx,
                        :calle, :num_ext, :colonia_id, :colonia_nom, :lat, :lon,
                        :actividad, :superficie, :volumen, :unidad,
                        :json, :estatus, :fecha_fin
                    )';
            
            $this->db->query($sql);
            
            $this->db->bind(':folio', $datos['folio']);
            $this->db->bind(':usuario_id', $datos['usuario_id']);
            $this->db->bind(':curp', $datos['curp']);
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->bind(':paterno', $datos['paterno']);
            $this->db->bind(':materno', $datos['materno']);
            $this->db->bind(':nacimiento', $datos['fecha_nacimiento']);
            $this->db->bind(':sexo', $datos['sexo']);
            $this->db->bind(':res_tlalpan', $datos['tiempo_tlalpan']);
            $this->db->bind(':res_cdmx', $datos['tiempo_cdmx']);
            $this->db->bind(':calle', $datos['calle']);
            $this->db->bind(':num_ext', $datos['num_ext']);
            $this->db->bind(':colonia_id', $datos['colonia_id'] ?? null);
            $this->db->bind(':colonia_nom', $datos['colonia_nombre']);
            
            $lat = ($datos['latitud'] !== '' && $datos['latitud'] !== 0) ? $datos['latitud'] : null;
            $lon = ($datos['longitud'] !== '' && $datos['longitud'] !== 0) ? $datos['longitud'] : null;
            $this->db->bind(':lat', $lat);
            $this->db->bind(':lon', $lon);

            $this->db->bind(':actividad', $datos['actividad_principal']);
            $this->db->bind(':superficie', $datos['superficie_total']);
            $this->db->bind(':volumen', $datos['volumen_total']);
            $this->db->bind(':unidad', $datos['unidad_medida']);

            $this->db->bind(':estatus', $datos['estatus']);
            $this->db->bind(':fecha_fin', $datos['fecha_conclusion']); 
            $this->db->bind(':json', $datos['respuestas_completas']);

            if ($this->db->execute()) {
                return $datos['folio']; 
            }
            return false;
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /**
     * Obtiene el ID más alto registrado
     */
    public function obtenerUltimoId() {
        $this->db->query("SELECT MAX(id) as ultimo FROM encuestas");
        $row = $this->db->single();
        return $row->ultimo ?? 0;
    }

    /**
     * Coordenadas para el Dashboard
     */
    public function obtenerCoordenadasMapa() {
        $this->db->query("SELECT folio, latitud, longitud, actividad_principal 
                        FROM encuestas 
                        WHERE latitud IS NOT NULL AND longitud IS NOT NULL");
        return $this->db->resultSet();
    }

    /**
     * Conteos para gráficas
     */
    public function getConteoActividades() {
        $this->db->query("SELECT actividad_principal, COUNT(*) as total 
                        FROM encuestas 
                        GROUP BY actividad_principal");
        return $this->db->resultSet();
    }

    public function getDashboardKPIs() {
        $this->db->query("SELECT 
            COUNT(*) as total_encuestas,
            IFNULL(SUM(superficie_total), 0) as total_hectareas,
            COUNT(DISTINCT usuario_id) as tecnicos_activos,
            (SELECT COUNT(*) FROM cat_colonias) as colonias_cobertura
            FROM encuestas");
        return $this->db->single();
    }

    public function getProduccionPorColonia() {
        $this->db->query("SELECT colonia_nombre, COUNT(*) as total, SUM(superficie_total) as hectareas 
                        FROM encuestas WHERE colonia_nombre IS NOT NULL 
                        GROUP BY colonia_nombre ORDER BY hectareas DESC LIMIT 10");
        return $this->db->resultSet();
    }

    public function getProblemasPrincipales() {
        $this->db->query("SELECT JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.11[2].value')) as problema, COUNT(*) as total
                        FROM encuestas WHERE respuestas_json IS NOT NULL AND JSON_VALID(respuestas_json) 
                        GROUP BY problema HAVING problema IS NOT NULL");
        return $this->db->resultSet();
    }

    /**
     * Listado Maestro (Soporta todas las columnas nuevas)
     */
    public function getListadoMaestro() {
        $this->db->query("SELECT e.*, u.nombre_completo as encuestador 
                        FROM encuestas e
                        LEFT JOIN usuarios u ON e.usuario_id = u.id 
                        ORDER BY e.fecha_inicio DESC");
        return $this->db->resultSet();
    }

    public function getTendenciaDiaria() {
        $this->db->query("SELECT DATE(fecha_inicio) as fecha, COUNT(*) as total 
                        FROM encuestas GROUP BY DATE(fecha_inicio) ORDER BY fecha ASC");
        return $this->db->resultSet();
    }

    public function getEncuestaById($id) {
        $this->db->query("SELECT * FROM encuestas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     *  NUEVO: Obtiene el expediente para el Generador PDF (61 campos)
     */
    public function getExpedienteCompleto($id) {
        // IMPORTANTE: Agregamos el JOIN con la tabla usuarios
        $this->db->query("SELECT e.*, u.nombre_completo as nombre_tecnico 
                        FROM encuestas e
                        LEFT JOIN usuarios u ON e.usuario_id = u.id 
                        WHERE e.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     *  ACTUALIZACIÓN INTEGRAL (Versión 100% Sincronizada)
     * CORRECCIÓN: Se cambiaron las llaves :paterno, :materno y :nombre_productor
     * para que coincidan con lo que manda el Controlador Captura.php
     */
    public function actualizarExpediente($data) {
    try {
        $sql = "UPDATE encuestas SET 
                    -- Identidad
                    nombre = :nombre, 
                    apellido_paterno = :paterno, 
                    apellido_materno = :materno, 
                    curp = :curp, 
                    rfc = :rfc,
                    tipo_id = :tipo_id,
                    numero_id = :numero_id,
                    -- Perfil y Vulnerabilidad
                    tiene_discapacidad = :tiene_discap,
                    cual_discapacidad = :cual_discap,
                    grupo_etnico = :grupo_etnico,
                    grupo_etnico_cual = :grupo_etnico_cual,
                    escolaridad = :escolaridad, 
                    ocupacion = :ocupacion,
                    estado_civil = :estado_civil,
                    -- Ubicación y Contacto
                    calle = :calle,
                    colonia_nombre = :colonia,
                    codigo_postal = :cp,
                    tel_particular = :tel_part,
                    tel_casa = :tel_casa,
                    tel_familiar = :tel_fam,
                    -- Datos Técnicos
                    linea_ayuda = :linea_ayuda,
                    registro_siniiga = :siniiga,
                    num_total_predios = :num_predios,
                    superficie_total = :superficie, 
                    tipo_documento_propiedad = :tipo_doc,
                    pueblo_colonia_up = :colonia_up,
                    parajes = :parajes,
                    tenencia_tierra = :tenencia,
                    especie_cultivo_principal = :especie,
                    numero_cabezas_colmenas = :cabezas,
                    -- Checklist Documentos (TINYINT 0/1)
                    check_solicitud = :check_sol,
                    check_identidad = :check_id,
                    check_domicilio = :check_dom,
                    check_curp_doc = :check_curp,
                    check_rfc_doc = :check_rfc,
                    check_manifiesto = :check_man,
                    check_propiedad = :check_prop,
                    check_finiquito = :check_fin,
                    check_siniiga_doc = :check_sin,
                    -- Control y VERIFICACIÓN (Nuevos Campos)
                    fase_proceso = :fase, 
                    observaciones_capturista = :obs,
                    latitud_verif = :lat_v,
                    longitud_verif = :lon_v,
                    respuestas_json = :json 
                WHERE id = :id";

        $this->db->query($sql);
        
        // Bindeos Identidad
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombre', $data['nombre']); 
        $this->db->bind(':paterno', $data['apellido_paterno']); 
        $this->db->bind(':materno', $data['apellido_materno']); 
        $this->db->bind(':curp', $data['curp']);
        $this->db->bind(':rfc', $data['rfc'] ?? null);
        $this->db->bind(':tipo_id', $data['tipo_id'] ?? null);
        $this->db->bind(':numero_id', $data['numero_id'] ?? null);

        // Bindeos Perfil y Vulnerabilidad
        $this->db->bind(':tiene_discap', $data['tiene_discapacidad'] ?? 'NO');
        $this->db->bind(':cual_discap', $data['cual_discapacidad'] ?? 'NA');
        $this->db->bind(':grupo_etnico', $data['grupo_etnico'] ?? 'NO');
        $this->db->bind(':grupo_etnico_cual', $data['grupo_etnico_cual'] ?? 'NA');
        $this->db->bind(':escolaridad', $data['escolaridad'] ?? null); 
        $this->db->bind(':ocupacion', $data['ocupacion'] ?? null);
        $this->db->bind(':estado_civil', $data['estado_civil'] ?? null);

        // Bindeos Ubicación y Contacto
        $this->db->bind(':calle', $data['calle'] ?? null);
        $this->db->bind(':colonia', $data['colonia_nombre'] ?? null);
        $this->db->bind(':cp', $data['codigo_postal'] ?? null);
        $this->db->bind(':tel_part', $data['tel_particular'] ?? null);
        $this->db->bind(':tel_casa', $data['tel_casa'] ?? null);
        $this->db->bind(':tel_fam', $data['tel_familiar'] ?? null);

        // Bindeos Técnicos de Producción
        $this->db->bind(':linea_ayuda', $data['linea_ayuda'] ?? null);
        $this->db->bind(':siniiga', $data['registro_siniiga'] ?? 'NO');
        $this->db->bind(':num_predios', $data['num_total_predios'] ?? 1);
        $this->db->bind(':superficie', $data['superficie_total'] ?? 0);
        $this->db->bind(':tipo_doc', $data['tipo_documento_propiedad'] ?? null);
        $this->db->bind(':colonia_up', $data['pueblo_colonia_up'] ?? null);
        $this->db->bind(':parajes', $data['parajes'] ?? null);
        $this->db->bind(':tenencia', $data['tenencia_tierra'] ?? 'NA');
        $this->db->bind(':especie', $data['especie_cultivo_principal'] ?? null);
        $this->db->bind(':cabezas', $data['numero_cabezas_colmenas'] ?? 0);

        // Bindeos Checklist Documental
        $this->db->bind(':check_sol', $data['check_solicitud']);
        $this->db->bind(':check_id', $data['check_identidad']);
        $this->db->bind(':check_dom', $data['check_domicilio']);
        $this->db->bind(':check_curp', $data['check_curp_doc']);
        $this->db->bind(':check_rfc', $data['check_rfc_doc']);
        $this->db->bind(':check_man', $data['check_manifiesto']);
        $this->db->bind(':check_prop', $data['check_propiedad']);
        $this->db->bind(':check_fin', $data['check_finiquito']);
        $this->db->bind(':check_sin', $data['check_siniiga_doc']);

        // Control, Observaciones y VERIFICACIÓN (GPS NUEVO)
        $this->db->bind(':fase', $data['fase_proceso']);
        $this->db->bind(':obs', $data['observaciones_capturista'] ?? '');
        $this->db->bind(':lat_v', $data['latitud_verif'] ?? null);
        $this->db->bind(':lon_v', $data['longitud_verif'] ?? null);
        $this->db->bind(':json', $data['json']);

        return $this->db->execute();
} catch (Exception $e) {
    // Esto imprimirá el error real en la respuesta JSON para que lo veas en la consola
    die(json_encode(['status' => 'error', 'msg' => 'Error SQL: ' . $e->getMessage()]));
}
}
public function getEvidencias($encuestaId) {
    $this->db->query("SELECT * FROM encuesta_evidencias WHERE encuesta_id = :id");
    $this->db->bind(':id', $encuestaId);
    return $this->db->resultSet();
}

public function guardarEvidenciaFoto($encuestaId, $ruta) {
        try {
            $this->db->query("INSERT INTO encuesta_evidencias (encuesta_id, ruta_archivo) 
                              VALUES (:eid, :ruta)");
            
            $this->db->bind(':eid', $encuestaId);
            $this->db->bind(':ruta', $ruta);
            
            return $this->db->execute();
        } catch (Exception $e) {
            // Opcional: log del error
            return false;
        }
    }

    /**
     * Actualizar solo Fase (Usado en flujos rápidos)
     */
    public function actualizarFase($data) {
        $this->db->query("UPDATE encuestas SET fase_proceso = :fase, respuestas_json = :json WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':fase', $data['fase']);
        $this->db->bind(':json', $data['json']);
        return $this->db->execute();
    }
}