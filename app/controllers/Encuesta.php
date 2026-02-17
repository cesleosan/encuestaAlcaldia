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

        // ✅ MEJORA: Folio único basado en el ID del usuario y tiempo para evitar colisiones
        $userId = $_SESSION['user_id'];
        $sufijoAleatorio = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        $folioPro = "TLP-26-" . $userId . "-" . $sufijoAleatorio;

        $data = [
            'banco' => $preguntaModel->getBanco(),
            'nombre_tecnico' => $_SESSION['nombre'],
            'user_id' => $userId,
            'folio_automatico' => $folioPro,
            'colonias_iniciales' => $coloniasPreview 
        ];
        $this->view('survey/cuestionario', $data);
    }

    public function guardar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Sesión expirada']);
            return;
        }

        $json = file_get_contents('php://input');
        $respuestas = json_decode($json, true);

        if (!$respuestas) {
            echo json_encode(['status' => 'error', 'msg' => 'No se recibieron datos']);
            return;
        }

        // --- MAPEO DE DATOS PROFESIONAL ---
        $curp = strtoupper($this->buscarValor($respuestas[2], 'curp'));

        // 2. Validar Duplicado Real (CURP)
        $folioExistente = $this->encuestaModel->existeCurp($curp);
        if ($folioExistente) {
            echo json_encode(['status' => 'error', 'msg' => "El CURP ya fue registrado con folio: $folioExistente"]);
            return;
        }

        // Mejora en parsing de nombre (evita errores si solo ponen un apellido)
        $nombre_completo = $this->buscarValor($respuestas[2], 'nombre_productor'); 
        $partes = explode(' ', trim($nombre_completo));
        $materno = (count($partes) > 2) ? array_pop($partes) : '';
        $paterno = (count($partes) > 1) ? array_pop($partes) : '';
        $nombre = implode(' ', $partes);

        $lat = $respuestas[6]['latitud'] ?? 0;
        $lon = $respuestas[6]['longitud'] ?? 0;
        $calle = $respuestas[6]['calle_numero'] ?? '';
        
        $actividad = $respuestas[18] ?? [];
        $actividad_str = is_array($actividad) ? implode(', ', array_map(function($item) { return $item['value']; }, $actividad)) : 'OTRO';

        $datosGuardar = [
            'folio' => $this->buscarValor($respuestas[1], 'folio'), // Tomamos el folio generado en el front
            'usuario_id' => $_SESSION['user_id'],
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

        $nuevoFolio = $this->encuestaModel->agregar($datosGuardar);

        if ($nuevoFolio) {
            echo json_encode(['status' => 'success', 'folio' => $nuevoFolio]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al insertar en base de datos']);
        }
    }

    private function buscarValor($dataStep, $campoName) {
        if (!is_array($dataStep)) return '';
        foreach ($dataStep as $item) {
            if (isset($item['name']) && $item['name'] === $campoName) return $item['value'];
        }
        return '';
    }

    private function curpToSexo($curp) {
        if(strlen($curp) < 11) return 'HOMBRE';
        return (substr($curp, 10, 1) == 'H') ? 'HOMBRE' : 'MUJER';
    }

    private function curpToFecha($curp) {
        if(strlen($curp) < 10) return date('Y-m-d');
        $yy = substr($curp, 4, 2);
        $mm = substr($curp, 6, 2);
        $dd = substr($curp, 8, 2);
        $prefix = ($yy < 30) ? '20' : '19';
        return "$prefix$yy-$mm-$dd";
    }

    public function buscarColonias($cp) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(strlen($cp) === 5 ? $this->encuestaModel->getColoniasPorCP($cp) : []);
        exit;
    }
}