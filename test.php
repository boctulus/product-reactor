<?php

use reactor\libs\Debug;
use reactor\libs\Files;
use Automattic\WooCommerce\Client;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!function_exists('dd')){
	function dd($val, $msg = null, $pre_cond = null){
		Debug::dd($val, $msg, $pre_cond);
	}
}

class Test
{
    protected $config;
	protected $woocommerce;

    function __construct(){
        $this->config = include __DIR__ . '/config.php';        
    }

    /*
        /wp-content/plugins/reactor/test.php
    */
    function dump(){
       //dump_product()
    }
}


$test = new Test();
$test->dump();