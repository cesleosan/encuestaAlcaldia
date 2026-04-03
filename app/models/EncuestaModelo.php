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

    // --- MÉTODOS DE CONSULTA ---

    public function existeCurp($curp) {
        $this->db->query('SELECT folio FROM encuestas WHERE curp = :curp');
        $this->db->bind(':curp', $curp);
        $row = $this->db->single();
        return $row ? $row->folio : false;
    }

    public function getExpedienteCompleto($id) {
        $this->db->query("SELECT * FROM encuestas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // --- MÉTODOS DE ACTUALIZACIÓN ---

    public function actualizarExpediente($data) {
        try {
            // El SQL debe coincidir EXACTAMENTE con tu DESCRIBE de 61 campos
            $sql = "UPDATE encuestas SET 
                        nombre = :nombre, 
                        apellido_paterno = :paterno, 
                        apellido_materno = :materno, 
                        curp = :curp, 
                        rfc = :rfc,
                        tipo_id = :tipo_id,
                        numero_id = :numero_id,
                        tiene_discapacidad = :tiene_discap,
                        cual_discapacidad = :cual_discap,
                        grupo_etnico = :grupo_etnico,
                        grupo_etnico_cual = :grupo_etnico_cual,
                        escolaridad = :escolaridad, 
                        ocupacion = :ocupacion,
                        calle = :calle,
                        colonia_nombre = :colonia,
                        codigo_postal = :cp,
                        tel_particular = :tel_part,
                        tel_casa = :tel_casa,
                        tel_familiar = :tel_fam,
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
                        check_solicitud = :check_sol,
                        check_identidad = :check_id,
                        check_domicilio = :check_dom,
                        check_curp_doc = :check_curp,
                        check_rfc_doc = :check_rfc,
                        check_manifiesto = :check_man,
                        check_propiedad = :check_prop,
                        check_finiquito = :check_fin,
                        check_siniiga_doc = :check_sin,
                        fase_proceso = :fase, 
                        observaciones_capturista = :obs,
                        respuestas_json = :json 
                    WHERE id = :id";

            $this->db->query($sql);
            
            // Bindeos (Ajustados a los nombres que envía tu JavaScript FormData)
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':nombre', $data['nombre_productor']);
            $this->db->bind(':paterno', $data['paterno']);
            $this->db->bind(':materno', $data['materno']);
            $this->db->bind(':curp', $data['curp']);
            $this->db->bind(':rfc', $data['rfc'] ?? null);
            $this->db->bind(':tipo_id', $data['tipo_id'] ?? null);
            $this->db->bind(':numero_id', $data['numero_id'] ?? null);
            $this->db->bind(':tiene_discap', $data['tiene_discapacidad'] ?? 'NO');
            $this->db->bind(':cual_discap', $data['cual_discapacidad'] ?? 'NA');
            $this->db->bind(':grupo_etnico', $data['grupo_etnico'] ?? 'NO');
            $this->db->bind(':grupo_etnico_cual', $data['grupo_etnico_cual'] ?? 'NA');
            $this->db->bind(':escolaridad', $data['grado_estudios'] ?? null);
            $this->db->bind(':ocupacion', $data['ocupacion'] ?? null);
            $this->db->bind(':calle', $data['calle_numero'] ?? null);
            $this->db->bind(':colonia', $data['pueblo_colonia'] ?? null);
            $this->db->bind(':cp', $data['cp'] ?? null);
            $this->db->bind(':tel_part', $data['tel_particular'] ?? null);
            $this->db->bind(':tel_casa', $data['tel_casa'] ?? null);
            $this->db->bind(':tel_fam', $data['tel_recados'] ?? null);
            $this->db->bind(':linea_ayuda', $data['linea_ayuda'] ?? null);
            $this->db->bind(':siniiga', $data['siniiga_status'] ?? 'NO');
            $this->db->bind(':num_predios', $data['num_total_predios'] ?? 1);
            $this->db->bind(':superficie', $data['superficie_prod'] ?? 0);
            $this->db->bind(':tipo_doc', $data['tipo_documento_prop'] ?? null);
            $this->db->bind(':colonia_up', $data['pueblo_colonia_up'] ?? null);
            $this->db->bind(':parajes', $data['parajes'] ?? null);
            $this->db->bind(':tenencia', $data['tenencia_tierra'] ?? 'NA');
            $this->db->bind(':especie', $data['cultivo_principal'] ?? null);
            $this->db->bind(':cabezas', $data['num_animales'] ?? 0);
            $this->db->bind(':check_sol', isset($data['check_solicitud']) ? 1 : 0);
            $this->db->bind(':check_id', isset($data['check_identidad']) ? 1 : 0);
            $this->db->bind(':check_dom', isset($data['check_domicilio']) ? 1 : 0);
            $this->db->bind(':check_curp', isset($data['check_curp_doc']) ? 1 : 0);
            $this->db->bind(':check_rfc', isset($data['check_rfc_doc']) ? 1 : 0);
            $this->db->bind(':check_man', isset($data['check_manifiesto']) ? 1 : 0);
            $this->db->bind(':check_prop', isset($data['check_propiedad']) ? 1 : 0);
            $this->db->bind(':check_fin', isset($data['check_finiquito']) ? 1 : 0);
            $this->db->bind(':check_sin', isset($data['check_siniiga_doc']) ? 1 : 0);
            $this->db->bind(':fase', $data['fase_proceso']);
            $this->db->bind(':obs', $data['observaciones_capturista'] ?? '');
            $this->db->bind(':json', $data['json']);

            return $this->db->execute();
        } catch (Exception $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    // --- MÉTODOS DE ESTADÍSTICAS (KPIs) ---

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
            COUNT(DISTINCT usuario_id) as tecnicos_activos
            FROM encuestas");
        return $this->db->single();
    }
}