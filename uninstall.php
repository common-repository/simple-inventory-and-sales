<?php
/**
 * Runs on Uninstall of Simple Inventory
 *
 * @package   Simple Inventory
 * @author    Steve Devine
 * @license   GPL-2.0+
 */


// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}


function delete_plugin_database_tables(){
	global $wpdb;
	$tableArray = [
		$wpdb->prefix . "simple_sales",
		$wpdb->prefix . "simple_product",
	];

	foreach ($tableArray as $tablename) {
		$wpdb->query("DROP TABLE IF EXISTS $tablename");
	}
}


//Delete Tables
delete_plugin_database_tables();


function delete_plugin_pages(){

	$arr = array('admin','admin-product-search','export', 'featured','product-manage','product-search','response');
	foreach ($arr as &$page_name) {
		global $wpdb;
		$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
		wp_delete_post($page_name_id, true);

	}
}

//Delete Pages that came with this plugin
delete_plugin_pages();

?>
