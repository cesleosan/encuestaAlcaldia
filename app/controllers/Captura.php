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

        // DEFINICIÓN DE PUBROOT SI NO EXISTE (Corrección Quirúrgica para el error)
        if (!defined('PUBROOT')) {
            // Asumiendo que APPROOT está en /var/www/html/encuestaAlcaldia/app
            // PUBROOT debe ser /var/www/html/encuestaAlcaldia/public
            define('PUBROOT', dirname(APPROOT) . '/public');
        }
    }

    public function index() {
        $this->view('captura/index');
    }

    /**
     * ACTUALIZADO: Guarda expediente oficial y gestiona carga de archivos por Folio
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            // 1. Obtener el registro actual para conocer el Folio oficial
            $registro = $this->encuestaModel->getExpedienteCompleto($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'No se encontró el registro en la base de datos']);
                return;
            }

            // 2. LÓGICA DE DIRECTORIOS: Carpeta única por Folio
            // Limpiamos el folio de caracteres que Linux/Apache no aceptan en nombres de carpeta
            $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
            $rutaBase = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;

            // Creamos la ruta física si no existe
            if (!is_dir($rutaBase)) {
                // La @ silencia el warning y el mkdir con true crea la ruta completa
                if (!@mkdir($rutaBase, 0775, true)) {
                    echo json_encode(['status' => 'error', 'msg' => 'Error de escritura en el servidor.']);
                    return;
                }
            }

            // 3. PROCESAMIENTO DE ARCHIVOS
            // Mapeo de name="input" del HTML a prefijo de nombre de archivo oficial
            $mapeoArchivos = [
                'file_solicitud'  => 'SOLICITUD',
                'file_identidad'  => 'IDENTIDAD',
                'file_domicilio'  => 'DOMICILIO',
                'file_curp_doc'   => 'CURP',
                'file_rfc_doc'    => 'RFC',
                'file_manifiesto' => 'MANIFIESTO',
                'file_propiedad'  => 'PROPIEDAD',
                'file_finiquito'  => 'FINIQUITO',
                'file_siniiga_doc'=> 'SINIIGA'
            ];

            foreach ($mapeoArchivos as $inputName => $nombreDoc) {
                if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                    
                    $tmpName = $_FILES[$inputName]['tmp_name'];
                    $originalName = $_FILES[$inputName]['name'];
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    
                    // Nombre estandarizado: TLP-26-XXXX_IDENTIDAD.pdf
                    $nombreFinal = $folioCarpeta . "_" . $nombreDoc . "." . $ext;
                    $destinoCompleto = $rutaBase . "/" . $nombreFinal;

                    // Mover archivo del temporal a la carpeta del folio
                    move_uploaded_file($tmpName, $destinoCompleto);
                }
            }

            // 4. PREPARACIÓN DE DATOS PARA BASE DE DATOS
            // Recibimos todo y aseguramos valores por defecto para evitar fallos en el Modelo
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
                'fase_proceso'      => $_POST['fase_proceso'] ?? 'EMPADRONADO',
                
                // Checkboxes de cotejo (llegan como 1 o se fuerzan a 0)
                'check_solicitud'   => isset($_POST['check_solicitud']) ? 1 : 0,
                'check_identidad'   => isset($_POST['check_identidad']) ? 1 : 0,
                'check_domicilio'   => isset($_POST['check_domicilio']) ? 1 : 0,
                'check_curp_doc'    => isset($_POST['check_curp_doc']) ? 1 : 0,
                'check_rfc_doc'     => isset($_POST['check_rfc_doc']) ? 1 : 0,
                'check_manifiesto'  => isset($_POST['check_manifiesto']) ? 1 : 0,
                'check_propiedad'   => isset($_POST['check_propiedad']) ? 1 : 0,
                'check_finiquito'   => isset($_POST['check_finiquito']) ? 1 : 0,
                'check_siniiga_doc' => isset($_POST['check_siniiga_doc']) ? 1 : 0,
                
                'json'              => $registro->respuestas_json // Preservamos la encuesta original
            ];

            // 5. EJECUTAR ACTUALIZACIÓN EN BASE DE DATOS
            if ($this->encuestaModel->actualizarExpediente($data)) {
                echo json_encode([
                    'status' => 'success', 
                    'msg' => '¡Expediente actualizado y documentos cargados con éxito!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'msg' => 'El expediente se subió pero hubo un error al registrar en la base de datos.'
                ]);
            }
        }
    }

        /**
     * Escanea la carpeta del folio y devuelve la lista de archivos existentes
     */
    public function verificarArchivos($id) {
        $registro = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$registro) { echo json_encode([]); return; }

        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $rutaFisica = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;
        $urlBase = URLROOT . '/public/uploads/expedientes/' . $folioCarpeta;

        $archivosEncontrados = [];

        if (is_dir($rutaFisica)) {
            $archivos = scandir($rutaFisica);
            foreach ($archivos as $archivo) {
                if ($archivo !== '.' && $archivo !== '..') {
                    // Identificamos el tipo basado en el nombre (ej. TLP-26_IDENTIDAD.pdf)
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