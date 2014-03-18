<?php
/**
 * User: DeDu
 * Date: 18.03.14
 * Time: 20:56
 */

class KLoggerWraper extends KLogger{

    public function log($line, $severity, $args = self::NO_ARGUMENTS) {

        if ($severity <= self::ERR) {
            $cmd = new Commando\Command();
            $cmd->error(new Exception($line));
        }

        parent::log($line, $severity, $args);
    }

} 