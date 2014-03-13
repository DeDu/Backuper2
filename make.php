<?php
/**
 * Makes the PHAR-executable
 *
 * @author   Matthias Dunkel <matthias.dunkel@dedu.ch>
 * @date     13.03.14
 */

$pathFrom = __DIR__ . "/src";
$path = __DIR__ . '/bin/backuper2.phar';
if (is_file($path)) {
    unlink($path);
}
if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0777, true);
}
$tarphar = new Phar($path);
$phar = $tarphar->convertToExecutable(Phar::PHAR);
$phar->buildFromDirectory($pathFrom);
$defaultstub = $phar->createDefaultStub('start.php', 'start.php');
$phar->setStub("#!/usr/bin/env php \n" . $defaultstub);