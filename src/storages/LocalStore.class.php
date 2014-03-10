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
    }


    public function store()
    {
        $localPath = $this->config['localPath'];
        $datestring = date("YmdHi");
        $localPath = $localPath . "/Backup_" . $datestring;

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
    }
}