<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class DataBackup implements DriverInterface{
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
            }
            $tmppath = $this->path . "/archive_$name.tar.gz";

            // Make filename unique:
            $counter = 1;
            while (is_file($tmppath)) {
                $tmppath = $this->path . "/archive_$name$counter.tar.gz";
                $counter++;
            }

            $cmd = "tar -zcf " . $tmppath . " -P $path";
            $this->logger->logInfo("Creating archive from '$path' with command: $cmd");
            exec($cmd);
        }
    }
}