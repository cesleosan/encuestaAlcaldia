<?php
class EncuestaModelo {
    private $db;
    private $ultimoError;

    public function __construct() {
        $this->db = new Database;
    }

    public function getError() {
        return $this->ultimoError;
    }

    public function existeCurp($curp) {
        $this->db->query('SELECT folio FROM encuestas WHERE curp = :curp');
        $this->db->bind(':curp', $curp);
        $row = $this->db->single();
        return $row ? $row->folio : false;
    }

    public function getColoniasTlalpan($limit = 10) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getColoniasPorCP($cp) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        WHERE codigo_postal = :cp 
                        ORDER BY nombre_asentamiento ASC");
        $this->db->bind(':cp', $cp);
        return $this->db->resultSet();
    }

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
            if ($e->errorInfo[1] == 1062) {
                return ['status' => 'duplicate', 'msg' => 'Folio ya existe'];
            }
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /**
     * 🔥 NUEVO: Obtiene el expediente completo (usado por Generador PDF)
     */
    public function getExpedienteCompleto($id) {
        $this->db->query("SELECT * FROM encuestas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * 🔥 ACTUALIZADO: Actualización masiva del expediente desde el Modal de Edición
     */
    public function actualizarExpediente($data) {
        $sql = "UPDATE encuestas SET 
                    -- Identidad
                    nombre = :nombre, 
                    apellido_paterno = :paterno, 
                    apellido_materno = :materno, 
                    curp = :curp, 
                    rfc = :rfc,
                    tipo_id = :tipo_id,
                    numero_id = :numero_id,
                    -- Perfil
                    tiene_discapacidad = :tiene_discap,
                    cual_discapacidad = :cual_discap,
                    grupo_etnico = :grupo_etnico,
                    grupo_etnico_cual = :grupo_etnico_cual,
                    grado_estudios = :escolaridad,
                    ocupacion = :ocupacion,
                    -- Contacto y Ubicación
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
                    -- Checklist Documentos
                    check_solicitud = :check_sol,
                    check_identidad = :check_id,
                    check_domicilio = :check_dom,
                    check_curp_doc = :check_curp,
                    check_rfc_doc = :check_rfc,
                    check_manifiesto = :check_man,
                    check_propiedad = :check_prop,
                    check_finiquito = :check_fin,
                    check_siniiga_doc = :check_sin,
                    -- Control
                    fase_proceso = :fase, 
                    observaciones_capturista = :obs,
                    respuestas_json = :json 
                WHERE id = :id";

        $this->db->query($sql);
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':paterno', $data['paterno']);
        $this->db->bind(':materno', $data['materno']);
        $this->db->bind(':curp', $data['curp']);
        $this->db->bind(':rfc', $data['rfc']);
        $this->db->bind(':tipo_id', $data['tipo_id']);
        $this->db->bind(':numero_id', $data['numero_id']);

        $this->db->bind(':tiene_discap', $data['tiene_discapacidad']);
        $this->db->bind(':cual_discap', $data['cual_discapacidad']);
        $this->db->bind(':grupo_etnico', $data['grupo_etnico']);
        $this->db->bind(':grupo_etnico_cual', $data['grupo_etnico_cual']);
        $this->db->bind(':escolaridad', $data['grado_estudios']);
        $this->db->bind(':ocupacion', $data['ocupacion']);

        $this->db->bind(':calle', $data['calle_numero']);
        $this->db->bind(':colonia', $data['pueblo_colonia']);
        $this->db->bind(':cp', $data['cp']);
        $this->db->bind(':tel_part', $data['tel_particular']);
        $this->db->bind(':tel_casa', $data['tel_casa']);
        $this->db->bind(':tel_fam', $data['tel_recados']);

        $this->db->bind(':linea_ayuda', $data['linea_ayuda']);
        $this->db->bind(':siniiga', $data['siniiga_status']);
        $this->db->bind(':num_predios', $data['num_total_predios']);
        $this->db->bind(':superficie', $data['superficie_prod']);
        $this->db->bind(':tipo_doc', $data['tipo_documento_prop']);
        $this->db->bind(':colonia_up', $data['pueblo_colonia_up']);
        $this->db->bind(':parajes', $data['parajes']);
        $this->db->bind(':tenencia', $data['tenencia_tierra']);
        $this->db->bind(':especie', $data['cultivo_principal']);
        $this->db->bind(':cabezas', $data['num_animales']);

        $this->db->bind(':check_sol', $data['check_solicitud']);
        $this->db->bind(':check_id', $data['check_identidad']);
        $this->db->bind(':check_dom', $data['check_domicilio']);
        $this->db->bind(':check_curp', $data['check_curp_doc']);
        $this->db->bind(':check_rfc', $data['check_rfc_doc']);
        $this->db->bind(':check_man', $data['check_manifiesto']);
        $this->db->bind(':check_prop', $data['check_propiedad']);
        $this->db->bind(':check_fin', $data['check_finiquito']);
        $this->db->bind(':check_sin', $data['check_siniiga_doc']);

        $this->db->bind(':fase', $data['fase_proceso']);
        $this->db->bind(':obs', $data['observaciones_capturista']);
        $this->db->bind(':json', $data['json']);

        return $this->db->execute();
    }

    // El resto de tus funciones de KPI y Dashboards se mantienen igual...
    public function obtenerUltimoId() {
        $this->db->query("SELECT MAX(id) as ultimo FROM encuestas");
        $row = $this->db->single();
        return $row->ultimo ?? 0;
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

    public function getListadoMaestro() {
        $this->db->query("SELECT e.*, u.nombre_completo as encuestador 
                        FROM encuestas e
                        LEFT JOIN usuarios u ON e.usuario_id = u.id 
                        ORDER BY e.fecha_inicio DESC");
        return $this->db->resultSet();
    }
}