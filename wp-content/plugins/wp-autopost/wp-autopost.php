<?php
/*
Plugin Name: WP-AutoPost
Plugin URI: http://wp-autopost.org
Description: WP-AutoPost Plugin can automatically post content from any other site. It is very simple to use, without complicated setting, and powerful enough.
Version: 2.9.7
Author: WP-AutoPost.ORG
Author URI: http://wp-autopost.org
*/

define('WPAP_PATH',WP_PLUGIN_DIR.'/wp-autopost');

global  $wp_autopost_root,$table_prefix,$t_f_ap_cnofig,$t_f_ap_updated_record,$t_f_ap_config_ur1_1ist,$t_f_ap_log,$t_autolink;
$wp_autopost_root = WP_PLUGIN_URL."/wp-autopost/";
$t_f_ap_cnofig = $table_prefix.'autopost_task';
$t_f_ap_updated_record = $table_prefix.'autopost_record';
$t_f_ap_config_ur1_1ist = $table_prefix.'autopost_task_urllist';
$t_f_ap_log = $table_prefix.'autopost_log';
$t_autolink = $table_prefix.'autolink';

global $wp_autopost_db_version;
$wp_autopost_db_version='2.9.7';


load_plugin_textdomain('wp-autopost', WP_PLUGIN_URL.'/wp-autopost/languages/', 'wp-autopost/languages/');


### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

### Function: Ratings Administration Menu
add_action('admin_menu', 'wp_autopost_menu');
function wp_autopost_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page('Auto Post','Auto Post', 'administrator', 'wp-autopost/wp-autopost-tasklist.php', '',WP_PLUGIN_URL.'/wp-autopost/images/menu_icon.png');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Posts'), __('Posts'),  'administrator', 'wp-autopost/wp-autopost-updatedpost.php');
		
		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Auto Link','wp-autopost'), __('Auto Link','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-link.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Options','wp-autopost'), __('Options','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-options.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Watermark Options','wp-autopost'), __('Watermark Options','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-watermark.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Microsoft Translator','wp-autopost'), __('Microsoft Translator','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-translator.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Proxy','wp-autopost'), __('Proxy','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-proxy.php');
		
		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Flickr','wp-autopost'), __('Flickr','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-flickr.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Qiniu','wp-autopost'), __('Qiniu','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-qiniu.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Upyun','wp-autopost'), __('Upyun','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-upyun.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Logs','wp-autopost'), __('Logs','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-logs.php');

		add_submenu_page('wp-autopost/wp-autopost-tasklist.php', __('Documentation','wp-autopost'), __('Documentation','wp-autopost'),  'administrator', 'wp-autopost/wp-autopost-documentation.php');
	
	}
}



function wp_autopost_install () {
  add_option('wp_autopost_updateMethod', '0');
  add_option('wp_autopost_timeLimit', '0');
  add_option('wp_autopost_downImgMinWidth', '100');
  add_option('wp_autopost_downImgTimeOut', '120');
  add_option('wp_autopost_downImgFailsNotPost', '0');
  add_option('wp_autopost_delComment', '1');
  add_option('wp_autopost_delAttrId', '1');
  add_option('wp_autopost_delAttrClass', '1');
  add_option('wp_autopost_delAttrStyle', '0');

  global $wpdb,$wp_autopost_db_version; 
  global $t_f_ap_cnofig,$t_f_ap_config_ur1_1ist,$t_f_ap_updated_record,$t_f_ap_log,$t_autolink;
  $old_db_version = get_option('wp_autopost_free_db_version');

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  
  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_cnofig'") != $t_f_ap_cnofig)||$wp_autopost_db_version!=$old_db_version){
    $sql = "CREATE TABLE " . $t_f_ap_cnofig . " (
    id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	m_extract TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	activation TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	name CHAR(200) NOT NULL COLLATE 'utf8_unicode_ci',
	page_charset CHAR(30) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	a_match_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	title_match_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    content_match_type VARCHAR(300) NOT NULL DEFAULT '0',
	a_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	title_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	content_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	source_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	start_num SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	end_num SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	updated_num MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	cat VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	author SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
	update_interval SMALLINT(5) UNSIGNED NOT NULL DEFAULT '60',
	published_interval SMALLINT(5) UNSIGNED NOT NULL DEFAULT '60',
	post_scheduled VARCHAR(20)  NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	post_scheduled_last_time INT(10) UNSIGNED NOT NULL DEFAULT '0',
	last_update_time INT(10) UNSIGNED NOT NULL DEFAULT '0',
	post_id INT(10) UNSIGNED NOT NULL DEFAULT '0',
	last_error INT(10) UNSIGNED NOT NULL DEFAULT '0',
	is_running TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	reverse_sort TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	auto_tags CHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
    proxy  CHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	post_type VARCHAR(50) NULL DEFAULT 'post' COLLATE 'utf8_unicode_ci',
	post_format VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	check_duplicate TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	err_status TINYINT(3) NOT NULL DEFAULT '1',
	 PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_config_ur1_1ist'") != $t_f_ap_config_ur1_1ist)||$wp_autopost_db_version!=$old_db_version){
    $sql = "CREATE TABLE " . $t_f_ap_config_ur1_1ist . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	url CHAR(255) NOT NULL,
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_updated_record'") != $t_f_ap_updated_record)||$wp_autopost_db_version!=$old_db_version){
    $sql = "CREATE TABLE " . $t_f_ap_updated_record . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id SMALLINT(5) UNSIGNED NOT NULL,
	url VARCHAR(1000) NOT NULL COLLATE 'utf8_unicode_ci',
	title VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	post_id INT(10) UNSIGNED NOT NULL,
	date_time INT(10) UNSIGNED NOT NULL,
	url_status TINYINT(3) NOT NULL DEFAULT '1',
	PRIMARY KEY (id),
	INDEX url (url),
	INDEX title (title)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_log'") != $t_f_ap_log)||$wp_autopost_db_version!=$old_db_version){
    $sql = "CREATE TABLE " . $t_f_ap_log . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id INT(10) UNSIGNED NULL DEFAULT NULL,
	date_time INT(10) UNSIGNED NULL DEFAULT NULL,
	info VARCHAR(255) NULL DEFAULT NULL,
	url VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_autolink'") != $t_autolink)||$wp_autopost_db_version!=$old_db_version){
    $sql = "CREATE TABLE " . $t_autolink . " (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	keyword VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',  
	details VARCHAR(200) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if($wp_autopost_db_version!=$old_db_version){
    $MicroTransOptions = get_option('wp-autopost-micro-trans-options');
	if($MicroTransOptions!=null){
       $array = array();
       $newArray = array();
       $newArray['clientID']=$MicroTransOptions['clientID'];
       $newArray['clientSecret']=$MicroTransOptions['clientSecret'];
       $array[] = $newArray;
       update_option('wp-autopost-micro-trans-options',$array);
	}
  }

  update_option("wp_autopost_free_db_version", $wp_autopost_db_version);
  
  //watermark
  $upload_image = dirname(__FILE__).'/watermark/uploads/watermark.png';
  $upload_image_url = plugins_url('/watermark/uploads/watermark.png', __FILE__ );
  
  $options = array(
	  'type' => 0,
	  'position' => 9,
	  'font' => '',
	  'text' => get_bloginfo('url'),
	  'size' => 16,
	  'color' => '#ffffff',
	  'x-adjustment' => 0,
	  'y-adjustment' => 0,
	  'transparency' => 80,
	  'upload_image' => $upload_image,
	  'upload_image_url' => $upload_image_url,
	  'min_width' => 300,
	  'min_height' => 300,
	  'jpeg_quality' => 90
   );
   add_option( 'wp-watermark-options', $options );

   $flickrOptions = array(
	  'api_key' => 'fc1ec013e1bfb8f17b952a89efbe355e',
	  'api_secret' => 'bbba8595664cfd10',
	  'oauth_token' => '',
	  'oauth_token_secret' => '',
	  'user_id' => '',
	  'flickr_set'=>'',
	  'is_public' => 0
   );
   add_option( 'wp-autopost-flickr-options', $flickrOptions );

   $qiniuOptions = array(
	  'domain' => '',
	  'bucket' => '',
	  'access_key' => '',
	  'secret_key' => ''
   );
   add_option( 'wp-autopost-qiniu-options', $flickrOptions );

}
register_activation_hook( __FILE__,'wp_autopost_install');

include WPAP_PATH.'/wp-autopost-function.php';

?>