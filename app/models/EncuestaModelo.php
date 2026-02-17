<?php
class EncuestaModelo {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // 1. Verificar si ya existe el CURP (Regla: Solo una encuesta por persona)
    public function existeCurp($curp) {
        $this->db->query('SELECT folio FROM encuestas WHERE curp = :curp');
        $this->db->bind(':curp', $curp);
        $row = $this->db->single();
        return $row ? $row->folio : false;
    }
    public function getColoniasTlalpan($limit = 10) {
        // 🔥 Agregamos codigo_postal a la consulta
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getColoniasPorCP($cp) {
        // 🔥 También aquí para que al buscar por CP se mantenga la referencia
        $this->db->query("SELECT id, nombre_asentamiento AS asentamiento, codigo_postal 
                        FROM cat_colonias 
                        WHERE codigo_postal = :cp 
                        ORDER BY nombre_asentamiento ASC");
        $this->db->bind(':cp', $cp);
        return $this->db->resultSet();
    }
    // 2. Guardar la Encuesta
    public function agregar($datos) {
        try {
            $this->db->dbh->beginTransaction(); // Iniciamos transacción por seguridad

            // A) Insertamos los datos
            $sql = 'INSERT INTO encuestas (
                        usuario_id, curp, nombre, apellido_paterno, apellido_materno,
                        fecha_nacimiento, sexo, tiempo_residencia_tlalpan, tiempo_residencia_cdmx,
                        calle, numero_exterior, colonia_id, latitud, longitud, 
                        actividad_principal, respuestas_json, estatus, folio
                    ) VALUES (
                        :usuario_id, :curp, :nombre, :paterno, :materno,
                        :nacimiento, :sexo, :res_tlalpan, :res_cdmx,
                        :calle, :num_ext, :colonia_id, :lat, :lon,
                        :actividad, :json, "Completa", "TEMP"
                    )';
            
            $this->db->query($sql);
            
            // Bindeamos valores (mapeo rápido)
            $this->db->bind(':usuario_id', $_SESSION['user_id']);
            $this->db->bind(':curp', $datos['curp']);
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->bind(':paterno', $datos['paterno']);
            $this->db->bind(':materno', $datos['materno']);
            $this->db->bind(':nacimiento', $datos['fecha_nacimiento']); // Calculada del CURP
            $this->db->bind(':sexo', $datos['sexo']);                   // Calculado del CURP
            $this->db->bind(':res_tlalpan', $datos['tiempo_tlalpan']);
            $this->db->bind(':res_cdmx', $datos['tiempo_cdmx']);
            $this->db->bind(':calle', $datos['calle']);
            $this->db->bind(':num_ext', $datos['num_ext']);
            $this->db->bind(':colonia_id', $datos['colonia_id'] ?? null);
            $this->db->bind(':lat', $datos['latitud']);
            $this->db->bind(':lon', $datos['longitud']);
            $this->db->bind(':actividad', $datos['actividad_principal']);
            $this->db->bind(':json', json_encode($datos['respuestas_completas']));

            if ($this->db->execute()) {
                // B) Obtener ID insertado para generar Folio
                $id_insertado = $this->db->dbh->lastInsertId();
                
                // C) Generar Folio Consecutivo: TLP-2026-0001
                $anio = date('Y');
                $consecutivo = str_pad($id_insertado, 4, '0', STR_PAD_LEFT);
                $nuevo_folio = "TLP-{$anio}-{$consecutivo}";

                // D) Actualizar el folio en el registro
                $this->db->query('UPDATE encuestas SET folio = :folio WHERE id = :id');
                $this->db->bind(':folio', $nuevo_folio);
                $this->db->bind(':id', $id_insertado);
                $this->db->execute();

                $this->db->dbh->commit(); // Confirmar cambios
                return $nuevo_folio;
            } else {
                return false;
            }
        } catch (Exception $e) {
            $this->db->dbh->rollBack(); // Si falla algo, deshacer todo
            return false;
        }
    }

    public function obtenerUltimoId() {
        $this->db->query("SELECT MAX(id) as ultimo FROM encuestas");
        $row = $this->db->single();
        
        // Si la tabla está vacía, devolvemos 0, si no, el ID más alto
        return $row->ultimo ?? 0;
    }
}