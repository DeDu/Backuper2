<?php

/* ENCRYPTION TEST:
$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

$key = openssl_random_pseudo_bytes(32);
//$hex   = bin2hex($bytes);
//echo $hex;
$data = "lkasdjlkJ lkjlkj lkj";
$enc = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $iv);
echo $enc;
echo "\n";
$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $enc, MCRYPT_MODE_CBC, $iv);
echo $dec;
echo "\n";

echo bin2hex($iv);
echo "\n";

exit; */
require_once("vendor/BitcasaClient.php");
require_once("vendor/KLogger.php");

function __autoload($className) {
    $paths = [
        __DIR__ . "/src",
        __DIR__ . "/src/backups",
        __DIR__ . "/src/encryption",
        __DIR__ . "/src/storages",
    ];
    $filename = $className . ".class.php";
    foreach ($paths as $path) {
        $filepath = $path . "/" . $filename;
        if (file_exists($filepath)) {
            require_once($filepath);
        }
    }
}

$config = (include("config.php"));

$backuper = new Backuper($config, new KLogger($config['log_dir'], KLogger::INFO));

$backuper->startBackup();