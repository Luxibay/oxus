<?php

/*
Plugin Name: Oxus
Plugin URI: https://nauticode.luxibay.com/oxus
Author: Nauticode by Luxibay
Author URI: https://nauticode.luxibay.com/
Description: Oxus provides integrative support for WPML when using the Classic Oxygen plugin.
Version: 0.9.1
Requires Plugins: sitepress-multilingual-cms, oxygen
Text Domain: oxus
*/

define("OXUS_FRAME", plugin_dir_path(__FILE__) . "frame");
define('OXUS_MAIN', __FILE__);
define('OXUS_PATH', plugin_dir_path(__FILE__));
define('OXUS_NAME', 'Oxus');

add_action('init', 'oxus_cast');

function oxus_cast()
{

    require_once(OXUS_FRAME . '/oxus-connect.php');

    $oxus = new Oxus_Connect();
}


add_action('init', function(){
	if ( ! class_exists( 'SureCart\Licensing\Client' ) ) {
		require_once __DIR__ . '/licensing/src/Client.php';
	}
	
	// initialize client with your plugin name and your public token.
	$client = new \SureCart\Licensing\Client( 'Oxus', 'pt_XKrGiA2xAs23SSkz2Q1k1EJH', __FILE__ );
	
	// set your textdomain.
	$client->set_textdomain( 'oxus' );
	
	// add the pre-built license settings page.
	$client->settings()->add_page( 
		[
			'type'                 => 'menu', // Can be: menu, options, submenu.
			//'parent_slug'          => 'options-general', // add your plugin menu slug.
			'page_title'           => 'Oxus License',
			'menu_title'           => 'Oxus License',
			'capability'           => 'manage_options',
			'menu_slug'            => $client->slug . '-manage-license',
			'icon_url'             => '',
			'position'             => null,
			'parent_slug'          => '',
		] 
	);
});
