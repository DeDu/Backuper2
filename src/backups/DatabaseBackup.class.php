<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class DatabaseBackup implements BackupInterface {
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
        foreach ($this->config as $driver => $config) {
            switch ($driver) {
                case "mysql":
                    $this->dumpMysql($config);
                    break;
                default:
                    $this->logger->logWarn("Don't support $driver ...");
                    break;
            }
        }
    }

    private function dumpMysql($config)
    {
        foreach ($config['databases'] as $db) {
            $path = $this->path . "/DB_MySQL_" . $db . ".sql";
            $cmd = "mysqldump -u " . $config['user'] . " -p" . $config['password'] . " " . $db . " > $path";
            $this->logger->logInfo("Dump MySQL-Database '$db' with command: $cmd");

            $output = "";
            $returnVar = "";
            exec($cmd, $output, $returnVar);
            unset($output);

            if ($returnVar !== 0) {
                $this->logger->logError('Command failed: ' . $cmd);
            } else {
                if (isset($config['compression'])) {
                    switch ($config['compression']) {
                        case "LZ4":
                            try {
                                LZ4::compress($path);
                                unlink($path);
                            } catch (LZ4Exception $e) {
                                $this->logger->logError($e->getMessage());
                            }
                            break;
                    }
                }
            }

        }
    }
}