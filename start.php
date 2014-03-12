<?php



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

switch ($argv[1]) {
    case 'decrypt':
        $backuper->startDecrypt($argv[3], $argv[2]);
        break;
    default:
        $backuper->startBackup();
        break;
}

