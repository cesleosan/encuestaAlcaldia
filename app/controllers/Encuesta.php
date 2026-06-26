<?php
class Encuesta extends Controller {
    
    private $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo');
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . URLROOT . '/Auth'); exit; }
        if (($_SESSION['rol'] ?? '') === 'consulta') {
            header('Location: ' . URLROOT . '/Dashboard/index');
            exit;
        }

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
        if (($_SESSION['rol'] ?? '') === 'consulta') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'El perfil consulta es de solo lectura']);
            return;
        }
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

       $resultado = $this->encuestaModel->agregar($datosGuardar);

        // Retornamos el status 'duplicate' para que el JS genere un folio nuevo
        if (is_array($resultado) && isset($resultado['status']) && $resultado['status'] === 'duplicate') {
            echo json_encode($resultado);
            return;
        }
        // El modelo devuelve el folio (string) si la inserción fue correcta
        if ($resultado) {
            echo json_encode([
                'status' => 'success', 
                'folio' => $datosGuardar['folio']
            ]);
        } 
        else {
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
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'msg' => 'SesiÃ³n expirada']);
        exit;
    }

    if (($_SESSION['rol'] ?? '') === 'consulta') {
        echo json_encode([
            'status' => 'success',
            'maestro' => $this->encuestaModel->getListadoMaestro('COMITE')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

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

public function cambiarFaseVerificacion() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'msg' => 'Método no permitido']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'msg' => 'Sesión expirada']);
            exit;
        }

        $rol = $_SESSION['rol'] ?? '';
        $puedeAdministrar = in_array($rol, ['root', 'admin'], true);
        $puedeDictaminarComite = ($rol === 'consulta');

        if (!$puedeAdministrar && !$puedeDictaminarComite) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'Solo administrador/root puede mover fases desde este módulo']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $payload = $_POST;
        }

        $id = isset($payload['id']) ? (int)$payload['id'] : 0;
        $fase = strtoupper(trim($payload['fase'] ?? ''));

        $fasesValidas = [
            'SOLICITUD_INGRESADA',
            'VALIDACION_DOCS',
            'EN_REVISION',
            'COMITE',
            'APROBADO',
            'RECHAZADO'
        ];

        if ($id <= 0 || !in_array($fase, $fasesValidas, true)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Datos inválidos para cambio de fase']);
            exit;
        }

        $registro = $this->encuestaModel->getEncuestaById($id);
        if (!$registro) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Expediente no encontrado']);
            exit;
        }

        if ($puedeDictaminarComite && !$puedeAdministrar) {
            $fasesComite = ['EN_REVISION', 'APROBADO', 'RECHAZADO'];
            if (($registro->fase_proceso ?? '') !== 'COMITE' || !in_array($fase, $fasesComite, true)) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'El perfil consulta solo puede dictaminar expedientes que estÃ¡n en ComitÃ©'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        $estatusPorFase = [
            'SOLICITUD_INGRESADA' => 'Solicitud ingresada',
            'VALIDACION_DOCS'     => 'Validación docs',
            'EN_REVISION'         => 'En revisión',
            'COMITE'              => 'ComitÃ©',
            'APROBADO'            => 'Aprobado',
            'RECHAZADO'           => 'Rechazado'
        ];
        $estatus = $estatusPorFase[$fase] ?? $fase;

        if ($this->encuestaModel->actualizarFaseProceso($id, $fase, $estatus)) {
            echo json_encode([
                'status' => 'success',
                'msg' => 'Fase actualizada correctamente',
                'id' => $id,
                'fase' => $fase,
                'estatus' => $estatus
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg' => 'No se pudo actualizar la fase: ' . ($this->encuestaModel->getError() ?? 'error desconocido')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

public function getEvidenciasConsulta($id) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'consulta') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'msg' => 'Acceso no autorizado']);
        exit;
    }

    $registro = $this->encuestaModel->getEncuestaById((int)$id);
    if (!$registro || ($registro->fase_proceso ?? '') !== 'COMITE') {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Registro no disponible para consulta']);
        exit;
    }

    $convertir = function($evidencias) {
        return array_map(function($foto) {
            return [
                'id' => $foto->id,
                'url' => URLROOT . '/' . $foto->ruta_archivo
            ];
        }, $evidencias ?: []);
    };

    echo json_encode([
        'status' => 'success',
        'verificacion' => $convertir($this->encuestaModel->getEvidencias($registro->id, 'VERIFICACION_CAMPO')),
        'formatos_tecnicos' => $convertir($this->encuestaModel->getEvidencias($registro->id, 'FORMATOS_TECNICOS'))
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

}
