<?php
/**
 * Nécessite au moins PHP 7.1.
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
    $pngQuality = 80;

    $completePath = $path . $file;

    if (is_file($completePath)) {
        $info = getimagesize($completePath);
        if ($info['mime'] == 'image/jpeg') {
            $image = new Imagick();
            $image->readImage($completePath);
            $image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(80);
            $image->writeImage();
        } elseif ($info['mime'] == 'image/png') {
            if(file_exists($completePath)) { // Si le fichier est déjà là on le supprime car pngquant ne peut pas écrire par-dessus un fichier
                unlink($completePath);
            }
            $completePathSrc = str_replace('kompressor', 'src', $completePath);
            $command = 'pngquant --quality=' . $pngQuality . ' "' . $completePathSrc . '" --output "' . $completePath . '"';
            $command = str_replace('/', '\\', $command);
            exec($command);
        }
    } else {
        die($file . ' is not a file.');
    }
}

/**
 * Explore un dossier et différencie les fichiers des dossiers.
 * @param $curDir Nom du dossier courant
 * @param string $dirPath Chemin du dossier
 */
function exploreDir($curDir, string $dirPath)
{
    while (($file = readdir($curDir))) {
        if ($file == '.' || $file == '..') {
//            echo 'ignore<br>';
        } else if (is_file(__DIR__ . $dirPath . '/' . $file)) { // Est un fichier
            echo '<p style="color: green;">' . $dirPath . '/' . $file . '</p>';
            compressFile($file, __DIR__ . $dirPath . '/');
        } else { // Est un dossier
            $nextDir = @opendir(__DIR__ . $dirPath . '/' . $file);
            exploreDir($nextDir, $dirPath . '/' . $file);
        }
    }
}

$srcFolder = '/src';
$destFolder = '/kompressor';
$pathDir = __DIR__ . $srcFolder;
$destDir = __DIR__ . $destFolder;

recurse_copy($pathDir, $destDir); // Copie de tous les fichiers et dossiers dans le dossier de destination

$firstDir = opendir($destDir);
exploreDir($firstDir, $destFolder);
