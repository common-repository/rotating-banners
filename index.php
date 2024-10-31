<?php
/*
Plugin Name: Rotating Banners
Plugin URI: https://wordpress.org/plugins/rotating-banners
Description: Create Sections of your website that change dynamically each minute/hour/day/month you set.
Version: 2.2.2
Author: MarketingHack.fr
Author URI: http://marketinghack.fr
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: rotating-banners
*/

defined( 'ABSPATH' ) or die( "This page intentionally left blank." );

// load languages
defined('TDFM_rb') || define('TDFM_rb', 'rotating-banners');
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( TDFM_rb, false, dirname(plugin_basename(__FILE__)) . '/languages/' );
} );

// Start
include plugin_dir_path( __FILE__ ) . "controllers/plugin.php";
new FMAD_Class_Plugin__rotating_banners(__FILE__);
