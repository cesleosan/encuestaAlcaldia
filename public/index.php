<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// 1. Cargar la configuración primero
require_once __DIR__ . '/../app/config/config.php';

// 2. Cargar la Base de Datos (Indispensable para los modelos)
// Usamos APPPATH que ya definiste en tu config
require_once APPPATH . '/libraries/Database.php';
require_once APPPATH . '/helpers/auth_helper.php';

if (!empty($_SESSION['user_id']) && function_exists('tc_refrescar_sesion_usuario')) {
    tc_refrescar_sesion_usuario();
}

if (!empty($_SESSION['user_id'])) {
    try {
        $activityDb = new Database();
        $activityDb->query("SHOW TABLES LIKE 'usuario_sesiones'");
        if ($activityDb->single()) {
            $activityDb->query("
                UPDATE usuario_sesiones
                SET ultima_actividad = NOW()
                WHERE usuario_id = :usuario_id
                  AND session_id = :session_id
                  AND cierre IS NULL
                  AND estado = 'activa'
            ");
            $activityDb->bind(':usuario_id', (int)$_SESSION['user_id']);
            $activityDb->bind(':session_id', session_id());
            $activityDb->execute();
        }
    } catch (Exception $e) {
        // El monitoreo de sesiones no debe bloquear el sistema.
    }
}

// 3. Cargar el núcleo (Core y Controller)
require_once APPPATH . '/core/Controller.php';
require_once APPPATH . '/core/Core.php';

// 4. Iniciar el framework
$init = new Core();
