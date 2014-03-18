<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class PublicKeyEncrypter implements EncryptInterface {
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
            $publickey = file_get_contents($this->config['public_key']);
            $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);

            foreach ($files as $file) {
                $this->logger->logInfo("Encrypt file: $file");
                // IV:
                $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
                // Create new random Key:
                $key = openssl_random_pseudo_bytes(32);
                // Encrypt:
                $fileStream = fopen($file, "r");
                $encFileStream = fopen($file . ".enc.data", "w");

                $opts = [
                    'iv' => $iv,
                    'key' => $key,
                    'mode' => 'cbc'
                ];
                stream_filter_append($encFileStream, 'mcrypt.rijndael-256', STREAM_FILTER_WRITE, $opts);

                while (!feof($fileStream)) {
                    fwrite($encFileStream,fread($fileStream, 8192));
                }
                //stream_copy_to_stream($fileStream, $encFileStream, 1024);

                fclose($fileStream);
                fclose($encFileStream);
                // Encrypt random generated key and save it:
                $encryptedKey = null;
                openssl_public_encrypt($key, $encryptedKey, $publickey);
                file_put_contents($file . ".enc.key", $encryptedKey);
                // Save Initial Vetor:
                file_put_contents($file . ".enc.iv", $iv);

                // Delete unencrypted file:
                $this->logger->logInfo("Delete uncrypted file from cache: $file");
                unlink($file);

            }

        } else {
            $this->logger->logError('Can not find public key to enrypt.');
        }
    }

    public function doDecrypt()
    {
        $privateKey = file_get_contents($this->config['private_key']);
        if (is_file($this->config['public_key'])) {
            $files = glob($this->path . '/*.enc.data');
            $publickey = file_get_contents($this->config['public_key']);

            foreach ($files as $file) {
                $basePath = substr($file, 0, -5);
                $savePath = substr($file, 0, -9);
                $this->logger->logInfo("Decrypt file: $file");
                // IV:
                $iv = file_get_contents($basePath . '.iv');
                // decrypt Key:
                $key = file_get_contents($basePath . '.key');
                $decryptedKey = null;
                openssl_private_decrypt($key, $decryptedKey, $privateKey);
                // Decrypt:
                $fileStream = fopen($savePath, "w");
                $encFileStream = fopen($file, "r");

                $opts = [
                    'iv' => $iv,
                    'key' => $decryptedKey,
                    'mode' => 'cbc'
                ];
                stream_filter_append($encFileStream, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
                stream_copy_to_stream($encFileStream, $fileStream);

                fclose($fileStream);
                fclose($encFileStream);


                /*$decryptedData = mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_256,
                    $decryptedKey,
                    file_get_contents($file),
                    MCRYPT_MODE_CBC,
                    $iv);
                file_put_contents($savePath, $decryptedData);*/
            }

        } else {
            $this->logger->logError('Can not find public key to enrypt.');
        }
    }
}