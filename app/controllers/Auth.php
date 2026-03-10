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

        // --- 2. VALIDACIÓN BASE DE DATOS (NUEVA LÓGICA DE HASH) ---
        $usuario = trim($_POST['usuario'] ?? '');
        $passwordInput = trim($_POST['password'] ?? '');

        // Paso A: Obtenemos el registro del usuario solo por su nombre
        // Importante: El modelo ahora debe devolver el hash guardado
        $userRow = $this->usuarioModel->obtenerUsuarioPorNombre($usuario);

        // Paso B: Verificamos si el usuario existe y si el hash coincide con lo escrito
        if ($userRow && password_verify($passwordInput, $userRow->password)) {
            
            // ¡ÉXITO! Guardamos variables de sesión
            $_SESSION['user_id'] = $userRow->id;
            $_SESSION['rol'] = $userRow->rol;
            $_SESSION['nombre'] = $userRow->nombre_completo; 

            // Redireccionar según el rol (Root, Supervisor o Encuestador)
            $this->redireccionarRol($userRow->rol);
            exit;

        } else {
            // Falla: O no existe el usuario o la contraseña no coincide con el hash
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