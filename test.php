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
        $this->load_automattic();
		$this->create_client();
    }

    function load_automattic() {
        // require __DIR__ . '/vendor/autoload.php';

        if ( !class_exists( 'HttpClient' ) ) {
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/Client.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClient.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/BasicAuth.php');;
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClientException.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/OAuth.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Options.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Request.php');
            require_once(__DIR__ . '../../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Response.php');
        }
    }

    function create_client(){
        $url = $this->config['API_URL'];
        $ck  = $this->config['API_KEY'];
        $cs  = $this->config['API_SECRET'];

        $this->woocommerce = new Client(
            $url, 
            $ck, 
            $cs,
            [
                'version' => 'wc/v3',
                'verify_ssl' => false,
                'timeout' => 5000
            ]
        );
    }

    /*
        /wp-content/plugins/reactor/test.php
    */
    function get(){
        dd($this->woocommerce->get('products'));
    }
}


$test = new Test();
$test->get();