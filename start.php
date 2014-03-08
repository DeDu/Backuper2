<?php

require_once("vendor/BitcasaClient.php");
require_once("vendor/KLogger.php");

require_once("Backuper.class.php");
require_once("DriverInterface.class.php");
require_once("EncryptInterface.class.php");
require_once("DatabaseBackup.class.php");
require_once("DataBackup.class.php");
require_once("OpenSSLEncypter.class.php");
require_once("StoreInterface.class.php");
require_once("BitcasaStore.class.php");

$config = (include("config.php"));

$backuper = new Backuper($config, new KLogger($config['log_dir'], KLogger::INFO));

$backuper->startBackup();









/*require_once('prepare.php');

$log->logInfo("Backup started.");

// Argumente:
$localpath = "";
if (isset($argv[1])) {
    $localpath = $argv[1];
}
$log->logInfo("Path to create Backupfiles is: " . $localpath);
// Pfad validieren:
if (!is_dir($localpath)) {
    $log->logError("Path to create Backupfiles does not exists!");
    exit("Ordner muss existieren!");
}

$localpath = realpath($localpath);

// Datenbanken sichern:
foreach ($dbdatabases as $db) {
    $log->logInfo("Backup database: " . $db);
    $cmd = "mysqldump -u " . $dbuser . " -p" . $dbpass . " " . $db . " > " . $localpath . "/DB_" . $db . ".sql";
    exec($cmd);
}

// Ordner sichern:
foreach ($folderstobackup as $name => $bufolder) {
    $bufolderold = $bufolder;
    $bufolder = realpath($bufolder);
    if (is_dir($bufolder)) {
        $log->logInfo("Backup directory: " . $bufolder);
        $foldername = dirname($bufolder);
        $cmd = "tar -zcf $localpath/archive_$name.tar.gz -P $bufolder";
        exec($cmd);
    } else {
        $log->logWarn("Path is not a directory: " . $bufolderold);
    }
}

// Encryption
if (ENCRYPT_BACKUP) {
    $log->logInfo('Encrypt everything.');
    $lcfiles = implode(" ", glob($localpath . "/*"));
    // Create Tar with everything:
    $backupfile = "$localpath/backup.tar.gz";
    $backupfileencrypted = "$localpath/backup.tar.gz.enc";
    $cmd = "tar -zcf $backupfile -P $lcfiles";
    exec($cmd);
    // Remove the rest:
    $cmd = "rm $lcfiles";
    exec($cmd);
    // Encrypt:
    $cmd = "openssl smime -encrypt -binary -aes-256-cbc -in $backupfile -out $backupfileencrypted -outform DER " . PATH_PUBLICKEY;
    exec($cmd);
    // Remove not crypted:
    unlink($backupfile);
}

require_once('doUpload.php');

$log->logInfo('Done.');*/