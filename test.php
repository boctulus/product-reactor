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


/*
    INSERT INTO `{$wpdb->prefix}product_updates` (`operation`, `product_id`) 
VALUES ('DELETE', 102)
ON DUPLICATE KEY UPDATE
`operation` = 'DELETE';
*/





