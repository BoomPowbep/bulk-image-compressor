<?php

$jpgquality = 90;
$pngQuality = 70;

//$path    = __DIR__ .  '/src';
//$dest = __DIR__ . '/output';
$path    = './src';
$dest = './output';
$files = scandir($path);
var_dump($files);

// Boucle d'exploration
foreach ($files as $file) {
    $currfile = $path . '/' . $file;

    if(is_file($currfile)) {
        $info = getimagesize($path . '/' . $file);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($currfile);
            imagejpeg($image, $dest . '/' . $file, $jpgquality);
        }
        elseif ($info['mime'] == 'image/png') {
//            $currfile = str_replace(' ', '&amp;', $currfile);
            exec('pngquant  --quality=' . $pngQuality . ' ' . $currfile . ' --output ' . $dest . '/' . $file);
        }
    }
}
