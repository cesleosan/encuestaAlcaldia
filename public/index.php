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

// 3. Cargar el núcleo (Core y Controller)
require_once APPPATH . '/core/Controller.php';
require_once APPPATH . '/core/Core.php';

// 4. Iniciar el framework
$init = new Core();
