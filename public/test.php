<?php
// Habilitar reporte de errores visual
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si GD está activo
if (!extension_loaded('gd')) {
    die("ERROR FATAL: La librería GD no está activa en tu PHP. Revisa tu php.ini");
}

// Intentar crear una imagen simple
$im = imagecreate(100, 100);
$fondo = imagecolorallocate($im, 255, 0, 0); // Rojo

header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);
?>