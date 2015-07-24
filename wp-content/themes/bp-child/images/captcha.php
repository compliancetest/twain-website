<?php 
$im=imagecreatetruecolor(70, 30);
$bg=imagecolorallocate($im, 250, 250, 250);
imagefill($im, 0, 0, $bg);

$text_color=imagecolorallocate($im, 115, 120, 120);
$rand = rand(10000, 99999);
session_start();
$_SESSION['captcha'] = $rand;
imagestring($im, 5, 13, 7, $rand, $text_color);
header('Content-type:image/jpeg');
imagejpeg($im); 
?>
