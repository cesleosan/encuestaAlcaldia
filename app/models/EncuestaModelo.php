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
            // ✅ SQL ACTUALIZADO: Agregamos columnas de métricas y conclusión
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
            
            // Bindeos de Identidad y Tiempo
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
            
            // Bindeos de Ubicación
            $this->db->bind(':calle', $datos['calle']);
            $this->db->bind(':num_ext', $datos['num_ext']);
            $this->db->bind(':colonia_id', $datos['colonia_id'] ?? null);
            $this->db->bind(':colonia_nom', $datos['colonia_nombre']); // 🔥 Captura "SAN MIGUEL TOPILEJO" etc.
            
            // Bindeos de Coordenadas (Manejo de NULLs quirúrgico)
            $lat = ($datos['latitud'] !== '' && $datos['latitud'] !== 0) ? $datos['latitud'] : null;
            $lon = ($datos['longitud'] !== '' && $datos['longitud'] !== 0) ? $datos['longitud'] : null;
            $this->db->bind(':lat', $lat);
            $this->db->bind(':lon', $lon);

            // 🔥 Bindeos de Métricas (Pantalla 47/48)
            $this->db->bind(':actividad', $datos['actividad_principal']);
            $this->db->bind(':superficie', $datos['superficie_total']);
            $this->db->bind(':volumen', $datos['volumen_total']);
            $this->db->bind(':unidad', $datos['unidad_medida']);

            // Estado y JSON
            $this->db->bind(':estatus', $datos['estatus']);
            $this->db->bind(':fecha_fin', $datos['fecha_conclusion']); // 🔥 Ya no será NULL
            
            // 🚨 IMPORTANTE: En el controlador ya hicimos json_encode. 
            // Aquí bindeamos la cadena directamente para evitar doble escape.
            $this->db->bind(':json', $datos['respuestas_completas']);

            if ($this->db->execute()) {
                return $datos['folio']; 
            }
            
            $this->ultimoError = "Fallo la ejecución de la consulta MariaDB.";
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
     * Obtiene el ID más alto registrado
     */
    public function obtenerUltimoId() {
        $this->db->query("SELECT MAX(id) as ultimo FROM encuestas");
        $row = $this->db->single();
        return $row->ultimo ?? 0;
    }

    public function obtenerCoordenadasMapa() {
        $this->db->query("SELECT folio, latitud, longitud, actividad_principal 
                        FROM encuestas 
                        WHERE latitud IS NOT NULL AND longitud IS NOT NULL");
        return $this->db->resultSet();
    }

    public function getConteoActividades() {
        $this->db->query("SELECT actividad_principal, COUNT(*) as total 
                        FROM encuestas 
                        GROUP BY actividad_principal");
        return $this->db->resultSet();
    }
    // En EncuestaModelo.php

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
        $this->db->query("SELECT 
            colonia_nombre, 
            COUNT(*) as total, 
            SUM(superficie_total) as hectareas 
            FROM encuestas 
            WHERE colonia_nombre IS NOT NULL 
            GROUP BY colonia_nombre 
            ORDER BY hectareas DESC 
            LIMIT 10");
        return $this->db->resultSet();
    }

    public function getProblemasPrincipales() {
        // Esta consulta extrae el problema del JSON de forma masiva (Pantalla 11, campo problema_principal)
        // Nota: Como ya tienes superficie_total fuera, lo ideal sería mapear 'problema' también.
        // Por ahora, lo sacamos del JSON directamente:
        $this->db->query("SELECT 
            -- Extraemos el valor pero solo si el JSON es válido
            JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.11[2].value')) as problema,
            COUNT(*) as total
            FROM encuestas
            WHERE respuestas_json IS NOT NULL 
            AND JSON_VALID(respuestas_json) -- 🔥 Esto evita que truene con JSONs mal formados
            GROUP BY problema
            HAVING problema IS NOT NULL");
        return $this->db->resultSet();
    }

    public function getListadoMaestro() {
    $this->db->query("SELECT 
        e.id, 
        e.folio, 
        u.nombre_completo as encuestador, 
        e.actividad_principal, 
        e.colonia_nombre,
        e.superficie_total, 
        e.fecha_inicio,
        e.estatus,
        e.respuestas_json 
        FROM encuestas e
        LEFT JOIN usuarios u ON e.usuario_id = u.id 
        ORDER BY e.fecha_inicio DESC");
        
    return $this->db->resultSet();
}

    public function getTendenciaDiaria() {
        $this->db->query("SELECT 
            DATE(fecha_inicio) as fecha, 
            COUNT(*) as total 
            FROM encuestas 
            GROUP BY DATE(fecha_inicio) 
            ORDER BY fecha ASC");
        return $this->db->resultSet();
    }
    /**
     * Obtiene una encuesta específica por su ID
     */
    public function getEncuestaById($id) {
        $this->db->query("SELECT * FROM encuestas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Actualización integral del expediente (Física + JSON)
     */
    public function actualizarExpediente($data) {
        $this->db->query("UPDATE encuestas SET 
                            nombre = :nombre, 
                            apellido_paterno = :paterno, 
                            apellido_materno = :materno, 
                            curp = :curp, 
                            colonia_nombre = :colonia,
                            superficie_total = :superficie, 
                            fase_proceso = :fase, 
                            respuestas_json = :json 
                          WHERE id = :id");
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':paterno', $data['paterno']);
        $this->db->bind(':materno', $data['materno']);
        $this->db->bind(':curp', $data['curp']);
        $this->db->bind(':colonia', $data['colonia']);
        $this->db->bind(':superficie', $data['superficie']);
        $this->db->bind(':fase', $data['fase']);
        $this->db->bind(':json', $data['json']);

        return $this->db->execute();
    }
}