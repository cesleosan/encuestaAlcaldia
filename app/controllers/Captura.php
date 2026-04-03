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
            // 1. Validar existencia del ID
            $id = $_POST['id'] ?? null;
            if (!$id) {
                echo json_encode(['status' => 'error', 'msg' => 'ID de expediente no válido']);
                return;
            }

            // 2. Recuperar el registro actual para preservar el JSON original de la encuesta
            $registro = $this->encuestaModel->getExpedienteCompleto($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'Registro no encontrado en el sistema']);
                return;
            }

            // 3. Mapeo Quirúrgico de datos (POST -> ARRAY PARA MODELO)
            // Usamos operadores ternarios para asegurar que no enviemos nulos fatales
            $data = [
                'id'                => $id,
                // Identidad
                'nombre_productor'  => trim($_POST['nombre_productor'] ?? ''),
                'paterno'           => trim($_POST['paterno'] ?? ''),
                'materno'           => trim($_POST['materno'] ?? ''),
                'curp'              => trim($_POST['curp'] ?? ''),
                'rfc'               => trim($_POST['rfc'] ?? ''),
                'tipo_id'           => $_POST['tipo_id'] ?? null,
                'numero_id'         => $_POST['numero_id'] ?? null,
                // Perfil
                'estado_civil'      => $_POST['estado_civil'] ?? null,
                'grado_estudios'    => $_POST['grado_estudios'] ?? null, // Columna 'escolaridad'
                'ocupacion'         => trim($_POST['ocupacion'] ?? ''),
                'tiene_discapacidad'=> $_POST['tiene_discapacidad'] ?? 'NO',
                'cual_discapacidad' => trim($_POST['cual_discapacidad'] ?? 'NA'),
                'grupo_etnico'      => $_POST['grupo_etnico'] ?? 'NO',
                'grupo_etnico_cual' => trim($_POST['grupo_etnico_cual'] ?? 'NA'),
                // Ubicación y Contacto
                'calle_numero'      => trim($_POST['calle_numero'] ?? ''),
                'pueblo_colonia'    => trim($_POST['pueblo_colonia'] ?? ''),
                'cp'                => trim($_POST['cp'] ?? ''),
                'tel_particular'    => trim($_POST['tel_particular'] ?? ''),
                'tel_casa'          => trim($_POST['tel_casa'] ?? ''),
                'tel_recados'       => trim($_POST['tel_recados'] ?? ''),
                // Producción Técnica
                'linea_ayuda'       => $_POST['linea_ayuda'] ?? null,
                'siniiga_status'    => $_POST['siniiga_status'] ?? 'NO',
                'num_total_predios' => $_POST['num_total_predios'] ?? 1,
                'superficie_prod'   => $_POST['superficie_prod'] ?? 0,
                'tipo_documento_prop' => $_POST['tipo_documento_prop'] ?? null,
                'pueblo_colonia_up' => trim($_POST['pueblo_colonia_up'] ?? ''),
                'parajes'           => trim($_POST['parajes'] ?? 'NA'),
                'tenencia_tierra'   => $_POST['tenencia_tierra'] ?? 'NA',
                'cultivo_principal' => trim($_POST['cultivo_principal'] ?? ''),
                'num_animales'      => $_POST['num_animales'] ?? 0,
                // Checklist de Documentación (Booleanos 1/0)
                'check_solicitud'   => isset($_POST['check_solicitud']) ? 1 : 0,
                'check_identidad'   => isset($_POST['check_identidad']) ? 1 : 0,
                'check_domicilio'   => isset($_POST['check_domicilio']) ? 1 : 0,
                'check_curp_doc'    => isset($_POST['check_curp_doc']) ? 1 : 0,
                'check_rfc_doc'     => isset($_POST['check_rfc_doc']) ? 1 : 0,
                'check_manifiesto'  => isset($_POST['check_manifiesto']) ? 1 : 0,
                'check_propiedad'   => isset($_POST['check_propiedad']) ? 1 : 0,
                'check_finiquito'   => isset($_POST['check_finiquito']) ? 1 : 0,
                'check_siniiga_doc' => isset($_POST['check_siniiga_doc']) ? 1 : 0,
                // Control
                'fase_proceso'      => $_POST['fase_proceso'] ?? 'EMPADRONADO',
                'observaciones_capturista' => trim($_POST['observaciones_capturista'] ?? ''),
                'json'              => $registro->respuestas_json // Mantenemos el JSON intacto
            ];

            // 4. Ejecutar la actualización masiva en el Modelo
            if ($this->encuestaModel->actualizarExpediente($data)) {
                echo json_encode([
                    'status' => 'success', 
                    'msg' => 'Expediente de ' . $data['nombre_productor'] . ' guardado y actualizado con éxito.'
                ]);
            } else {
                // Capturamos el error técnico si existe en el modelo
                $errorMsg = method_exists($this->encuestaModel, 'getError') ? $this->encuestaModel->getError() : 'Error en la consulta SQL';
                echo json_encode([
                    'status' => 'error', 
                    'msg' => 'No se pudo guardar: ' . $errorMsg
                ]);
            }
        } else {
            // Bloqueo de acceso directo por URL
            header('Location: ' . URLROOT . '/Captura');
        }
    }
}