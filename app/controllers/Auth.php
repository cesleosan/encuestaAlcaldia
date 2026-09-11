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

        $motivo = $_GET['motivo'] ?? '';
        $mensajesMotivo = [
            'sin_sesion_dashboard' => 'Diagnóstico: el acceso llegó al Dashboard sin sesión activa. Intenta iniciar sesión de nuevo; si se repite, revisa cookies/sesión del servidor.',
            'ajax_401_dashboard' => 'Diagnóstico: el Dashboard abrió, pero la carga de datos no recibió la sesión activa. Revisa cookies/sesión del servidor.'
        ];
        $error = $mensajesMotivo[$motivo] ?? '';

        $data = ['error' => $error];
        $this->view('auth/login', $data);
    }

    public function validar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // --- 1. LECTURA CAPTCHA ---
        $captcha_user = $this->normalizarCaptcha($_POST['captcha_input'] ?? '');
        $captcha_real = $this->normalizarCaptcha($_SESSION['captcha_real'] ?? '');
        $captchaValido = ($captcha_real !== '' && $captcha_user === $captcha_real);

        // --- 2. VALIDACIÓN BASE DE DATOS (NUEVA LÓGICA DE HASH) ---
        $usuario = trim($_POST['usuario'] ?? '');
        $passwordInput = trim($_POST['password'] ?? '');

        // Paso A: Obtenemos el registro del usuario solo por su nombre
        // Importante: El modelo ahora debe devolver el hash guardado
        $userRow = $this->usuarioModel->obtenerUsuarioPorNombre($usuario);

        // Paso B: Verificamos si el usuario existe y si el hash coincide con lo escrito
        if ($userRow && password_verify($passwordInput, $userRow->password)) {
            if (!$captchaValido) {
                error_log('[Auth] Captcha no coincidio, se permite acceso por credenciales validas para usuario: ' . $usuario);
            }
            
            // ¡ÉXITO! Guardamos variables de sesión
            $_SESSION['user_id'] = $userRow->id;
            $_SESSION['usuario'] = $userRow->usuario;
            $_SESSION['rol'] = $userRow->rol;
            $_SESSION['nombre'] = $userRow->nombre_completo; 
            $_SESSION['modulo'] = $userRow->modulo ?? 'TIERRA';

            $this->usuarioModel->registrarInicioSesion(
                $userRow->id,
                session_id(),
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );

            // Redireccionar según el rol (Root, Supervisor o Encuestador)
            session_write_close();
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

            case 'capturista':
                header('Location: ' . URLROOT . '/Captura/index');
                break;

            case 'encuestador':
            default:
                // Los técnicos van a la Encuesta
                header('Location: ' . URLROOT . '/Encuesta/index');
                break;
        }
    }

    private function normalizarCaptcha($valor) {
        $valor = strtoupper(trim((string)$valor));
        return preg_replace('/[^A-Z0-9]/', '', $valor);
    }

    public function diagnosticoSesion() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['diagnostico_probe'] = (int)($_SESSION['diagnostico_probe'] ?? 0) + 1;

        $savePath = session_save_path();
        if ($savePath === '') {
            $savePath = sys_get_temp_dir();
        }

        $usuarios = [];
        try {
            $db = new Database();
            $db->query("SHOW COLUMNS FROM usuarios LIKE 'estado_acceso'");
            $tieneEstadoAcceso = (bool)$db->single();

            $estadoSelect = $tieneEstadoAcceso
                ? "COALESCE(NULLIF(estado_acceso, ''), IF(activo = 1, 'activo', 'inactivo'))"
                : "IF(activo = 1, 'activo', 'inactivo')";

            $db->query("
                SELECT id, usuario, rol, modulo, activo, {$estadoSelect} AS estado_acceso, ultimo_acceso
                FROM usuarios
                WHERE usuario IN ('fernando.romero', 'edgar.zavala', 'aGuillen', 'aguillen')
                   OR LOWER(usuario) = 'aguillen'
                ORDER BY usuario
            ");
            $usuarios = $db->resultSet();
        } catch (Exception $e) {
            $usuarios = ['error' => $e->getMessage()];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'ok',
            'session' => [
                'name' => session_name(),
                'id' => session_id(),
                'cookie_recibida' => isset($_COOKIE[session_name()]),
                'probe_counter' => $_SESSION['diagnostico_probe'],
                'keys' => array_keys($_SESSION),
                'user_id' => $_SESSION['user_id'] ?? null,
                'usuario' => $_SESSION['usuario'] ?? null,
                'rol' => $_SESSION['rol'] ?? null,
                'modulo' => $_SESSION['modulo'] ?? null
            ],
            'server' => [
                'https' => $_SERVER['HTTPS'] ?? null,
                'forwarded_proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
                'host' => $_SERVER['HTTP_HOST'] ?? null,
                'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
                'save_path' => $savePath,
                'save_path_writable' => is_writable($savePath)
            ],
            'usuarios_clave' => $usuarios
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) {
            $this->usuarioModel->cerrarSesion((int)$_SESSION['user_id'], session_id());
        }
        session_destroy();
        header('Location: ' . URLROOT . '/Auth');
    }
}
