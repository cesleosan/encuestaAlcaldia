<?php
class Encuesta extends Controller {
    
    private $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo');
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . URLROOT . '/Auth'); exit; }

        $coloniasPreview = $this->encuestaModel->getColoniasTlalpan(10);
        $preguntaModel = $this->model('PreguntaModel');

        $data = [
            'banco' => $preguntaModel->getBanco(),
            'nombre_tecnico' => $_SESSION['nombre'],
            'folio_automatico' => "TLP-2026-" . str_pad($this->encuestaModel->obtenerUltimoId() + 1, 4, "0", STR_PAD_LEFT),
            'colonias_iniciales' => $coloniasPreview // <-- Preview para la pantalla 5
        ];
        $this->view('survey/cuestionario', $data);
    }
    public function guardar() {
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Sesión expirada']);
            return;
        }

        // Obtener el JSON enviado por JS
        $json = file_get_contents('php://input');
        $respuestas = json_decode($json, true);

        if (!$respuestas) {
            echo json_encode(['status' => 'error', 'msg' => 'No se recibieron datos']);
            return;
        }

        // --- MAPEO DE DATOS ---
        
        // 1. Datos Personales
        $nombre_completo = $this->buscarValor($respuestas[2], 'nombre_productor'); 
        $partes_nombre = explode(' ', $nombre_completo);
        $paterno = array_pop($partes_nombre);
        $materno = ''; 
        $nombre = implode(' ', $partes_nombre);

        $curp = strtoupper($this->buscarValor($respuestas[2], 'curp'));

        // 2. Validar Duplicado
        $folioExistente = $this->encuestaModel->existeCurp($curp);
        if ($folioExistente) {
            echo json_encode(['status' => 'error', 'msg' => "El CURP ya fue registrado con folio: $folioExistente"]);
            return;
        }

        // 3. Ubicación
        $lat = $respuestas[6]['latitud'] ?? 0;
        $lon = $respuestas[6]['longitud'] ?? 0;
        $calle = $respuestas[6]['calle_numero'] ?? '';
        
        // 4. Actividad
        $actividad = $respuestas[18] ?? [];
        if (is_array($actividad)) {
            $vals = array_map(function($item) { return $item['value']; }, $actividad);
            $actividad_str = implode(', ', $vals);
        } else {
            $actividad_str = 'DESCONOCIDO';
        }

        // 5. Preparar Array
        $datosGuardar = [
            'curp' => $curp,
            'nombre' => $nombre,
            'paterno' => $paterno,
            'materno' => $materno,
            'fecha_nacimiento' => $this->curpToFecha($curp),
            'sexo' => $this->curpToSexo($curp),
            'tiempo_tlalpan' => $this->buscarValor($respuestas[2], 'tiempo_residencia'),
            'tiempo_cdmx' => $this->buscarValor($respuestas[4], 'tiempo_residencia_cdmx'),
            'calle' => $calle,
            'num_ext' => 'S/N',
            'colonia_id' => null,
            'latitud' => $lat,
            'longitud' => $lon,
            'actividad_principal' => $actividad_str,
            'respuestas_completas' => $respuestas
        ];

        // 6. Guardar
        $nuevoFolio = $this->encuestaModel->agregar($datosGuardar);

        if ($nuevoFolio) {
            echo json_encode(['status' => 'success', 'folio' => $nuevoFolio]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al insertar en base de datos']);
        }
    }

    // --- HELPERS PRIVADOS ---
    private function buscarValor($dataStep, $campoName) {
        if (!is_array($dataStep)) return '';
        foreach ($dataStep as $item) {
            if ($item['name'] === $campoName) return $item['value'];
        }
        return '';
    }

    private function curpToSexo($curp) {
        if(strlen($curp) < 11) return 'HOMBRE'; // Fallback
        $letra = substr($curp, 10, 1);
        return ($letra == 'H') ? 'HOMBRE' : 'MUJER';
    }

    private function curpToFecha($curp) {
        if(strlen($curp) < 10) return date('Y-m-d'); // Fallback
        $yy = substr($curp, 4, 2);
        $mm = substr($curp, 6, 2);
        $dd = substr($curp, 8, 2);
        $prefix = ($yy < 30) ? '20' : '19';
        return "$prefix$yy-$mm-$dd";
    }

    public function buscarColonias($cp) {
    // Limpiamos cualquier salida previa para enviar JSON puro
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if (strlen($cp) === 5) {
        // Llamamos al método del modelo que acabamos de arreglar
        $resultados = $this->encuestaModel->getColoniasPorCP($cp);
        echo json_encode($resultados);
    } else {
        echo json_encode([]);
    }
    exit; // 🔥 CRÍTICO: Detenemos la ejecución para que no cargue nada más
}
}