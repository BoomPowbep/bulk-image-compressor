<?php

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while( ($file = readdir($dir)) != false ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function exploreDir($dir) {

}


$jpgquality = 90;
$pngQuality = 70;

$path    = __DIR__ .  '/src';
$dest = __DIR__ . '/kompressor';

recurse_copy($path, $dest);

$dir = opendir($dest);

exploreDir($dir);

die();
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
            exec('pngquant  --quality=' . $pngQuality . ' "' . $currfile . '" --output "' . $dest . '/' . $file . '"');
        }
    }
}
