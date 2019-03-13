<?php
/**
 * Nécessite au moins PHP 7.0.
 */

/**
 * Copie un répertoire.
 * @param $src Chemin du dossier à copier
 * @param $dst Chemin du dossier de destination
 */
function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (($file = readdir($dir)) != false) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/**
 * Compresse une image.
 * @param $file Nom du fichier à compresser
 * @param $path Chemin absolu du fichier
 */
function compressFile($file, $path)
{
    $jpgquality = 70;
    $pngQuality = 70;

    $completePath = $path . $file;

    if (is_file($completePath)) {

        $info = getimagesize($completePath);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($completePath);
            imagejpeg($image, $completePath, $jpgquality);
        } elseif ($info['mime'] == 'image/png') {
            $command = 'pngquant  --quality=' . $pngQuality . ' "' . $completePath . '" --output "' . $completePath . '"';
            exec($command);
        }
    } else {
        die($file . ' is not a file.');
    }
}

/**
 * Explore un dossier et différencie les fichiers des dossiers.
 * @param $dir Nom du dossier
 * @param string $dirPath Chemin du dossier
 */
function exploreDir($dir, string $dirPath)
{
    while (($file = readdir($dir))) {
        if ($file == '.' || $file == '..') {
//            echo 'ignore<br>';
        } else if (is_file(__DIR__ . $dirPath . '/' . $file)) {
            var_dump(__DIR__ . $dirPath . '/' . $file);
            echo $dirPath . '/' . $file . '<br>';
            compressFile($file, __DIR__ . $dirPath . '/');
        } else {
            echo '==>' . $dirPath . '<br>';
            $dir2 = @opendir(__DIR__ . $dirPath . '/' . $file);
            exploreDir($dir2, $dirPath . '/' . $file);
        }
    }
}

$path = __DIR__ . '/src';
$dest = __DIR__ . '/kompressor';

recurse_copy($path, $dest);

$dir = opendir($dest);

exploreDir($dir, '/kompressor');
