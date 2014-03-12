<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class DataBackup implements BackupInterface {
    private $config;
    private $path;
    private $logger;

    function __construct($path, $config, $logger)
    {
        unset($config['driver']);
        $this->path = $path;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function doBackup()
    {
        foreach ($this->config as $path) {
            if (is_dir($path)) {
                $name = basename($path);
            } else if (is_file($path)) {
                $name = pathinfo($path)['filename'];
            } else {
                $this->logger->logError("Path is not a file or folder: " . $path);
            }
            $tmppath = $this->path . "/archive_$name.tar";

            // Make filename unique:
            $counter = 1;
            while (is_file($tmppath)) {
                $tmppath = $this->path . "/archive_$name$counter.tar.gz";
                $counter++;
            }

            $this->logger->logInfo("Creating archive from '$path'.");
            $arch = new PharData($tmppath);
            if (is_dir($path)) {
                $arch->buildFromDirectory($path);
            } else {
                $arch->addFile($path);
            }
            $arch->compress(Phar::GZ);
            unset($arch);
            unlink($tmppath);
        }
    }
}