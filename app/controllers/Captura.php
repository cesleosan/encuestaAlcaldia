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
    }

    public function index() {
        $this->view('captura/index');
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $fase = $_POST['fase_proceso'];
            
            // Recuperamos el registro para no perder datos del JSON original
            $registro = $this->encuestaModel->getEncuestaById($id);
            if (!$registro) {
                echo json_encode(['status' => 'error', 'msg' => 'Registro no encontrado']);
                return;
            }

            $json = json_decode($registro->respuestas_json, true);

            // Aquí podrías agregar lógica para guardar los checks de documentos en el JSON si fuera necesario
            
            $datosUpdate = [
                'id' => $id,
                'fase' => $fase,
                'json' => json_encode($json, JSON_UNESCAPED_UNICODE)
            ];

            if ($this->encuestaModel->actualizarFase($datosUpdate)) {
                echo json_encode(['status' => 'success', 'msg' => 'Expediente actualizado y promovido con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar en base de datos']);
            }
        }
    }
}