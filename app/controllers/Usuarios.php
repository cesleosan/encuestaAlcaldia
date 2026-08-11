<?php
class Usuarios extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!$this->puedeVerModulo()) {
            $this->redireccionarFlujoNormal();
            exit;
        }

        $datos = [
            'titulo' => 'Control de accesos',
            'lista' => $this->usuarioModel->getMonitoreoAccesos(),
            'resumen' => $this->usuarioModel->getResumenAccesos()
        ];

        $this->view('usuarios/index', $datos);
    }

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!$this->puedeVerModulo()) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'No autorizado'], 403);
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Método no permitido'], 405);
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Usuario inválido'], 422);
        }

        $estadoSolicitado = strtolower(trim((string)($_POST['estado_acceso'] ?? '')));
        if (!in_array($estadoSolicitado, ['activo', 'pausado', 'inactivo'], true)) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Estado inválido'], 422);
        }

        $usuarioActual = $this->usuarioModel->obtenerUsuarioPorId($id);
        if (!$usuarioActual) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Usuario no encontrado'], 404);
        }

        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            $rol = strtolower(trim((string)($_POST['rol'] ?? $usuarioActual->rol)));
            if ($estadoSolicitado !== 'activo' || $rol !== strtolower((string)($usuarioActual->rol ?? ''))) {
                return $this->jsonResponse(['ok' => false, 'mensaje' => 'No puedes cambiar tu propio rol o estado desde este módulo'], 422);
            }
        }

        $ok = $this->usuarioModel->actualizarUsuarioAdmin($id, [
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'rol' => $_POST['rol'] ?? '',
            'modulo' => $_POST['modulo'] ?? '',
            'estado_acceso' => $estadoSolicitado
        ]);

        return $this->jsonResponse([
            'ok' => (bool)$ok,
            'mensaje' => $ok ? 'Usuario actualizado correctamente' : 'No fue posible actualizar el usuario',
            'estado_acceso' => $estadoSolicitado
        ], $ok ? 200 : 422);
    }

    public function estado() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!$this->puedeVerModulo()) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'No autorizado'], 403);
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Método no permitido'], 405);
        }

        $id = (int)($_POST['id'] ?? 0);
        $estado = strtolower(trim((string)($_POST['estado_acceso'] ?? '')));

        if ($id <= 0 || !in_array($estado, ['activo', 'pausado', 'inactivo'], true)) {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'Datos inválidos'], 422);
        }

        if ($id === (int)($_SESSION['user_id'] ?? 0) && $estado !== 'activo') {
            return $this->jsonResponse(['ok' => false, 'mensaje' => 'No puedes pausar o inactivar tu propio acceso'], 422);
        }

        $ok = $this->usuarioModel->actualizarEstadoAcceso($id, $estado);

        return $this->jsonResponse([
            'ok' => (bool)$ok,
            'mensaje' => $ok ? 'Estado actualizado correctamente' : 'No fue posible cambiar el estado',
            'estado_acceso' => $estado
        ], $ok ? 200 : 422);
    }

    private function puedeVerModulo() {
        if (!isset($_SESSION['user_id'])) return false;

        if (function_exists('tc_puede_ver_accesos_usuarios')) {
            return tc_puede_ver_accesos_usuarios();
        }

        $usuario = $this->usuarioModel->obtenerUsuarioPorId((int)$_SESSION['user_id']);
        return $usuario
            && strtolower($usuario->usuario ?? '') === 'aguillen'
            && $usuario->rol === 'root';
    }

    private function redireccionarFlujoNormal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/Auth');
            return;
        }

        switch ($_SESSION['rol'] ?? '') {
            case 'capturista':
                header('Location: ' . URLROOT . '/Captura/index');
                break;
            case 'encuestador':
                header('Location: ' . URLROOT . '/Encuesta/index');
                break;
            default:
                header('Location: ' . URLROOT . '/Dashboard/index');
                break;
        }
    }

    private function jsonResponse($payload, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
