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

function send($message, $status = 200){
	http_response_code($status);
	echo json_encode($message);
	exit;
}


#	GET /index.php/wp-json/connector/v1/products?api_key=xxx
add_action( 'rest_api_init', function () {
	register_rest_route( 'connector/v1', '/products', array(
		'methods' => 'GET',
		'callback' => 'get_updated_products',
        'permission_callback' => '__return_true'
	) );
} );

#	GET /index.php/wp-json/connector/v1/products/all
add_action( 'rest_api_init', function () {
	register_rest_route( 'connector/v1', '/products/all', array(
		'methods' => 'GET',
		'callback' => 'get_products',
        'permission_callback' => '__return_true'
	) );
} );


function get_updated_products(){
    $api_key = $_GET['api_key'] ?? NULL;

    if ($api_key != Reactor::getConfig()['API_KEY']){
        send("Acceso no autorizado. API KEY inválida");
    }

    $stack = Reactor::getStack();

    // product_id(s) del stack a ser limpiados
    $ids   = array_column($stack, 'id');

    $arr = [];
    foreach ($stack as $row){
        $product = $product = wc_get_product($row->product_id);

        $p = Reactor::dumpProduct($product);
        $p['operation'] = $row->operation;

        $arr[] = $p;
    }

    $ok = Reactor::clearStack($ids);    

    return $arr;
}


function get_products(){
    global $wpdb;

    $api_key  = $_GET['api_key'] ?? NULL;
    $since_id = $_GET['since_id'] ?? 0;
    $limit    = $_GET['limit'] ?? 100;
 
    if ($api_key != Reactor::getConfig()['API_KEY']){
        send("Acceso no autorizado. API KEY inválida");
    }

    $sql = "SELECT DISTINCT posts.ID AS product_id 
    FROM {$wpdb->prefix}posts AS posts, {$wpdb->prefix}terms AS terms, {$wpdb->prefix}term_relationships AS term_relationships, {$wpdb->prefix}term_taxonomy AS term_taxonomy
    WHERE term_relationships.object_id = posts.ID AND term_taxonomy.term_id = terms.term_id AND term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id AND posts.post_type = 'product' AND posts.post_status = 'publish'
    AND posts.ID > $since_id 
    ORDER BY posts.ID 
    LIMIT $limit;";

    $data = $wpdb->get_results($sql);
    if (empty($data)){
        return [];
    }

    $pids = array_column($data, 'product_id'); 

    $arr = [];
    foreach ($pids as $pid){
        $product = $product = wc_get_product($pid);

        $p = Reactor::dumpProduct($product);
        $p['operation'] = 'CREATE';

        $arr[] = $p;
    }

    return $arr;
}

