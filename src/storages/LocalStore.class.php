<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class LocalStore implements StoreInterface {
    private $config;
    private $path;
    private $logger;

    function __construct($path, $config, $logger)
    {
        $this->path = $path;
        $this->config = $config;
        $this->logger = $logger;

        // defaults:
        $defaultConfig = ['dir_prefix' => 'Backup_'];
        $this->config = array_merge($defaultConfig, $this->config);
    }


    public function store()
    {
        $this->logger->logInfo("Start move Backup local.");
        $localPath = $this->config['localPath'];
        $datestring = date("YmdHi");
        $localPath = $localPath . "/" . $this->config['dir_prefix'] . $datestring;

        // use unique directory:
        $tmpLocalPath = $localPath;
        $counter = 1;
        while (is_dir($tmpLocalPath) OR is_file($tmpLocalPath)) {
            $tmpLocalPath = $localPath . '_' . $counter;
            $counter++;
        }
        $localPath = $tmpLocalPath;

        // create this dir:
        if (! mkdir($localPath, 0777, true) ) {
            $this->logger->logError("Could not create following directory to store backup: $localPath");
        }

        // Move files:
        foreach (glob($this->path . "/*") as $file) {
            if (is_file($file) OR is_dir($file)) {
                $newPath = $localPath . "/" . basename($file);
                // Copy:
                if (copy($file, $newPath)) {
                    $this->logger->logInfo("Copied file/directory to: $newPath");
                } else {
                    $this->logger->logError("Could not copy to: $newPath");
                }
            }
        }

        $this->logger->logInfo("Done move backup local.");
    }
}