<?php
/*
Plugin Name: Reactor
Description: Product Manager Updater
Version: 1.0.0
Author: boctulus@gmail.com <Pablo>
*/

use reactor\libs\Debug;
use reactor\libs\Files;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/libs/Debug.php';
require __DIR__ . '/libs/Files.php';
require __DIR__ . '/config.php';
require __DIR__ . '/ajax.php';


if (!function_exists('dd')){
	function dd($val, $msg = null, $pre_cond = null){
		Debug::dd($val, $msg, $pre_cond);
	}
}

/**
 * Check if WooCommerce is active
 */
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}


// http://hookr.io/functions/is_checkout/
//if ( ! is_cart() && ! is_checkout()  ) {
//		return;
//}

$action = null;

add_action( 'woocommerce_update_product', 'sync_on_product_update', 11, 1 );

function sync_on_product_update( $product_id ) {
	$action = 'edit';
    $product = wc_get_product( $product_id );
    
	dd($product, "Producto - ". $action);
	//Files::dump($product, 'product_edit');
	exit; //
}


// este hook reacciona a productos agregados o modificados
add_action('added_post_meta', 'sync_on_product_add', 10, 4 );

function sync_on_product_add( $meta_id, $post_id, $meta_key, $meta_value ) {  
	if ( get_post_type( $post_id ) == 'product' ) { // we've been editing a product
		//dd($meta_key, 'META KEY');  
		
		/*
			$meta_key == 
			
			_wp_trash_meta_status  => es borrado
			_wp_old_slug => restaurado
			_stock => editado
		*/

		// si ya lo cogió el otro hook
		if ($meta_key == '_stock'){
			return;
		}

		switch ($meta_key){
			case '_wp_trash_meta_status': 
				$action = 'trashed';
				break;
			case '_wp_old_slug':
				$action = 'restored';
				break;
		}


		$product = wc_get_product( $post_id );			
		dd($product, "Producto - ". $action);

		//Files::dump($product, 'product_add');
		exit; //
	}
 
}


