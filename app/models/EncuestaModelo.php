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

    /**
     * 1. Verificar si ya existe el CURP
     */
    public function existeCurp($curp) {
        $this->db->query('SELECT folio FROM encuestas WHERE curp = :curp');
        $this->db->bind(':curp', $curp);
        $row = $this->db->single();
        return $row ? $row->folio : false;
    }

    /**
     * 2. Obtener registro completo por ID
     */
    public function getExpedienteCompleto($id) {
        $this->db->query("SELECT * FROM encuestas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * 🔥 ACTUALIZACIÓN INTEGRAL (Sincronizada al 100% con MariaDB)
     * Este método usa las llaves enviadas por el controlador corregido.
     */
    public function actualizarExpediente($data) {
        try {
            $sql = "UPDATE encuestas SET 
                        -- Identidad
                        curp = :curp, 
                        rfc = :rfc,
                        nombre = :nombre, 
                        apellido_paterno = :apellido_paterno, 
                        apellido_materno = :apellido_materno, 
                        tipo_id = :tipo_id,
                        numero_id = :numero_id,
                        -- Perfil y Social
                        estado_civil = :estado_civil,
                        escolaridad = :escolaridad, 
                        ocupacion = :ocupacion,
                        tiene_discapacidad = :tiene_discapacidad,
                        cual_discapacidad = :cual_discapacidad,
                        grupo_etnico = :grupo_etnico,
                        grupo_etnico_cual = :grupo_etnico_cual,
                        -- Ubicación
                        calle = :calle,
                        colonia_nombre = :colonia_nombre,
                        codigo_postal = :codigo_postal,
                        tel_particular = :tel_particular,
                        tel_casa = :tel_casa,
                        tel_familiar = :tel_familiar,
                        -- Producción
                        linea_ayuda = :linea_ayuda,
                        registro_siniiga = :registro_siniiga,
                        num_total_predios = :num_total_predios,
                        superficie_total = :superficie_total,
                        tipo_documento_propiedad = :tipo_documento_propiedad,
                        pueblo_colonia_up = :pueblo_colonia_up,
                        parajes = :parajes,
                        tenencia_tierra = :tenencia_tierra,
                        especie_cultivo_principal = :especie_cultivo_principal,
                        numero_cabezas_colmenas = :numero_cabezas_colmenas,
                        -- Bits de Cotejo
                        check_solicitud = :check_solicitud,
                        check_identidad = :check_identidad,
                        check_domicilio = :check_domicilio,
                        check_curp_doc = :check_curp_doc,
                        check_rfc_doc = :check_rfc_doc,
                        check_manifiesto = :check_manifiesto,
                        check_propiedad = :check_propiedad,
                        check_finiquito = :check_finiquito,
                        check_siniiga_doc = :check_siniiga_doc,
                        -- Control
                        fase_proceso = :fase_proceso,
                        respuestas_json = :respuestas_json
                    WHERE id = :id";

            $this->db->query($sql);
            
            // Bindeo de Identidad (Llaves actualizadas)
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':curp', $data['curp']);
            $this->db->bind(':rfc', $data['rfc']);
            $this->db->bind(':nombre', $data['nombre']);
            $this->db->bind(':apellido_paterno', $data['apellido_paterno']);
            $this->db->bind(':apellido_materno', $data['apellido_materno']);
            $this->db->bind(':tipo_id', $data['tipo_id']);
            $this->db->bind(':numero_id', $data['numero_id']);

            // Bindeo de Perfil
            $this->db->bind(':estado_civil', $data['estado_civil']);
            $this->db->bind(':escolaridad', $data['escolaridad']);
            $this->db->bind(':ocupacion', $data['ocupacion']);
            $this->db->bind(':tiene_discapacidad', $data['tiene_discapacidad']);
            $this->db->bind(':cual_discapacidad', $data['cual_discapacidad']);
            $this->db->bind(':grupo_etnico', $data['grupo_etnico']);
            $this->db->bind(':grupo_etnico_cual', $data['grupo_etnico_cual']);

            // Bindeo de Ubicación
            $this->db->bind(':calle', $data['calle']);
            $this->db->bind(':colonia_nombre', $data['colonia_nombre']);
            $this->db->bind(':codigo_postal', $data['codigo_postal']);
            $this->db->bind(':tel_particular', $data['tel_particular']);
            $this->db->bind(':tel_casa', $data['tel_casa']);
            $this->db->bind(':tel_familiar', $data['tel_familiar']);

            // Bindeo de Datos Técnicos
            $this->db->bind(':linea_ayuda', $data['linea_ayuda']);
            $this->db->bind(':registro_siniiga', $data['registro_siniiga']);
            $this->db->bind(':num_total_predios', $data['num_total_predios']);
            $this->db->bind(':superficie_total', $data['superficie_total']);
            $this->db->bind(':tipo_documento_propiedad', $data['tipo_documento_propiedad']);
            $this->db->bind(':pueblo_colonia_up', $data['pueblo_colonia_up']);
            $this->db->bind(':parajes', $data['parajes']);
            $this->db->bind(':tenencia_tierra', $data['tenencia_tierra']);
            $this->db->bind(':especie_cultivo_principal', $data['especie_cultivo_principal']);
            $this->db->bind(':numero_cabezas_colmenas', $data['numero_cabezas_colmenas']);

            // Bindeo de Checklist (TINYINT)
            $this->db->bind(':check_solicitud', $data['check_solicitud']);
            $this->db->bind(':check_identidad', $data['check_identidad']);
            $this->db->bind(':check_domicilio', $data['check_domicilio']);
            $this->db->bind(':check_curp_doc', $data['check_curp_doc']);
            $this->db->bind(':check_rfc_doc', $data['check_rfc_doc']);
            $this->db->bind(':check_manifiesto', $data['check_manifiesto']);
            $this->db->bind(':check_propiedad', $data['check_propiedad']);
            $this->db->bind(':check_finiquito', $data['check_finiquito']);
            $this->db->bind(':check_siniiga_doc', $data['check_siniiga_doc']);

            // Control y JSON
            $this->db->bind(':fase_proceso', $data['fase_proceso']);
            $this->db->bind(':respuestas_json', $data['json']);

            return $this->db->execute();
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    /**
     * Métodos para Dashboards y Estadísticas
     */
    public function getListadoMaestro() {
        $this->db->query("SELECT e.*, u.nombre_completo as encuestador 
                        FROM encuestas e
                        LEFT JOIN usuarios u ON e.usuario_id = u.id 
                        ORDER BY e.fecha_inicio DESC");
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

    public function getColoniasTlalpan($limit = 10) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal FROM cat_colonias LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getColoniasPorCP($cp) {
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal FROM cat_colonias WHERE codigo_postal = :cp ORDER BY nombre_asentamiento ASC");
        $this->db->bind(':cp', $cp);
        return $this->db->resultSet();
    }
}