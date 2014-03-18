<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class DataBackupLZ4 implements BackupInterface {
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
                unset($arch);

                // Try-Catch to only unlink $tmppath when compression was successfully
                try {
                    $this->compressLZ4($tmppath);

                    if (file_exists($tmppath)) {
                        // Can not exist when directory $path was empty.
                        unlink($tmppath);
                    }
                } catch (LZ4Exception $e) {
                    $this->logger->logError($e->getMessage());
                }

            } catch (PharException $e) {
                $this->logger->logError($e->getMessage());
            }

        }
    }

    private function compressLZ4($file)
    {
        $this->logger->logInfo("Going to compress: $file");
        $compressedfilepath = $file . ".lz4";

        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
        );
        $cwd = '/tmp';
        $process = proc_open('lz4', $descriptorspec, $pipes, $cwd);

        if (is_resource($process)) {
            $compressedfile = fopen($compressedfilepath, "w");
            $tarfile = fopen($file, "r");

            $size = 0;
            while (!feof($tarfile)) {
                //$size += fwrite($pipes[0],fread($tarfile, 8192));
                $size += stream_copy_to_stream($tarfile, $pipes[0], 1024);
                $this->logger->logInfo("round. written: $size --- mem: " . memory_get_usage());
            }
            fclose($tarfile);
            fclose($pipes[0]);
            $size = 0;
            while (!feof($pipes[1])) {
                //$size += fwrite($compressedfile,fread($pipes[1], 8192));
                $size += stream_copy_to_stream($pipes[1], $compressedfile, 1024);
                $this->logger->logInfo("round2. written: $size");
            }
            fclose($pipes[1]);
            fclose($compressedfile);

            $return_value = proc_close($process);
            if ($return_value !== 0) {
                throw new LZ4Exception("Compression Failed. LZ4 Error Code: $return_value");
            }
        } else {
            throw new LZ4Exception("Wasn't able to execute LZ4.");
        }

    }
}