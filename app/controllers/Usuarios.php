<?php
class Usuarios extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!$this->puedeVerModulo()) {
            header('Location: ' . URLROOT . '/Dashboard');
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
        if ($usuarioSesion === 'aGuillen') return true;

        $usuario = $this->usuarioModel->obtenerUsuarioPorId((int)$_SESSION['user_id']);
        return $usuario && $usuario->usuario === 'aGuillen';
    }
}
