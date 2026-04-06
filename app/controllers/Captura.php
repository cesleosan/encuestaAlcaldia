<?php

class Captura extends Controller {
    private $encuestaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Verificación de seguridad y roles
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['root', 'supervisor', 'capturista'])) {
            header('Location: ' . URLROOT . '/Auth');
            exit;
        }
        $this->encuestaModel = $this->model('EncuestaModelo');

        // DEFINICIÓN DE PUBROOT: Asegura la ruta física para el servidor (Nginx/Linux)
        if (!defined('PUBROOT')) {
            define('PUBROOT', dirname(APPROOT) . '/public');
        }
    }

    public function index() {
        $this->view('captura/index');
    }

    /**
     * Motor de Actualización: Gestiona Datos, Archivos Físicos y Estados de Cotejo
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            // 1. Obtener el registro actual
            $registro = $this->encuestaModel->getExpedienteCompleto($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'No se encontró el registro']);
                return;
            }

            // 2. LÓGICA DE DIRECTORIOS
            $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
            $rutaBase = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;

            // 3. MAPEO DE DOCUMENTOS (JS Key => Prefijo Archivo => Columna DB)
            $mapeoDoc = [
                'solicitud'   => ['file' => 'SOLICITUD',   'col' => 'check_solicitud'],
                'identidad'   => ['file' => 'IDENTIDAD',   'col' => 'check_identidad'],
                'domicilio'   => ['file' => 'DOMICILIO',   'col' => 'check_domicilio'],
                'curp_doc'    => ['file' => 'CURP',        'col' => 'check_curp_doc'],
                'rfc_doc'     => ['file' => 'RFC',         'col' => 'check_rfc_doc'],
                'manifiesto'  => ['file' => 'MANIFIESTO',   'col' => 'check_manifiesto'],
                'propiedad'   => ['file' => 'PROPIEDAD',   'col' => 'check_propiedad'],
                'finiquito'   => ['file' => 'FINIQUITO',   'col' => 'check_finiquito'],
                'siniiga_doc' => ['file' => 'SINIIGA',     'col' => 'check_siniiga_doc']
            ];

            $dbChecks = [];

            foreach ($mapeoDoc as $key => $info) {
                $columna = $info['col'];
                $prefijo = $info['file'];

                // A) ELIMINACIÓN FÍSICA (Si se activó la 'X' en el JS)
                if (isset($_POST['delete_' . $key]) && $_POST['delete_' . $key] == '1') {
                    $patron = $rutaBase . '/' . $folioCarpeta . '_' . $prefijo . '.*';
                    $archivosViejos = glob($patron);
                    if ($archivosViejos) {
                        foreach ($archivosViejos as $f) { @unlink($f); }
                    }
                    $dbChecks[$columna] = 0; // Apagamos el check en la DB
                } 
                // B) CARGA DE ARCHIVO NUEVO
                elseif (isset($_FILES['file_' . $key]) && $_FILES['file_' . $key]['error'] === UPLOAD_ERR_OK) {
                    if (!is_dir($rutaBase)) { @mkdir($rutaBase, 0775, true); }
                    
                    $ext = strtolower(pathinfo($_FILES['file_' . $key]['name'], PATHINFO_EXTENSION));
                    $nombreFinal = $folioCarpeta . "_" . $prefijo . "." . $ext;
                    $destino = $rutaBase . "/" . $nombreFinal;

                    if (move_uploaded_file($_FILES['file_' . $key]['tmp_name'], $destino)) {
                        chmod($destino, 0664);
                        $dbChecks[$columna] = 1; // Encendemos el check en la DB
                    }
                } 
                // C) RESPETAR ESTADO ACTUAL (Si no se borró ni subió nada)
                else {
                    $dbChecks[$columna] = isset($_POST[$columna]) ? 1 : 0;
                }
            }

            // 4. PREPARACIÓN DE DATOS PARA LA BASE DE DATOS (Mapeo 1:1 con describe)
            $data = [
                'id'                => $id,
                'nombre_productor'  => mb_strtoupper($_POST['nombre_productor'], 'UTF-8'),
                'paterno'           => mb_strtoupper($_POST['paterno'], 'UTF-8'),
                'materno'           => mb_strtoupper($_POST['materno'], 'UTF-8'),
                'curp'              => mb_strtoupper($_POST['curp'], 'UTF-8'),
                'rfc'               => mb_strtoupper($_POST['rfc'], 'UTF-8'),
                'tipo_id'           => $_POST['tipo_id'] ?? 'INE',
                'numero_id'         => $_POST['numero_id'] ?? '',
                'estado_civil'      => $_POST['estado_civil'] ?? '',
                'grado_estudios'    => $_POST['grado_estudios'] ?? '',
                'ocupacion'         => mb_strtoupper($_POST['ocupacion'], 'UTF-8'),
                'tiene_discapacidad'=> $_POST['tiene_discapacidad'] ?? 'NO',
                'cual_discapacidad' => mb_strtoupper($_POST['cual_discapacidad'] ?? 'NA', 'UTF-8'),
                'grupo_etnico'      => $_POST['grupo_etnico'] ?? 'NO',
                'grupo_etnico_cual' => mb_strtoupper($_POST['grupo_etnico_cual'] ?? 'NA', 'UTF-8'),
                'calle_numero'      => mb_strtoupper($_POST['calle_numero'], 'UTF-8'),
                'pueblo_colonia'    => mb_strtoupper($_POST['pueblo_colonia'], 'UTF-8'),
                'cp'                => $_POST['cp'] ?? '',
                'tel_particular'    => $_POST['tel_particular'] ?? '',
                'tel_casa'          => $_POST['tel_casa'] ?? '',
                'tel_recados'       => $_POST['tel_recados'] ?? '', 
                'linea_ayuda'       => $_POST['linea_ayuda'] ?? '',
                'siniiga_status'    => $_POST['siniiga_status'] ?? 'NO',
                'num_total_predios' => $_POST['num_total_predios'] ?? 1,
                'superficie_prod'   => $_POST['superficie_prod'] ?? 0,
                'tipo_documento_prop' => mb_strtoupper($_POST['tipo_documento_prop'] ?? '', 'UTF-8'),
                'pueblo_colonia_up' => mb_strtoupper($_POST['pueblo_colonia_up'] ?? '', 'UTF-8'),
                'parajes'           => mb_strtoupper($_POST['parajes'] ?? 'NA', 'UTF-8'),
                'tenencia_tierra'   => $_POST['tenencia_tierra'] ?? 'NA',
                'cultivo_principal' => mb_strtoupper($_POST['cultivo_principal'] ?? '', 'UTF-8'),
                'num_animales'      => $_POST['num_animales'] ?? 0,
                
                // Lógica de Fases
                'fase_proceso'      => ($registro->fase_proceso == 'EMPADRONADO') 
                                        ? 'SOLICITUD_INGRESADA' 
                                        : ($_POST['fase_proceso'] ?? $registro->fase_proceso),
                
                // Checks Sincronizados
                'check_solicitud'   => $dbChecks['check_solicitud'],
                'check_identidad'   => $dbChecks['check_identidad'],
                'check_domicilio'   => $dbChecks['check_domicilio'],
                'check_curp_doc'    => $dbChecks['check_curp_doc'],
                'check_rfc_doc'     => $dbChecks['check_rfc_doc'],
                'check_manifiesto'  => $dbChecks['check_manifiesto'],
                'check_propiedad'   => $dbChecks['check_propiedad'],
                'check_finiquito'   => $dbChecks['check_finiquito'],
                'check_siniiga_doc' => $dbChecks['check_siniiga_doc'],
                
                'json'              => $registro->respuestas_json 
            ];

            // 5. EJECUTAR EN MODELO
            if ($this->encuestaModel->actualizarExpediente($data)) {
                echo json_encode(['status' => 'success', 'msg' => '¡Expediente y archivos actualizados con éxito!']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar el registro en la base de datos.']);
            }
        }
    }

    /**
     * Escanea archivos físicos para la UI
     */
    public function verificarArchivos($id) {
        $registro = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$registro) { echo json_encode([]); return; }

        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $rutaFisica = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;
        
        // URL compatible con Nginx (sin /public)
        $urlBase = URLROOT . '/uploads/expedientes/' . $folioCarpeta;

        $archivosEncontrados = [];

        if (is_dir($rutaFisica)) {
            $archivos = scandir($rutaFisica);
            foreach ($archivos as $archivo) {
                if ($archivo !== '.' && $archivo !== '..') {
                    $archivosEncontrados[] = [
                        'nombre' => $archivo,
                        'url'    => $urlBase . '/' . $archivo,
                        'tipo'   => pathinfo($archivo, PATHINFO_FILENAME)
                    ];
                }
            }
        }
        echo json_encode($archivosEncontrados);
    }
}