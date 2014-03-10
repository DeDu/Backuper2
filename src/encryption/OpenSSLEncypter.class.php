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
            //$files = implode(' ', $files);
            $key = file_get_contents($this->config['public_key']);

            foreach ($files as $file) {
                $this->logger->logInfo("Encrypt file: $file");
                $encryptonSuccessfull = openssl_pkcs7_encrypt(
                    $file,
                    $file . ".enc",
                    $key,
                    [],
                    PKCS7_BINARY,
                    OPENSSL_CIPHER_AES_256_CBC);

                if ($encryptonSuccessfull) {
                    $this->logger->logInfo("Delete uncrypted file from cache: $file");
                    unlink($file);
                } else {
                    $this->logger->logError("Encryption failed for file: $file");
                }
            }

        } else {
            $this->logger->logError('Can not find public key to enrypt.');
        }
    }
}