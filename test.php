<?php

use reactor\libs\Debug;
use reactor\libs\Files;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../../../wp-load.php';

if (!function_exists('dd')){
	function dd($val, $msg = null, $pre_cond = null){
		Debug::dd($val, $msg, $pre_cond);
	}
}


$config = include __DIR__ . '/config.php';        


function toStack($pid, $operation)
{
    global $wpdb;

    if (!is_numeric($pid)){
        throw new \InvalidArgumentException("Product id $pid is invalid");
    }

    if (! in_array($operation, ['UPDATE', 'DELETE', 'CREATE', 'RESTORE'])){
        throw new \InvalidArgumentException("Operation $operation is invalid");
    }

    $wpdb->query("INSERT INTO `{$wpdb->prefix}product_updates` (`operation`, `product_id`) 
    VALUES ('$operation', $pid)
    ON DUPLICATE KEY UPDATE
    `operation` = '$operation';");
}


toStack(350, 'DELETE');


