<?php
header("Content-Type: image/png");
$im = imagecreatetruecolor(100, 100);
$red = imagecolorallocate($im, 255, 0, 0);
imagefilledrectangle($im, 0, 0, 100, 100, $red);
imagepng($im);
imagedestroy($im);
