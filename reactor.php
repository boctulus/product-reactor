<?php
/*
Plugin Name: Reactor
Description: Product Manager Updater
Version: 1.0.0
Author: boctulus@gmail.com <Pablo>
*/

use reactor\libs\Debug;
use reactor\libs\Files;
use Automattic\WooCommerce\Client;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/libs/Debug.php';
require __DIR__ . '/libs/Files.php';
require __DIR__ . '/../../../wp-admin/includes/export.php';
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


//add_action( 'plugins_loaded', 'load_automattic', 0 );

/*
	https://www.mootpoint.org/blog/woocommerce-hook-product-updated-added/
*/


$action = null;

class Reactor 
{
	protected $action = null;
	protected $config;
	protected $woocommerce;

	function __construct()
	{
		$this->config = include __DIR__ . '/config.php';

		add_action( 'woocommerce_update_product', [$this, 'sync_on_product_update'], 11, 1 );
		add_action('added_post_meta', [$this, 'sync_on_product_add'], 10, 4 );
	}	

	function load_automattic() {
		// require __DIR__ . '/vendor/autoload.php';

		if ( !class_exists( 'HttpClient' ) ) {
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/Client.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClient.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/BasicAuth.php');;
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClientException.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/OAuth.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Options.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Request.php');
			require_once( plugin_dir_path(__FILE__) . '../../../vendor/automattic/woocommerce/src/WooCommerce/HttpClient/Response.php');;
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

	function onCreate($product){
		//dd($product, 'product_create');
				
		try {
			$res = $this->woocommerce->post('products', $product);	
		} catch (\Exception $e){
			dd($product, "Product with id=" . $product->get_id());
			dd($e->getMessage(), 'Error');       
			//Files::logger("product_id=$product_id: ". strip_tags($e->getMessage()), 'fails.txt');
		}	
	}

	function onUpdate($product){
		dd($product, 'product_edit');
		exit; //
	}

	function onDelete($product){

	}

	function onRestore($product){

	}

	private function sync_on_product_update( $product_id ) {
		$this->load_automattic();
		$this->create_client();

		$action = 'edit';
		$product = wc_get_product( $product_id );
		$this->onUpdate($product);
	}

	function sync_on_product_add( $meta_id, $post_id, $meta_key, $meta_value ) {  
		if (get_post_type( $post_id ) == 'product') 
		{ 
			$this->load_automattic();
			$this->create_client();

			//dd($meta_key, 'META KEY');  

			/*
				$meta_key == 
				
				_wp_trash_meta_status  => es borrado
				_wp_old_slug => restaurado
				_stock => editado
			*/

			// si ya lo cogió el otro hook
			if ($this->action == 'edit'){
				return;
			}

			$product = wc_get_product( $post_id );

			switch ($meta_key){
				case '_wp_trash_meta_status': 
					$this->action = 'trash';
					$this->onDelete($product);
					break;
				case '_wp_old_slug':
					$this->action = 'restore';
					$this->onRestore($product);
					break;
				case '_stock':
					$this->action = 'edit';
					$this->onUpdate($product);
					break;
				// creación
				default:
					$this->action = 'create';
					$this->onCreate($product);
			}
		}
	
	}
}


$reactor = new Reactor();
