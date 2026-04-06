<?php

class Captura extends Controller {
    private $encuestaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Seguridad: Solo roles autorizados
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['root', 'supervisor', 'capturista'])) {
            header('Location: ' . URLROOT . '/Auth');
            exit;
        }
        
        $this->encuestaModel = $this->model('EncuestaModelo');

        if (!defined('PUBROOT')) {
            define('PUBROOT', dirname(APPROOT) . '/public');
        }
    }

    public function index() {
        $this->view('captura/index');
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            // 1. Obtener registro actual
            $registro = $this->encuestaModel->getExpedienteCompleto($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'No se encontró el registro']);
                return;
            }

            $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
            $rutaBase = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;

            // 2. MAPEO: Clave JS => Prefijo Archivo => Columna DB
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

                // REGLA 1: Prioridad Manual - Tomar lo que diga el POST (0 o 1)
                // Se asume que el JS envía 0 si no está checado
                $dbChecks[$columna] = (isset($_POST[$columna]) && $_POST[$columna] == '1') ? 1 : 0;

                // REGLA 2: Si el usuario presionó la 'X' para borrar físicamente
                if (isset($_POST['delete_' . $key]) && $_POST['delete_' . $key] == '1') {
                    $patron = $rutaBase . '/' . $folioCarpeta . '_' . $prefijo . '.*';
                    $archivosViejos = glob($patron);
                    if ($archivosViejos) {
                        foreach ($archivosViejos as $f) { @unlink($f); }
                    }
                    $dbChecks[$columna] = 0; // Al borrar el archivo, forzamos el check a 0
                } 
                
                // REGLA 3: Si está subiendo un archivo nuevo en este momento
                if (isset($_FILES['file_' . $key]) && $_FILES['file_' . $key]['error'] === UPLOAD_ERR_OK) {
                    if (!is_dir($rutaBase)) { @mkdir($rutaBase, 0775, true); }
                    
                    $ext = strtolower(pathinfo($_FILES['file_' . $key]['name'], PATHINFO_EXTENSION));
                    $nombreFinal = $folioCarpeta . "_" . $prefijo . "." . $ext;
                    $destino = $rutaBase . "/" . $nombreFinal;

                    if (move_uploaded_file($_FILES['file_' . $key]['tmp_name'], $destino)) {
                        chmod($destino, 0664);
                        $dbChecks[$columna] = 1; // Si sube archivo nuevo, forzamos el check a 1
                    }
                }
            }

            // 3. PREPARACIÓN DE DATA (Mapeo exacto a MariaDB)
            $data = [
                'id'                => $id,
                'curp'              => mb_strtoupper($_POST['curp'], 'UTF-8'),
                'rfc'               => mb_strtoupper($_POST['rfc'], 'UTF-8'),
                'nombre'            => mb_strtoupper($_POST['nombre_productor'], 'UTF-8'),
                'apellido_paterno'  => mb_strtoupper($_POST['paterno'], 'UTF-8'),
                'apellido_materno'  => mb_strtoupper($_POST['materno'], 'UTF-8'),
                'tipo_id'           => !empty($_POST['tipo_id']) ? $_POST['tipo_id'] : $registro->tipo_id,
                'numero_id'         => $_POST['numero_id'] ?? '',
                'estado_civil'      => $_POST['estado_civil'] ?? '',
                'escolaridad'       => $_POST['grado_estudios'] ?? '',
                'ocupacion'         => mb_strtoupper($_POST['ocupacion'], 'UTF-8'),
                'tiene_discapacidad'=> $_POST['tiene_discapacidad'] ?? 'NO',
                'cual_discapacidad' => mb_strtoupper($_POST['cual_discapacidad'] ?? 'NA', 'UTF-8'),
                'grupo_etnico'      => $_POST['grupo_etnico'] ?? 'NO',
                'grupo_etnico_cual' => mb_strtoupper($_POST['grupo_etnico_cual'] ?? 'NA', 'UTF-8'),
                'calle'             => mb_strtoupper($_POST['calle_numero'], 'UTF-8'),
                'colonia_nombre'    => mb_strtoupper($_POST['pueblo_colonia'], 'UTF-8'),
                'codigo_postal'     => $_POST['cp'] ?? '',
                'tel_particular'    => $_POST['tel_particular'] ?? '',
                'tel_casa'          => $_POST['tel_casa'] ?? '',
                'tel_familiar'      => $_POST['tel_recados'] ?? '',
                'linea_ayuda'       => $_POST['tipo_produccion'] ?? 'AGRICOLA',
                'registro_siniiga'  => $_POST['siniiga_status'] ?? 'NO',
                'num_total_predios' => $_POST['num_total_predios'] ?? 1,
                'superficie_total'  => $_POST['superficie_prod'] ?? 0,
                'tipo_documento_propiedad' => mb_strtoupper($_POST['tipo_documento_prop'] ?? '', 'UTF-8'),
                'pueblo_colonia_up' => mb_strtoupper($_POST['pueblo_colonia_up'] ?? '', 'UTF-8'),
                'parajes'           => mb_strtoupper($_POST['parajes'] ?? 'NA', 'UTF-8'),
                'tenencia_tierra'   => !empty($_POST['tenencia_tierra']) ? $_POST['tenencia_tierra'] : 'NA',
                'especie_cultivo_principal' => mb_strtoupper($_POST['cultivo_principal'] ?? '', 'UTF-8'),
                'numero_cabezas_colmenas'   => $_POST['num_animales'] ?? 0,
                
                // Fases
                'fase_proceso'      => ($registro->fase_proceso == 'EMPADRONADO') 
                                        ? 'SOLICITUD_INGRESADA' 
                                        : ($_POST['fase_proceso'] ?? $registro->fase_proceso),
                
                // ✅ CHECKS MANUALES (Obedecen al formulario)
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

            // 4. EJECUTAR EN EL MODELO
            if ($this->encuestaModel->actualizarExpediente($data)) {
                echo json_encode(['status' => 'success', 'msg' => '...']);
            } else {
                // CAMBIA ESTA LÍNEA TEMPORALMENTE:
                $errorTecnico = $this->encuestaModel->getError(); 
                echo json_encode(['status' => 'error', 'msg' => 'Error BD: ' . $errorTecnico]);
            }
        }
    }

    public function verificarArchivos($id) {
        $registro = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$registro) { echo json_encode([]); return; }
        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $rutaFisica = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;
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