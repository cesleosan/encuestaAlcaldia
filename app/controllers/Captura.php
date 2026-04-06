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
     * Guarda expediente oficial, convierte a MAYÚSCULAS y gestiona archivos
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            // 1. Obtener el registro para conocer el Folio oficial
            $registro = $this->encuestaModel->getExpedienteCompleto($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'No se encontró el registro']);
                return;
            }

            // 2. LÓGICA DE DIRECTORIOS
            $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
            $rutaBase = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;

            if (!is_dir($rutaBase)) {
                // Creamos con 0775 y forzamos permisos para el usuario nginx
                if (!@mkdir($rutaBase, 0775, true)) {
                    echo json_encode(['status' => 'error', 'msg' => 'Error de permisos: No se pudo crear la carpeta del expediente.']);
                    return;
                }
                chmod($rutaBase, 0775); 
            }

            // 3. PROCESAMIENTO DE ARCHIVOS (Sobrescribe si ya existen)
            $mapeoArchivos = [
                'file_solicitud'   => 'SOLICITUD',
                'file_identidad'   => 'IDENTIDAD',
                'file_domicilio'   => 'DOMICILIO',
                'file_curp_doc'    => 'CURP',
                'file_rfc_doc'     => 'RFC',
                'file_manifiesto'  => 'MANIFIESTO',
                'file_propiedad'   => 'PROPIEDAD',
                'file_finiquito'   => 'FINIQUITO',
                'file_siniiga_doc' => 'SINIIGA'
            ];

            foreach ($mapeoArchivos as $inputName => $nombreDoc) {
                if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$inputName]['tmp_name'];
                    $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
                    
                    // Nombre estandarizado: FOLIO_TIPO.ext
                    $nombreFinal = $folioCarpeta . "_" . $nombreDoc . "." . $ext;
                    $destinoCompleto = $rutaBase . "/" . $nombreFinal;

                    if (move_uploaded_file($tmpName, $destinoCompleto)) {
                        chmod($destinoCompleto, 0664); // Permiso de lectura para Nginx
                    }
                }
            }

            // 4. PREPARACIÓN DE DATOS (Todo a MAYÚSCULAS)
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
                // En el array $data del método actualizar():
                'fase_proceso' => ($registro->fase_proceso == 'EMPADRONADO') ? 'SOLICITUD_INGRESADA' : $_POST['fase_proceso'],
                
                // Cotejo
                'check_solicitud'   => isset($_POST['check_solicitud']) ? 1 : 0,
                'check_identidad'   => isset($_POST['check_identidad']) ? 1 : 0,
                'check_domicilio'   => isset($_POST['check_domicilio']) ? 1 : 0,
                'check_curp_doc'    => isset($_POST['check_curp_doc']) ? 1 : 0,
                'check_rfc_doc'     => isset($_POST['check_rfc_doc']) ? 1 : 0,
                'check_manifiesto'  => isset($_POST['check_manifiesto']) ? 1 : 0,
                'check_propiedad'   => isset($_POST['check_propiedad']) ? 1 : 0,
                'check_finiquito'   => isset($_POST['check_finiquito']) ? 1 : 0,
                'check_siniiga_doc' => isset($_POST['check_siniiga_doc']) ? 1 : 0,
                
                'json'              => $registro->respuestas_json 
            ];

            // 5. EJECUTAR ACTUALIZACIÓN
            if ($this->encuestaModel->actualizarExpediente($data)) {
                echo json_encode(['status' => 'success', 'msg' => '¡Expediente y documentos actualizados!']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al registrar en la base de datos.']);
            }
        }
    }

    /**
     * Escanea archivos para UI/UX
     */
    public function verificarArchivos($id) {
        $registro = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$registro) { echo json_encode([]); return; }

        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $rutaFisica = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;
        
        // CORRECCIÓN CLAVE: Quitamos '/public' de la URL para que Nginx no redirija al index
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