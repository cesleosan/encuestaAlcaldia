<?php

class Captura extends Controller {
    
    private $encuestaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['root', 'supervisor', 'capturista'])) {
            header('Location: ' . URLROOT . '/Auth');
            exit;
        }
        $this->encuestaModel = $this->model('EncuestaModelo');
    }

    public function index() {
        // La vista captura/index.php que armamos anteriormente
        $this->view('captura/index');
    }

    /**
     * Procesa la actualización del expediente desde el modal del capturista
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // 1. Recibir datos del formulario
            $id = $_POST['id'];
            $fase = $_POST['fase_proceso'];
            $curp = strtoupper(trim($_POST['curp']));
            $superficie = floatval($_POST['superficie']);
            
            // Datos para el parsing de nombre
            $nombre_completo = trim($_POST['nombre']);
            $partes = explode(' ', $nombre_completo);
            $materno = (count($partes) > 2) ? array_pop($partes) : '';
            $paterno = (count($partes) > 1) ? array_pop($partes) : '';
            $nombre = implode(' ', $partes);

            // 2. Obtener el registro actual para no perder el JSON original
            $registroActual = $this->encuestaModel->getEncuestaById($id);
            if (!$registroActual) {
                echo json_encode(['status' => 'error', 'msg' => 'Registro no encontrado']);
                return;
            }

            $json = json_decode($registroActual->respuestas_json, true);

            // 3. Sincronización Quirúrgica del JSON
            // Actualizamos los valores dentro del JSON para que los reportes detallados sigan siendo correctos
            $this->actualizarValorJSON($json, 'curp', $curp);
            $this->actualizarValorJSON($json, 'nombre_productor', $nombre_completo);
            $this->actualizarValorJSON($json, 'tel_particular', $_POST['telefono']);
            $this->actualizarValorJSON($json, 'calle_numero', $_POST['calle']);
            $this->actualizarValorJSON($json, 'cp', $_POST['cp']);
            $this->actualizarValorJSON($json, 'superficie_prod', $superficie);

            // 4. Preparar el paquete de actualización para la BD
            $dataUpdate = [
                'id' => $id,
                'nombre' => $nombre,
                'paterno' => $paterno,
                'materno' => $materno,
                'curp' => $curp,
                'superficie' => $superficie,
                'fase' => $fase,
                'colonia' => $_POST['colonia'],
                'json' => json_encode($json, JSON_UNESCAPED_UNICODE)
            ];

            // 5. Ejecutar la actualización en el Modelo
            if ($this->encuestaModel->actualizarExpediente($dataUpdate)) {
                echo json_encode(['status' => 'success', 'msg' => 'Expediente actualizado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al guardar en la base de datos']);
            }
        }
    }

    /**
     * Helper para buscar y reemplazar un valor dentro de la estructura compleja del JSON
     */
    private function actualizarValorJSON(&$json, $nombreCampo, $nuevoValor) {
        foreach ($json as $seccion => &$campos) {
            if (is_array($campos)) {
                foreach ($campos as &$item) {
                    if (isset($item['name']) && ($item['name'] === $nombreCampo || $item['name'] === $nombreCampo . '[]')) {
                        $item['value'] = $nuevoValor;
                        return; // Valor encontrado y actualizado
                    }
                }
            }
        }
    }
}