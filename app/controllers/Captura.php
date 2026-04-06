<?php

class Captura extends Controller {
    private $encuestaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
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

public function actualizarExpediente($data) {
    // Preparamos la consulta con los nombres EXACTOS de tu MariaDB
    $this->db->query("UPDATE encuestas SET 
        curp = :curp,
        rfc = :rfc,
        nombre = :nombre,
        apellido_paterno = :apellido_paterno,
        apellido_materno = :apellido_materno,
        tipo_id = :tipo_id,
        numero_id = :numero_id,
        estado_civil = :estado_civil,
        escolaridad = :escolaridad,
        ocupacion = :ocupacion,
        tiene_discapacidad = :tiene_discapacidad,
        cual_discapacidad = :cual_discapacidad,
        grupo_etnico = :grupo_etnico,
        grupo_etnico_cual = :grupo_etnico_cual,
        calle = :calle,
        colonia_nombre = :colonia_nombre,
        codigo_postal = :codigo_postal,
        tel_particular = :tel_particular,
        tel_casa = :tel_casa,
        tel_familiar = :tel_familiar,
        linea_ayuda = :linea_ayuda,
        registro_siniiga = :registro_siniiga,
        num_total_predios = :num_total_predios,
        superficie_total = :superficie_total,
        tipo_documento_propiedad = :tipo_documento_propiedad,
        pueblo_colonia_up = :pueblo_colonia_up,
        parajes = :parajes,
        tenencia_tierra = :tenencia_tierra,
        especie_cultivo_principal = :especie_cultivo_principal,
        numero_cabezas_colmenas = :numero_cabezas_colmenas,
        fase_proceso = :fase_proceso,
        check_solicitud = :check_solicitud,
        check_identidad = :check_identidad,
        check_domicilio = :check_domicilio,
        check_curp_doc = :check_curp_doc,
        check_rfc_doc = :check_rfc_doc,
        check_manifiesto = :check_manifiesto,
        check_propiedad = :check_propiedad,
        check_finiquito = :check_finiquito,
        check_siniiga_doc = :check_siniiga_doc,
        respuestas_json = :respuestas_json
    WHERE id = :id");

    // Vinculamos los datos usando las llaves que configuramos en el Controlador
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':curp', $data['curp']);
    $this->db->bind(':rfc', $data['rfc']);
    $this->db->bind(':nombre', $data['nombre']);
    $this->db->bind(':apellido_paterno', $data['apellido_paterno']);
    $this->db->bind(':apellido_materno', $data['apellido_materno']);
    $this->db->bind(':tipo_id', $data['tipo_id']);
    $this->db->bind(':numero_id', $data['numero_id']);
    $this->db->bind(':estado_civil', $data['estado_civil']);
    $this->db->bind(':escolaridad', $data['escolaridad']);
    $this->db->bind(':ocupacion', $data['ocupacion']);
    $this->db->bind(':tiene_discapacidad', $data['tiene_discapacidad']);
    $this->db->bind(':cual_discapacidad', $data['cual_discapacidad']);
    $this->db->bind(':grupo_etnico', $data['grupo_etnico']);
    $this->db->bind(':grupo_etnico_cual', $data['grupo_etnico_cual']);
    $this->db->bind(':calle', $data['calle']);
    $this->db->bind(':colonia_nombre', $data['colonia_nombre']);
    $this->db->bind(':codigo_postal', $data['codigo_postal']);
    $this->db->bind(':tel_particular', $data['tel_particular']);
    $this->db->bind(':tel_casa', $data['tel_casa']);
    $this->db->bind(':tel_familiar', $data['tel_familiar']);
    $this->db->bind(':linea_ayuda', $data['linea_ayuda']);
    $this->db->bind(':registro_siniiga', $data['registro_siniiga']);
    $this->db->bind(':num_total_predios', $data['num_total_predios']);
    $this->db->bind(':superficie_total', $data['superficie_total']);
    $this->db->bind(':tipo_documento_propiedad', $data['tipo_documento_propiedad']);
    $this->db->bind(':pueblo_colonia_up', $data['pueblo_colonia_up']);
    $this->db->bind(':parajes', $data['parajes']);
    $this->db->bind(':tenencia_tierra', $data['tenencia_tierra']);
    $this->db->bind(':especie_cultivo_principal', $data['especie_cultivo_principal']);
    $this->db->bind(':numero_cabezas_colmenas', $data['numero_cabezas_colmenas']);
    $this->db->bind(':fase_proceso', $data['fase_proceso']);
    
    // Bits de Cotejo
    $this->db->bind(':check_solicitud', $data['check_solicitud']);
    $this->db->bind(':check_identidad', $data['check_identidad']);
    $this->db->bind(':check_domicilio', $data['check_domicilio']);
    $this->db->bind(':check_curp_doc', $data['check_curp_doc']);
    $this->db->bind(':check_rfc_doc', $data['check_rfc_doc']);
    $this->db->bind(':check_manifiesto', $data['check_manifiesto']);
    $this->db->bind(':check_propiedad', $data['check_propiedad']);
    $this->db->bind(':check_finiquito', $data['check_finiquito']);
    $this->db->bind(':check_siniiga_doc', $data['check_siniiga_doc']);
    
    $this->db->bind(':respuestas_json', $data['json']);

    return $this->db->execute();
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