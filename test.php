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


#$config = include __DIR__ . '/config.php';        

#Reactor::toStack(40, 'CREATE');
#Reactor::toStack(120, 'CREATE');
#Reactor::toStack(350, 'DELETE');
#Reactor::toStack(120, 'UPDATE');

dd(Reactor::getStack());
#Reactor::clearStack([16,6]);

