<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class IncrementalDataBackupLZ4 implements BackupInterface {
    private $config;
    private $path;
    private $logger;

    function __construct($path, $config, $logger)
    {
        unset($config['driver']);
        $defaultConfig = array(
            "level0_on_day" => 1
        );
        $this->path = $path;
        $this->config = array_merge($defaultConfig, $config);
        $this->logger = $logger;

        // Check Config
        if (!isset($this->config['snar_file']) OR empty($this->config['snar_file'])) {
            throw new Exception("Configuration error. Need path to a 'snar_file'. Will create it if it does not exist.");
        }
    }

    public function doBackup()
    {
        $config = $this->config;
        $snarFile = $config['snar_file'];
        $fullBackupDay = (int) $config['level0_on_day'];
        unset ($config['snar_file']);
        unset ($config['level0_on_day']);

        foreach ($config as $path) {
            if (is_dir($path)) {
                $name = basename($path);
            } else if (is_file($path)) {
                $name = pathinfo($path)['filename'];
            } else {
                $msg = "Path is not a file or folder. Skipping: " . $path;
                $this->logger->logError($msg);
                continue;
            }

            // Get incremental level:
            $dayOfWeek = (int) date("N");
            $todaysLevel = $dayOfWeek - $fullBackupDay;
            $doFullBackup = false;

            // Path
            $tmppath = $this->path . "/archive_level{$todaysLevel}_$name.tar";

            if ($dayOfWeek === $fullBackupDay) {
                $doFullBackup = true;
            }

            // Make filename unique:
            $counter = 1;
            while (is_file($tmppath)) {
                $tmppath = $this->path . "/archive_level{$todaysLevel}_$name$counter.tar";
                $counter++;
            }

            $this->logger->logInfo("Creating archive from '$path'.");

            $args = "--create --file=$tmppath --listed-incrementa=$snarFile";
            if ($doFullBackup) {
                $args .= " --level=0";
            }
            $cmd = "tar $args $path   > /dev/null 2>&1";
            $this->logger->logInfo("Execute command: " . $cmd);
            exec($cmd, $output, $return);
            unset($output);

            if ($return !== 0) {
                $this->logger->logError("Backup for folowing Path failed with error code $return: $path");
            }

            try {
                $this->compressLZ4($tmppath);

                if (file_exists($tmppath)) {
                    // Can not exist when directory $path was empty.
                    unlink($tmppath);
                }
            } catch (LZ4Exception $e) {
                $this->logger->logError($e->getMessage());
            }

        }
    }

    private function compressLZ4($file)
    {
        $this->logger->logInfo("Going to compress: $file");
        return LZ4::compress($file);
    }
}