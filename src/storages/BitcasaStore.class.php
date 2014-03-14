<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:32
 */

class BitcasaStore implements StoreInterface {
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
        $this->logger->logInfo("Starting Upload to Bitcasa.");

        if (!isset($this->config['access_token']) OR empty($this->config['access_token'])) {
            $this->logger->logError("Need access-token for Bitcasa.");
            return;
        }
        if (!isset($this->config['base_backup_path']) OR empty($this->config['base_backup_path'])) {
            $this->logger->logError("Need base_backup_path for Bitcasa.");
            return;
        }

        $client = new BitcasaClient();
        $client->setAccessToken($this->config['access_token']);

        //Create folder on Bitcasa:
        $datestring = date("YmdHi");
        /** @var BitcasaFolder $backupFolder */
        $backupFolder = $client->createFolder($this->config['base_backup_path'], $this->config['dir_prefix'] . $datestring);


        // Dateien darin hochladen:
        foreach (glob($this->path . "/*") as $file) {
            if (is_file($file)) {
                $this->logger->logInfo('Upload file to Bitcasa: ' . $file);
                $backupFolder->upload($client, $file);
            }
        }
    }
}