<?php 
/*
 * Plugin Name: FWC Image LazyLoad 
 * Plugin URI: http://bugatan.com
 * Description: Lazy image load using jQuery lazyLoadXT plugin.
 * Author: Ebenhaezer BM
 * Author URI: http://ebenhaezerbm.com
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 */

defined( 'ABSPATH' ) OR exit;

defined( 'WPINC' ) OR exit;

$fwc_lazyLoadXT_plugin_uri 		= plugin_dir_url(__FILE__);
$fwc_lazyLoadXT_plugin_basename = plugin_basename( __FILE__ );

include "inc/admin-settings.php";
include "inc/class-lazyLoad.php";
