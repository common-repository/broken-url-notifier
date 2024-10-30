<?php
/**
 * Plugin Name:       broken url notifier
 * Plugin URI:        https://wordpress.org/plugins/broken-url-notifier/
 * Description:       Sample Plugin For WooCommerce
 * Version:           1.0
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       broken-url-notifier
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: https://github.com/technofreaky/broken-url-notifier
 */

if ( ! defined( 'WPINC' ) ) { die; }
 

require_once(plugin_dir_path(__FILE__).'broken-url-notifier-loader.php');


if(!function_exists('Broken_Url_Notifier')){
	function Broken_Url_Notifier(){
		return Broken_Url_Notifier::get_instance();
	}
	
}
Broken_Url_Notifier();

require_once(BUN_INC.'class-activation.php');
$default_args = array(
'dbslug' => BUN_DB,
'welcome_slug' => BUN_SLUG.'-welcome-page',
'wp_plugin_slug' => BUN_SLUG,
'wp_plugin_url' => 'https://wordpress.org/plugins/broken-url-notifier/',
'tweet_text' => 'Get notified when there is some broken url or image in your website.',
'twitter_user' => 'varunsridharan2',
'twitter_hash' => 'brokenurlnotifier',
'gitub_user' => 'technofreaky',
'github_repo' => 'broken-url-notifier',
'plugin_name' => BUN_NAME,
'version' => BUN_V,
'template' => BUN_INC.'welcome-page-template.php',
'menu_name' => 'Welcome'.BUN_NAME,
'plugin_file' => __FILE__,
);

new broken_url_notifier_activation($default_args);

?>