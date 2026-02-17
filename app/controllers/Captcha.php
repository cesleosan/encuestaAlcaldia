<?php
class Captcha extends Controller {

    public function index() {

        // Limpiar cualquier salida previa
        while (ob_get_level()) {
            ob_end_clean();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generar código aleatorio
        $codigo = substr(md5(uniqid(rand(), true)), 0, 5);
        $_SESSION['captcha_real'] = strtoupper($codigo);

        // Dimensiones del lienzo
        $ancho = 130;
        $alto  = 50;

        $imagen = imagecreatetruecolor($ancho, $alto);

        // Colores
        $fondo  = imagecolorallocate($imagen, 245, 245, 245);
        $guinda = imagecolorallocate($imagen, 119, 51, 87);   // #773357
        $gris   = imagecolorallocate($imagen, 180, 180, 180);

        imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $fondo);

        // Ruido visual
        for ($i = 0; $i < 10; $i++) {
            imageline(
                $imagen,
                rand(0, $ancho),
                rand(0, $alto),
                rand(0, $ancho),
                rand(0, $alto),
                $gris
            );
        }

        // Texto centrado (compatible PHP 8.2+)
        $font       = 5;
        $fontWidth  = imagefontwidth($font);
        $fontHeight = imagefontheight($font);
        $textWidth  = $fontWidth * strlen($codigo);

        $x = intdiv($ancho - $textWidth, 2);
        $y = intdiv($alto - $fontHeight, 2);

        imagestring($imagen, $font, $x, $y, strtoupper($codigo), $guinda);

        // Headers correctos antes de imprimir la imagen
        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        imagepng($imagen);
        imagedestroy($imagen);
        exit;
    }
}
