<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:21
 */

class Backuper {
    private $config;
    private $logger;

    function __construct($config, $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function startBackup()
    {
        $this->prepare();
        $this->generateData();
        $this->enctyptData();
        $this->storeData();

        $this->cleanup();
    }

    public function startDecrypt($keyfile, $pathtofiles)
    {
        if (isset($this->config['encrypt']) AND isset($this->config['encrypt']['driver'])) {
            $this->logger->logInfo("Going to decrypt.");

            $this->config['encrypt']['private_key'] = $keyfile;

            $driver = $this->config['encrypt']['driver'];
            $driver = new $driver($pathtofiles, $this->config['encrypt'], $this->logger);
            if ($driver instanceof EncryptInterface) {
                $driver->doDecrypt();
            } else {
                $this->logger->logError("Driver must implement EncryptInterface!");
            }
        } else {
            $this->logger->logInfo("Do NOT decrypt. Nothing is specified in config.");
        }
    }

    private function prepare()
    {
        // Make sure cache-folder exists:
        if (!is_dir($this->config['cache_dir'])) {
            if (! mkdir($this->config['cache_dir'], 0777, true) ) {
                $error = "Can't create cache-directory: " . $this->config['cache_dir'];
                $this->logger->logFatal($error);
                throw new Exception($error);
            }
        }
    }

    private function generateData()
    {
        foreach ($this->config['backup'] as $key => $driverconfig) {
            if (isset($driverconfig['driver'])) {
                $driver = new $driverconfig['driver']($this->config['cache_dir'], $driverconfig, $this->logger);
                if ($driver instanceof BackupInterface) {
                    $driver->doBackup();
                } else {
                    $this->logger->logError("Driver must implement BackupInterface!");
                }
            } else {
                $this->logger->logWarn("No driver configured under {$key}. Ignoring.");
            }
        }
    }

    private function enctyptData()
    {
        if (isset($this->config['encrypt']) AND isset($this->config['encrypt']['driver'])) {
            $this->logger->logInfo("Going to encrypt.");

            $driver = $this->config['encrypt']['driver'];
            $driver = new $driver($this->config['cache_dir'], $this->config['encrypt'], $this->logger);
            if ($driver instanceof EncryptInterface) {
                $driver->doEncrypt();
            } else {
                $this->logger->logError("Driver must implement EncryptInterface!");
            }
        } else {
            $this->logger->logInfo("Do NOT encrypt.");
        }
    }

    private function storeData()
    {
        foreach ($this->config['storages'] as $key => $driverconfig) {
            if (isset($driverconfig['driver'])) {
                $driver = new $driverconfig['driver']($this->config['cache_dir'], $driverconfig, $this->logger);
                if ($driver instanceof StoreInterface) {
                    $driver->store();
                } else {
                    $this->logger->logError("Driver must implement StoreInterface!");
                }
            } else {
                $this->logger->logWarn("No driver configured under {$key}. Ignoring.");
            }
        }
    }

    private function cleanup()
    {
        foreach (glob($this->config['cache_dir'] . '/*') as $file) {
            unlink($file);
        }
    }
} 