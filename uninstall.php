<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// check for needed function
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// delete only if no other plugin-version(free/pro) is installed
if ( ! array_key_exists( 'rotating-banners-pro/index.php', get_plugins() ) ) {
	// Delete from wp_options
	delete_option("fmad_rotating-banners");

	// Delete all fmrb custom post type
	global $wpdb;
	$fmrb_wpdb_results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'posts WHERE post_type = "fmrb"', ARRAY_A );
	if (!empty($fmrb_wpdb_results) && is_array($fmrb_wpdb_results) && count($fmrb_wpdb_results) > 0) {
		foreach ($fmrb_wpdb_results as $fmrb_cpt) {
			wp_delete_post( $fmrb_cpt['ID'] );
		}
	}
}