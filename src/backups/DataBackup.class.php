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
                $msg = "Path is not a file or folder. Skipping: " . $path;
                $this->logger->logError($msg);
                continue;
            }
            $tmppath = $this->path . "/archive_$name.tar";

            // Make filename unique:
            $counter = 1;
            while (is_file($tmppath)) {
                $tmppath = $this->path . "/archive_$name$counter.tar";
                $counter++;
            }

            $this->logger->logInfo("Creating archive from '$path'.");
            $arch = new PharData($tmppath);
            try {
                if (is_dir($path)) {
                    $arch->buildFromDirectory($path);
                } else {
                    $arch->addFile($path);
                }
                try {
                    $arch->compress(Phar::GZ);
                    unset($arch);
                    if (file_exists($tmppath)) {
                        // Can not exist when directory $path was empty.
                        unlink($tmppath);
                    }
                } catch (BadMethodCallException $e) {
                    $this->logger->logError("Can't compress. zlib extension is not available, or the bzip2 extension is not enabled in php.");
                }
            } catch (PharException $e) {
                $this->logger->logError($e->getMessage());
            }
        }
    }
}