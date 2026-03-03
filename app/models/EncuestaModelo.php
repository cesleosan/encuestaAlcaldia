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
     * 2. Guardar la Encuesta (Versión Blindada)
     */
    public function agregar($datos) {
        try {
            $sql = 'INSERT INTO encuestas (
                        usuario_id, curp, nombre, apellido_paterno, apellido_materno,
                        fecha_nacimiento, sexo, tiempo_residencia_tlalpan, tiempo_residencia_cdmx,
                        calle, numero_exterior, colonia_id, latitud, longitud, 
                        actividad_principal, respuestas_json, estatus, folio
                    ) VALUES (
                        :usuario_id, :curp, :nombre, :paterno, :materno,
                        :nacimiento, :sexo, :res_tlalpan, :res_cdmx,
                        :calle, :num_ext, :colonia_id, :lat, :lon,
                        :actividad, :json, "Completa", :folio
                    )';
            
            $this->db->query($sql);
            
            // Bindeos de texto y enteros
            $this->db->bind(':folio', $datos['folio']);
            $this->db->bind(':usuario_id', $_SESSION['user_id']);
            $this->db->bind(':curp', $datos['curp']);
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->bind(':paterno', $datos['paterno']);
            $this->db->bind(':materno', $datos['materno']);
            $this->db->bind(':nacimiento', $datos['fecha_nacimiento']);
            $this->db->bind(':sexo', $datos['sexo']);
            $this->db->bind(':res_tlalpan', $datos['tiempo_tlalpan']);
            $this->db->bind(':res_cdmx', $datos['tiempo_residencia_cdmx'] ?? $datos['tiempo_cdmx']);
            $this->db->bind(':calle', $datos['calle']);
            $this->db->bind(':num_ext', $datos['num_ext']);
            $this->db->bind(':colonia_id', !empty($datos['colonia_id']) ? $datos['colonia_id'] : null);
            $this->db->bind(':actividad', $datos['actividad_principal']);

            // --- CORRECCIÓN QUIRÚRGICA: COORDENADAS ---
            // Si latitud o longitud vienen como string vacío "", mandamos NULL para evitar el error de MariaDB
            $lat = ($datos['latitud'] !== '') ? $datos['latitud'] : null;
            $lon = ($datos['longitud'] !== '') ? $datos['longitud'] : null;
            $this->db->bind(':lat', $lat);
            $this->db->bind(':lon', $lon);

            // Asegurar que el JSON se codifique correctamente
            $jsonRespuestas = json_encode($datos['respuestas_completas'], JSON_UNESCAPED_UNICODE);
            $this->db->bind(':json', $jsonRespuestas);

            if ($this->db->execute()) {
                return $datos['folio']; 
            }
            
            // Si llega aquí sin excepción pero falló el execute
            $this->ultimoError = "Fallo la ejecución de la consulta (execute returned false).";
            return false;

        } catch (PDOException $e) {
            // Capturamos el error real de SQL (ej: columna inexistente o valor inválido)
            $this->ultimoError = $e->getMessage();
            return false;
        } catch (Exception $e) {
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
}