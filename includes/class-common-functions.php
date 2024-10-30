<?php
/**
 * functionality of the plugin.
 *
 * @link       @TODO
 * @since      1.0
 *
 * @package    @TODO
 * @subpackage @TODO
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Broken_Url_Notifier_Functions  {
	public $img_log = '';
	public $default_img = '';
	public $img_email_sub = '';
	public $img_email_content = '';
	public $img_notice = '';

	public $page_log = '';
	public $page_email_sub = '';
	public $page_email_content = '';
	public $page_notice = '';
	
	public $email_limit = '';
	public $notice_email = '';
	
	public function __construct() {
		add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
		add_action('wp_head',array($this,'log_page_404'),1); 
		add_action('wp_footer', array($this,'add_footer_image_script'),200);
		
		add_action( 'wp_ajax_delete_log', array($this,'delete_log' ));
		
		add_action( 'wp_ajax_bun_404_page_notice', array($this,'send_page_notice' ));
		add_action( 'wp_ajax_nopriv_bun_404_page_notice', array($this,'send_page_notice' ));
		
		add_action( 'wp_ajax_bun_log_img', array($this,'send_log_image' ));
		add_action( 'wp_ajax_nopriv_bun_log_img', array($this,'send_log_image' ));
		
		
	}

	
	public function after_plugins_loaded(){
		$this->notice_email = broken_url_notifier()->get_option(BUN_DB.'notification_email');
		$this->email_limit = broken_url_notifier()->get_option(BUN_DB.'notification_count_hold');
		$this->default_img = broken_url_notifier()->get_option(BUN_DB.'default_error_image');
		$this->img_notice = broken_url_notifier()->get_option(BUN_DB.'broke_image_notification');
		$this->page_notice = broken_url_notifier()->get_option(BUN_DB.'broke_page_notification');
		$this->img_log = broken_url_notifier()->get_option(BUN_DB.'broke_image_log');
		$this->page_log = broken_url_notifier()->get_option(BUN_DB.'broke_page_log');
		if(empty($this->email_limit)){$this->email_limit = 10;}
		if(empty($this->notice_email)){$this->notice_email = get_option('admin_email');}
	}
	
	public function log_page_404(){
		if(is_404()){
			$arrayKey = MD5($_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_URI']);
			$data = array();
			$data['type'] = 'page';
			$data['url'] =  "http://".$_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_URI'];
			$data['page'] = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
			$data['hits'] = 1;	
			
			if($this->page_log == true){ $data['hits'] = $this->add_log($arrayKey,$data); }
			
			if($this->page_notice == true){
				$send = false;
				if($data['hits'] == '1'){ $send = true; } 
				else if($data['hits'] % $this->email_limit == 0){ $send = true; }		
				if($send){add_action('wp_footer', array($this,'add_footer_page_script'),200);}
			}
		}
	}
	
	
	public function send_log_image(){
		$arrayKey = MD5($_REQUEST['image']);
		$data = array();
		$data['type'] = 'image';
		$data['url'] = $_REQUEST['image'];
		$data['page'] = $_REQUEST['page'];
		$data['hits'] = 1;

		if($this->img_log == true){ $data['hits'] = $this->add_log($arrayKey,$data); }
		
		if($this->img_notice == true){
			$send = false;
			if($data['hits'] == '1'){ $send = true; } 
			else if($data['hits'] % $this->email_limit == 0){ $send = true; }		
			if($send){
				$bun_content = "Your website (".site_url().") is signaling a broken image! <br/><br/>
					<strong>Broken Image Url:</strong>  ".stripslashes($_REQUEST['image'])." <br/>
					<strong>Referenced on Page:</strong> <a href='".stripslashes($_REQUEST['page'])."'>
					".stripslashes($_REQUEST['page'])."</a>";
				$this->send_mail('Found 404 Image',$bun_content);
			}
		}
		
		exit;
	}
	
	
	
	public function send_page_notice(){
		//$ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
		$ref = isset($_REQUEST['refpage']) ? $_REQUEST['refpage'] : '';
		$bun_content = "Your website (".site_url().") is signaling a broken link! <br/><br/>
		<strong>Referenced on Page:</strong> <a href='http://".$_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_URI']."'> 	".$_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_URI']."</a> <br/> 
                            <strong>Refered Page :</strong>  ".$ref." <br/> ";
		$this->send_mail('Found 404 Page',$bun_content);
		
		exit;
	}
	

	
	public function add_log($key = '', $array){
		$existing_log = get_option(BUN_DB.'reports');
		if(is_string($existing_log)){$existing_log = json_decode($existing_log,true);}

		if(isset($existing_log[$key])){
			$existing_log[$key]['hits'] =  $existing_log[$key]['hits'] + 1;
		} else {
			$existing_log[$key] = $array;
		}
		update_option(BUN_DB.'reports',$existing_log);
		return $existing_log[$key]['hits'];
	}
	
	public function delete_log(){
		if(isset($_REQUEST['key'])){
			$existing_log = get_option(BUN_DB.'reports');
			if(is_string($existing_log)){$existing_log = json_decode($existing_log,true);}
			$key = $_REQUEST['key'];
			if(isset($existing_log[$key])){
				unset($existing_log[$key]);
				update_option(BUN_DB.'reports',$existing_log);
				echo 'done';
			}  
		}
		
		exit;
	}
	
	public function send_mail($subject,$content){
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($this->notice_email, $subject, $content,$headers);
	}
	
	
	public function add_footer_page_script(){
		$ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
		echo "<script>
		jQuery(document).ready(function(){  
				jQuery.post('".admin_url('admin-ajax.php')."', {
					action : 'bun_404_page_notice', 
					refpage : '".$ref."' ,
					page: window.location.href
				}, function() {
				}); 
		});
		</script>";
	}
	public function add_footer_image_script(){ 
		echo "<script>
				jQuery(document).ready(function(){
					jQuery('img').error(function() {
        				var oldImg = jQuery(this).attr('src');
						jQuery.post('".admin_url('admin-ajax.php')."', {
       						action : 'bun_log_img',
							imageError:'yes',
							image: oldImg,
							page: window.location.href
						}, function() {
						});
						//jQuery(this).attr('src', '".$this->default_img."');
					});
				});
       			</script>";	 	
	}	
}
