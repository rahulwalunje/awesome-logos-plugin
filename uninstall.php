<?php
#if uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
#get list of all settings
global $wpdb,$table_prefix;
$slider_meta = $table_prefix.WPS_AWESOME_LOGOS_META; 
$sql = "SELECT * FROM $slider_meta";
$deletelogos = $wpdb->get_results($sql, ARRAY_A);
#delete all created options and tables by plugin
foreach ($deletelogos as $logo) {
    $sid = $logo['slider_id'];
    $wps_logos_settings = 'wps_logos_settings'.$sid;
    delete_option($wps_logos_settings); 
}
delete_option('wps_awesome_logos_ver');
$wps__logos_table = $table_prefix.'wps_awesome_logos';
$wps__logos_meta = $table_prefix.'wps_awesome_logos_meta';
$sql = "DROP TABLE $wps__logos_table;";
$wpdb->query($sql);
$sql = "DROP TABLE $wps__logos_meta;";
$wpdb->query($sql);
?>