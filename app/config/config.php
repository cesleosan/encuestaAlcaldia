<?php
// Configuración de acceso a la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Tu usuario de MariaDB (usualmente 'root')
define('DB_PASS', 'root');          // Tu contraseña (en XAMPP suele ser vacía, en MAMP es 'root')
define('DB_NAME', 'censo_tlalpan'); // El nombre exacto de la base que creamos
// Raíz real del proyecto (encuestaAlcaldia)
define('ROOTPATH', dirname(__DIR__, 2));

// Carpeta app
define('APPPATH', ROOTPATH . '/app');

// Carpeta public
define('PUBLICPATH', ROOTPATH . '/public');

// Compatibilidad con lo que ya usabas
define('APPROOT', APPPATH);

// URL
define('URLROOT', 'http://localhost:8000');

// Nombre del sitio
define('SITENAME', 'Encuestas Tlalpan');
