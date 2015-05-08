<?php
/*
Plugin Name: Cool Tables
Plugin URI: http://websensepro.com/cool-tables
Description: Create stylish and cool wordpress tables for pages and posts.
Version: 1.2
Author: Bilal Naseer
Author URI: http://websensepro.com
*/

/*
Actionhooks
*/
add_action('admin_menu', 'cool_tables_menu_items'); //menu
add_action('init', 'cool_tables_plugin_requests', 9999); //all db requests
add_action('admin_head', 'cool_tables_admin_register_head'); //admin css
add_action('plugins_loaded', 'cool_tables_update_function'); //update
/*
Shortcodes
*/
add_shortcode('cs_table', 'cool_tables_shortcode'); //shortcode function

/*
register and unregister hooks
*/
register_activation_hook(__FILE__,'cool_tables_install_plugin');
register_uninstall_hook(__FILE__, 'cool_tables_uninstall_plugin');

/*
creates the content for the shortcode
returns $table
*/
function cool_tables_shortcode ($atts)
{
	extract(shortcode_atts(array(
		'id' => 'The correct attribute is missing'
	), $atts));
		
	include( 'php/shortcode.php' );
		
	return do_shortcode($table);
}

//Add a submenu page
function cool_tables_menu_items() 
{ 
	add_menu_page('Cool Tables', 'Cool Tables', 'manage_options', 'cool_tables', 'cool_tables_plugin_page');
} 

/*
*	function that displays all admin pages
*/
function cool_tables_plugin_page () { 
	if (!current_user_can('manage_options'))  
	{ 
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	global $wpdb;
	if (isset($_GET['action']) && $_GET['action'] == 'edit_table') 
	{ 
		require_once( 'php/edit_table.php' );
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'edit_style') 
	{ 
		require_once( 'php/edit_style.php' );
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'rename_table') 
	{ 
		require_once( 'php/rename_table.php' );	
	} 
	elseif (isset($_GET['action']) && $_GET['action'] == 'ws_import_table') 
	{ 
		require_once( 'php/import_table.php' );	
	} 
	else 
	{
		require_once( 'php/all_tables.php' );
	}
}

/*
*	Handles all requests to and from database
*/
function cool_tables_plugin_requests()
{ 
	require_once( 'php/requests.php' );
}

/*
*	Adds links to stylesheet in admin pages
*/
function cool_tables_admin_register_head() 
{
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/cool_tables.css';
    $preview_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/table_skins.php';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
    echo "<link rel='stylesheet' type='text/css' href='$preview_url' />\n";
}

/*
*	Installs the table wp_cool_tables 
*/
function cool_tables_install_plugin() 
{
	$cool_tables_db_version = "1.0";
	$cool_tables_version = "1.2";
	global $wpdb;

	$table_name = $wpdb->prefix . "cool_tables";
	$sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  tablename VARCHAR(150) DEFAULT '' NOT NULL,
	  shortcode VARCHAR(500) DEFAULT '' NOT NULL,
	  rows VARCHAR(500) DEFAULT '' NOT NULL,
	  cols VARCHAR(500) DEFAULT '' NOT NULL,
	  style VARCHAR(500) DEFAULT '' NOT NULL,
	  design TEXT(5000),
	  advanced TEXT(5000),
	  headlines TEXT(5000),
	  content TEXT(100000),
	  UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1 ;";
		
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	add_option("cool_tables_db_version", $cool_tables_db_version);
	add_option("cool_tables_version", $cool_tables_version);
}

/*
* Updates for database and versions of the plugin 
*/
function cool_tables_update_function() 
{
	$cool_tables_db_version = '1.0';
	$cool_tables_version = '1.2';
	
	$db_ver = get_site_option('cool_tables_db_version');
	$plug_ver = get_site_option('cool_tables_version');
    
	if ($db_ver == '1.0' || $db_ver == '1.01') 
	{
		add_option('cool_tables_copy', 1);
		update_option("cool_tables_db_version", '1.02');
    }
	if ($db_ver != $cool_tables_db_version) 
	{
		update_option("cool_tables_db_version", '1.0');
    }
	if ($plug_ver != $cool_tables_version) 
	{
        update_option("cool_tables_version", '1.2');
    }
}
/*
*	Removes the the table wp_cool_tables if plugin is uninstalled
*	If plugin is inactivaded, nothing will be deleted
*/
function cool_tables_uninstall_plugin () 
{
	global $wpdb;
	$table_name = $wpdb->prefix . "cool_tables";
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	delete_option("cool_tables_db_version");
	delete_option("cool_tables_version");
	delete_option("cool_tables_copy");
}

?>