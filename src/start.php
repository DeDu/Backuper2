<?php
require_once("vendor/autoload.php");

$cmd = new Commando\Command();

try {

    $cmd->argument()
        ->referToAs('Action')
        ->describedAs('Either "backup" or "decrypt".')
        ->require()
        ->must(function($action){
            $allowedActions = ['backup', 'decrypt'];
            return in_array($action, $allowedActions);
        });

    $cmd->argument()
        ->referToAs('Directory')
        ->describedAs('Directory to decrypt when action "decrypt" is set.')
        ->file();

    $cmd->flag('c')
        ->aka('config')
        ->require()
        ->file()
        ->describeAs('Path to config file.');

    $cmd->flag('k')
        ->aka('key')
        ->file()
        ->describeAs('Path to private key for decryption. If "action" is decrypt, this flag is required!');

    $flags      = $cmd->getFlagValues();
    $arguments  = $cmd->getArgumentValues();

    $config = (include($flags['c']));
    $logger = new KLogger($config['log_dir'], KLogger::INFO);
    $backuper = new Backuper($config, $logger);

    switch ($arguments[0]) {
        case 'decrypt':

            if (is_dir($arguments[1])) {
                if (is_file($flags['k'])) {
                    $backuper->startDecrypt($flags['k'], $arguments[1]);
                } else {
                    $msg = "Path to private key is not set! Use '-k' flag to set the key-path.";
                    $logger->logError($msg);
                    throw new Exception($msg);
                }
            } else {
                $msg = "Path to directory to decrypt is not set!";
                $logger->logError($msg);
                throw new Exception($msg);
            }

            break;
        default:
            $backuper->startBackup();
            break;
    }
} catch (Exception $e) {
    $cmd->error($e);
}