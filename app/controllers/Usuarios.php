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

    private function puedeVerModulo() {
        if (!isset($_SESSION['user_id'])) return false;

        $usuarioSesion = $_SESSION['usuario'] ?? '';
        $rolSesion = $_SESSION['rol'] ?? '';
        if ($usuarioSesion === 'aGuillen' && $rolSesion === 'root') return true;

        $usuario = $this->usuarioModel->obtenerUsuarioPorId((int)$_SESSION['user_id']);
        return $usuario && $usuario->usuario === 'aGuillen' && $usuario->rol === 'root';
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
}
