<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class SFTPStore implements StoreInterface {
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
        $this->logger->logInfo("Starting Upload per SFTP.");

        if (!isset($this->config['user'])) {
            $this->logger->logError("Need username for SFTP.");
            return;
        }
        if (!isset($this->config['password'])) {
            $this->logger->logError("Need password for SFTP.");
            return;
        }
        if (!isset($this->config['host']) OR empty($this->config["host"])) {
            $this->logger->logError("Need host for SFTP.");
            return;
        }


        // Foldername:
        $datestring = date("YmdHi");
        $foldername = $this->config['dir_prefix'] . $datestring;

        $stream = "ssh2.sftp://";
        if (!empty($this->config["user"])) {
            $stream .= $this->config["user"] . ":" . $this->config["password"] . "@";
        }
        $stream .= $this->config["host"] . "/";
        if (!empty($this->config["path"])) {
            $stream .= $this->config["path"] ."/";
        }
        $stream .= $foldername . "/";

        mkdir($stream, 0777, true);

        foreach (glob($this->path . "/*") as $file) {
            if (is_file($file)) {
                $thisStream = $stream . basename($file);
                $ret = file_put_contents($thisStream, $file);
                if ($ret === false){
                    $this->logger->logWarn("Failed to transfer file over SFTP: " . $file);
                }
            }
        }

        $this->logger->logInfo("Done Uploading per SFTP.");
    }
}