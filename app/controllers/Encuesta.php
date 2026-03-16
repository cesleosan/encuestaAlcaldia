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
        $sufijoAleatorio = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
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

        // 1. Mapeo de Identidad (Pantalla 2)
        $curp = strtoupper($this->buscarValor($respuestas[2], 'curp'));
        
        // Validar Duplicado Real
        $folioExistente = $this->encuestaModel->existeCurp($curp);
        if ($folioExistente) {
            echo json_encode(['status' => 'error', 'msg' => "El CURP ya fue registrado con folio: $folioExistente"]);
            return;
        }

        // Parsing de nombre
        $nombre_completo = $this->buscarValor($respuestas[2], 'nombre_productor'); 
        $partes = explode(' ', trim($nombre_completo));
        $materno = (count($partes) > 2) ? array_pop($partes) : '';
        $paterno = (count($partes) > 1) ? array_pop($partes) : '';
        $nombre = implode(' ', $partes);

        // 2. Mapeo de Ubicación (Pantalla 5 y 6)
        $lat = $respuestas[6]['latitud'] ?? 0;
        $lon = $respuestas[6]['longitud'] ?? 0;
        $calle = $respuestas[6]['calle_numero'] ?? '';
        
        // Manejo de Colonia (OTRO vs Catálogo)
        $pueblo_seleccionado = $this->buscarValor($respuestas[5], 'pueblo_colonia');
        $pueblo_otro = $this->buscarValor($respuestas[5], 'pueblo_otro');
        $colonia_final = ($pueblo_seleccionado === 'OTRO') ? $pueblo_otro : $pueblo_seleccionado;

        // 3. Mapeo de Actividad y Métricas (Pantalla 18 y 47)
        $actividad = $respuestas[18] ?? [];
        $actividad_str = is_array($actividad) ? implode(', ', array_map(function($item) { return $item['value']; }, $actividad)) : 'OTRO';

        // 🔥 EXTRACCIÓN QUIRÚRGICA DE MÉTRICAS (Lo que faltaba en el registro de Omar)
        $superficie = $this->buscarValor($respuestas[47], 'superficie_prod');
        $volumen = $this->buscarValor($respuestas[47], 'volumen_prod');
        $unidad = $this->buscarValor($respuestas[48], 'unidad_medida');

        // 4. Preparación del Array de Carga
        $datosGuardar = [
            'folio'                => $this->buscarValor($respuestas[2], 'folio'),
            'usuario_id'           => $_SESSION['user_id'],
            'curp'                 => $curp,
            'nombre'               => $nombre,
            'paterno'              => $paterno,
            'materno'              => $materno,
            'fecha_nacimiento'     => $this->curpToFecha($curp),
            'sexo'                 => $this->curpToSexo($curp),
            'tiempo_tlalpan'       => $this->buscarValor($respuestas[2], 'tiempo_residencia'),
            'tiempo_cdmx'          => $this->buscarValor($respuestas[4], 'tiempo_residencia_cdmx'),
            'calle'                => $calle,
            'num_ext'              => 'S/N',
            'colonia_nombre'       => $colonia_final, // Columna de texto para reportes rápidos
            'latitud'              => $lat,
            'longitud'             => $lon,
            'actividad_principal'  => $actividad_str,
            'superficie_total'     => floatval($superficie), // Aseguramos que sea número
            'volumen_total'        => floatval($volumen),
            'unidad_medida'        => $unidad,
            'estatus'              => 'Completa',
            'fecha_conclusion'     => date('Y-m-d H:i:s'), // 🔥 Registramos el cierre real
            'respuestas_completas' => $json,
        ];

        $exito = $this->encuestaModel->agregar($datosGuardar);

        if ($exito) {
            echo json_encode(['status' => 'success', 'folio' => $datosGuardar['folio']]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'msg' => 'Error al insertar en base de datos',
                'detalles' => $this->encuestaModel->getError() 
            ]);
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
    // Dentro de class Encuesta ...

public function getTodasLasColonias() {
    // 1. Limpiar cualquier eco previo
    if (ob_get_length()) ob_clean();
    
    // 2. Establecer cabecera JSON
    header('Content-Type: application/json');
    
    // 3. Consultar TODAS (ajustamos el límite para precarga completa)
    // Tlalpan tiene aproximadamente 200-300 asentamientos/colonias
    $colonias = $this->encuestaModel->getColoniasTlalpan(2000); 
    
    echo json_encode($colonias, JSON_UNESCAPED_UNICODE);
    exit;
}

public function getEstadisticas() {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $datos = [
        'kpis'        => $this->encuestaModel->getDashboardKPIs(),
        'puntos'      => $this->encuestaModel->obtenerCoordenadasMapa(),
        'actividades' => $this->encuestaModel->getConteoActividades(),
        'colonias'    => $this->encuestaModel->getProduccionPorColonia(),
        'problemas'   => $this->encuestaModel->getProblemasPrincipales(),
        'maestro'     => $this->encuestaModel->getListadoMaestro(),
        'tendencia'   => $this->encuestaModel->getTendenciaDiaria()
    ];

    echo json_encode($datos);
    exit;
}
}