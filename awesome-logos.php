<?php
/*--------------------------------------------------------------------------------------
 # Plugin Name: Awesome Logos
 # Plugin URI : http://www.wpshopee.com/awesome-logos-wordpress-plugin/
 # Description: Awesome Logos to show slider carousel and grids of brands, partners logos or to showcase photos.
 # Version: 1.1.1
 # Author: WPshopee
 # Author URI: http://www.wpshopee.com
 *-------------------------------------------------------------------------------------*/

/**
 * Awesome Logos
 * Copyright (C) 2016, WPshopee - wpshopee@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 **/

if ( !function_exists( 'add_action' ) ) {
	echo 'Uh huh! Plugin can not do much when called directly.';
	exit;
}
#declare constants
define( 'WPS_AWESOME_LOGOS_VERSION', '1.1.1' );
define( 'AWESOME_LOGOS_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'AWESOME_LOGOS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPS_AWESOME_LOGOS_TABLE','wps_awesome_logos');
define( 'WPS_AWESOME_LOGOS_META','wps_awesome_logos_meta');
require_once ( AWESOME_LOGOS_PLUGIN_DIR . '/modules/install-logos.php');
#register plugin function
function setup_wps_awesome_logos() {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
						id int(5) NOT NULL AUTO_INCREMENT,
						slider_id int(5) NOT NULL DEFAULT '1',
						post_id int(11) NOT NULL,
						date datetime NOT NULL,
						slides_order int(5) NOT NULL DEFAULT '0',
						UNIQUE KEY id(id)
					);";
			$rs = $wpdb->query($sql);
		}
	   	$meta_table_logos = $table_prefix.WPS_AWESOME_LOGOS_META;
		if($wpdb->get_var("show tables like '$meta_table_logos'") != $meta_table_logos) {
			$sql = "CREATE TABLE $meta_table_logos (
						slider_id int(5) NOT NULL AUTO_INCREMENT,
						slider_name varchar(100) NOT NULL default '',
						slider_type varchar(100) NOT NULL default '',
						UNIQUE KEY slider_id(slider_id)
					);";
			$rs2 = $wpdb->query($sql);
		}
		update_option('wps_awesome_logos_ver', WPS_AWESOME_LOGOS_VERSION );
}
register_activation_hook( __FILE__, 'setup_wps_awesome_logos' );
add_action( 'init', 'logos_slider_enqueue_scripts' ); #LC-2
add_action( 'admin_init', 'logos_slider_adminpage_scripts' ); #LC-5
add_action( 'init', 'create_wps_post_type', 11 ); #LC-28 
add_action( 'edit_post', 'wps_logos_update_cfields'); #LC-100
add_action( 'publish_post', 'wps_logos_update_cfields');
add_action( 'admin_menu', 'wps_logos_cpt_meta_box'); #LC-125
add_action( 'manage_posts_custom_column', 'wpscpt_custom_columns'); #LC-174
add_filter( 'manage_edit-wps_logos_columns', 'wpscpt_edit_columns'); #LC-163
require_once ( AWESOME_LOGOS_PLUGIN_DIR . '/settings/dashboard.php');
require_once ( AWESOME_LOGOS_PLUGIN_DIR . '/settings/settings.php');
require_once ( AWESOME_LOGOS_PLUGIN_DIR . '/modules/functions.php');
require_once ( AWESOME_LOGOS_PLUGIN_DIR . '/modules/embed.php');
function wps_al_remove_footer_admin_text () { 
	$wps_screen = get_current_screen();
	if ( $wps_screen->post_type == 'wps_logos' ) {
		echo '<span id="footer-thankyou">Developed by <a href="http://www.wpshopee.com" target="_blank">WPshopee</a>. | Please rate <a href="https://wordpress.org/support/view/plugin-reviews/awesome-logos?rate=5#postform" target="_blank">Awesome logos</a> | Need help? <a href="http://www.wpshopee.com/contact-us/" target="_blank">Contact us</a></span>';
	}
	else { echo '<span id="footer-thankyou">Thank you for creating with <a href="https://wordpress.org/">WordPress</a>.</span>'; }
	return;
}
add_filter('admin_footer_text', 'wps_al_remove_footer_admin_text');
?>