<?php

/*
Plugin Name: Oxus
Plugin URI: https://luxibay.com/plugins/oxus
Author: Luxibay
Author URI: https://luxibay.com/
Description: Provides integrative support for WPML when using the Oxygen plugin.
Version: 0.5
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
