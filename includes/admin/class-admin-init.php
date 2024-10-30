<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Broken_Url_Notifier_Admin extends Broken_Url_Notifier {
	
    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),1099);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ));
		add_action( 'admin_menu',  array( $this,'plugin_menu') );
		add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
	}

	
	public function plugin_menu(){
		add_menu_page(
			__('Broken Url Notifier',BUN_TXT), 
			__('Broken Url Notifier',BUN_TXT), 
			'administrator','broken-url-notifier',array($this,'settings_page'), BUN_IMG.'icon.png');
		$this->settings_page_hook = add_submenu_page('broken-url-notifier', 
						 __('Settings',BUN_TXT), 
						 __('Settings',BUN_TXT), 
						 'administrator', 'broken-url-notifier', array($this,'settings_page') );
		$this->report_page_hook = add_submenu_page( 'broken-url-notifier', 
						 __('Report',BUN_TXT), 
						 __('Report',BUN_TXT), 
						 'administrator', 'broken-url-notifier-reports', array($this,'bun_reports') ); 
	}
	
	
	public function settings_page(){ 
		wp_enqueue_media();
		broken_url_notifier()->settings()->admin_page();
	}
	
	public function bun_reports(){
		tt_render_list_page();
	}
	
	
    /**
     * Inits Admin Sttings
     */
    public function admin_init(){
		
    }
 
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() { 
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_style(BUN_SLUG.'_core_style',BUN_CSS.'admin-style.css', array(), $this->version, 'all' );  
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_script(BUN_SLUG.'_core_script',BUN_JS.'admin-script.js', array('jquery'), $this->version, false ); 
        }
 
	}
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
		$screen_ids[] = $this->settings_page_hook;
		$screen_ids[] = $this->report_page_hook;
        return $screen_ids;
    }
    
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( BUN_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('Settings',BUN_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('F.A.Q',BUN_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('View On Github',BUN_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('Report Issue',BUN_TXT) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', '#', __('Donate',BUN_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',BUN_TXT) );
		}
		return $plugin_meta;
	}	    
}

?>