<?php
/**
 * Created by PhpStorm.
 * User: DeDu
 * Date: 08.03.14
 * Time: 18:45
 */

interface StoreInterface {
    function __construct($path, $config, $logger);
    public function store();
} 