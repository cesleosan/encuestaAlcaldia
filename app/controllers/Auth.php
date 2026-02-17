<?php
class Auth extends Controller {
    
    private $usuarioModel;

    public function __construct() {
        // Esto busca app/models/Usuario.php
        $this->usuarioModel = $this->model('Usuario'); 
    }

    public function index() {
        // Verificar si ya tiene sesión activa para no pedir login de nuevo
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) {
            $this->redireccionarRol($_SESSION['rol']);
            return;
        }

        $data = ['error' => ''];
        $this->view('auth/login', $data);
    }

    public function validar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // --- 1. VALIDACIÓN CAPTCHA ---
        $captcha_user = strtoupper($_POST['captcha_input'] ?? '');
        $captcha_real = strtoupper($_SESSION['captcha_real'] ?? '');

        if ($captcha_user !== $captcha_real) {
            $data = ['error' => 'El código de seguridad es incorrecto'];
            $this->view('auth/login', $data);
            return;
        }

        // --- 2. VALIDACIÓN BASE DE DATOS ---
        $usuario = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Llamamos al método login del modelo Usuario.php
        $userLogged = $this->usuarioModel->login($usuario, $password);

        if ($userLogged) {
            // ¡ÉXITO! Guardamos variables de sesión
            $_SESSION['user_id'] = $userLogged->id;
            $_SESSION['rol'] = $userLogged->rol;
            // Nota: En la BD le pusimos 'nombre_completo', en sesión usamos 'nombre'
            $_SESSION['nombre'] = $userLogged->nombre_completo; 

            // Redireccionar según el rol
            $this->redireccionarRol($userLogged->rol);
            exit;

        } else {
            // Falla
            $data = ['error' => 'Usuario o contraseña incorrectos'];
            $this->view('auth/login', $data);
        }
    }

    // Helper para no repetir el switch
    private function redireccionarRol($rol) {
        switch ($rol) {
            case 'root':
            case 'supervisor':
            case 'consulta':
                // Los jefes van al Dashboard
                header('Location: ' . URLROOT . '/Dashboard/index');
                break;

            case 'encuestador':
            default:
                // Los técnicos van a la Encuesta
                header('Location: ' . URLROOT . '/Encuesta/index');
                break;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: ' . URLROOT . '/Auth');
    }
}