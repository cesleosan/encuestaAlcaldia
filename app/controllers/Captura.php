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
    }

    public function index() {
        $this->view('captura/index');
    }

    /**
     * 🔥 ACTUALIZADO: Guarda TODO el expediente oficial (61 campos)
     */
    public function actualizar() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $registro = $this->encuestaModel->getExpedienteCompleto($id);

        $data = [
            'id'                => $id,
            'nombre_productor'  => $_POST['nombre_productor'],
            'paterno'           => $_POST['paterno'],
            'materno'           => $_POST['materno'],
            'curp'              => $_POST['curp'],
            'rfc'               => $_POST['rfc'],
            'tipo_id'           => $_POST['tipo_id'],
            'numero_id'         => $_POST['numero_id'],
            'estado_civil'      => $_POST['estado_civil'],
            'grado_estudios'    => $_POST['grado_estudios'],
            'ocupacion'         => $_POST['ocupacion'],
            'tiene_discapacidad'=> $_POST['tiene_discapacidad'],
            'cual_discapacidad' => $_POST['cual_discapacidad'] ?? 'NA',
            'grupo_etnico'      => $_POST['grupo_etnico'],
            'grupo_etnico_cual' => $_POST['grupo_etnico_cual'] ?? 'NA',
            'calle_numero'      => $_POST['calle_numero'],
            'pueblo_colonia'    => $_POST['pueblo_colonia'],
            'cp'                => $_POST['cp'],
            'tel_particular'    => $_POST['tel_particular'],
            'tel_casa'          => $_POST['tel_casa'],
            'tel_recados'       => $_POST['tel_recados'], // <--- Este es el del HTML
            'linea_ayuda'       => $_POST['linea_ayuda'],
            'siniiga_status'    => $_POST['siniiga_status'],
            'num_total_predios' => $_POST['num_total_predios'],
            'superficie_prod'   => $_POST['superficie_prod'],
            'tipo_documento_prop' => $_POST['tipo_documento_prop'],
            'pueblo_colonia_up' => $_POST['pueblo_colonia_up'],
            'parajes'           => $_POST['parajes'],
            'tenencia_tierra'   => $_POST['tenencia_tierra'],
            'cultivo_principal' => $_POST['cultivo_principal'],
            'num_animales'      => $_POST['num_animales'],
            'fase_proceso'      => $_POST['fase_proceso'],
            'check_solicitud'   => $_POST['check_solicitud'] ?? 0,
            'check_identidad'   => $_POST['check_identidad'] ?? 0,
            'check_domicilio'   => $_POST['check_domicilio'] ?? 0,
            'check_curp_doc'    => $_POST['check_curp_doc'] ?? 0,
            'check_rfc_doc'     => $_POST['check_rfc_doc'] ?? 0,
            'check_manifiesto'  => $_POST['check_manifiesto'] ?? 0,
            'check_propiedad'   => $_POST['check_propiedad'] ?? 0,
            'check_finiquito'   => $_POST['check_finiquito'] ?? 0,
            'check_siniiga_doc' => $_POST['check_siniiga_doc'] ?? 0,
            'json'              => $registro->respuestas_json 
        ];

        if ($this->encuestaModel->actualizarExpediente($data)) {
            echo json_encode(['status' => 'success', 'msg' => 'Expediente actualizado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error en el servidor al guardar']);
        }
    }
}
}