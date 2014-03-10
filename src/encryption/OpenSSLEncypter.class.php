<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class OpenSSLEncypter implements EncryptInterface {
    private $config;
    private $path;
    private $logger;

    function __construct($path, $config, $logger)
    {
        $this->path = $path;
        $this->config = $config;
        $this->logger = $logger;
    }


    public function doEncrypt()
    {
        if (is_file($this->config['public_key'])) {
            $files = glob($this->path . '/*');
            $files = implode(' ', $files);

            $backupfile = $this->path . '/backup.tar.gz';
            $backupfileencrypted = $this->path . '/backup.tar.gz.enc';

            $cmd = "tar -zcf $backupfile -P $files";
            $this->logger->logInfo("Creating archive with command: $cmd");
            exec($cmd);

            $cmd = "rm $files";
            $this->logger->logInfo("Removing archived files with command: $cmd");
            exec($cmd);

            $cmd = "openssl smime -encrypt -binary -aes-256-cbc -in $backupfile -out $backupfileencrypted -outform DER " . $this->config['public_key'];
            $this->logger->logInfo("Removing archived files with command: $cmd");
            exec($cmd);

            $this->logger->logInfo("Removing uncrypted archive.");
            unlink($backupfile);
        } else {
            $this->logger->logError('Can not find public key to enrypt.');
        }
    }
}