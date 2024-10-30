<?php
global $fields;

$selected_img = Broken_Url_Notifier()->get_option(BUN_DB.'default_error_image');
if($selected_img == BUN_IMG.'img1.jpg' || $selected_img == BUN_IMG.'img2.jpg'){
	$selected_img = '';
}

$error_image = array(
		BUN_IMG.'img1.jpg' => '<img class="settings_view_image" src="'.BUN_IMG.'img1.jpg" />',
		BUN_IMG.'img2.jpg' => '<img class="settings_view_image" src="'.BUN_IMG.'img2.jpg" />',
		$selected_img      => '<img class="settings_view_image" src="'.$selected_img.'"/> <button id="upload_image_button" type="button" class="button button-secodary" >Upload or Choose Image </button>',
	);
	
/** General Settings **/
$fields['settings_general']['general'][] = array(
    'id'      =>  BUN_DB.'notification_email',
    'type'    => 'text',
    'label'   => __( 'Email Notification To :', BUN_TXT),
	'desc'    => __( 'Enter Ids By <code>,</code> for multiple',BUN_TXT),
);


/** General Settings **/
$fields['settings_general']['general'][] = array(
    'id'      =>  BUN_DB.'notification_count_hold',
    'type'    => 'text',
    'label'   => __( 'Notification Every :', BUN_TXT),
	'attr'  => array('style' => 'width:10%;'),
	'desc'    => __( 'Get notification for every <code>x</code> count of hits ',BUN_TXT),
);

$fields['settings_general']['general'][] =  			array(
	'id'      => BUN_DB.'default_error_image', 
	'type'    => 'radio', //  required
	'label'   => __( '404 Default Image', BUN_TXT ),
	'desc'    => __( '404 Default Image', BUN_TXT ), 
	'options' => $error_image,
	'class' => 'default_error_img_list'
);
 
$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => '<h2 class="settings_title" >Notification</h2>',
	'id'      => 'content_field', // required
	'type'    => 'content', // required 
);

$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => __( 'Broken Image', BUN_TXT),
	'desc'  => '<p class="description">'.__( 'Get Email When Broken Image Found In Your Website',BUN_TXT).'</p>',
	'id'    =>   BUN_DB.'broke_image_notification',
	'type'  => 'checkbox',
	'default' => 'yes',
);

$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => __( 'Broken Page (404)', BUN_TXT),
	'desc'  => '<p class="description">'.__( 'Get Email When Broken Image Found In Your Website',BUN_TXT).'</p>',
	'id'    =>   BUN_DB.'broke_page_notification',
	'type'  => 'checkbox',
	'default' => 'yes',
);
 

$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => '<hr/>',
	'id'      => 'content_field_1', // required
	'type'    => 'content', // required
	'content' => '<hr/>'
);
  
$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => '<h2 class="settings_title" >Logging</h2>',
	'id'      => 'content_field_2', // required
	'type'    => 'content', // required 
);

$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => __( 'Broken Image', BUN_TXT),
	'desc'  => '<p class="description">'.__( 'Save Log When Broken Image Found In Your Website',BUN_TXT).'</p>',
	'id'    =>   BUN_DB.'broke_image_log',
	'type'  => 'checkbox',
	'default' => 'yes',
);

$fields['settings_logging_notification']['logging_notification'][] =  array(
	'label' => __( 'Broken Page (404)', BUN_TXT),
	'desc'  => '<p class="description">'.__( 'Save Log When Broken Image Found In Your Website',BUN_TXT).'</p>',
	'id'    =>   BUN_DB.'broke_page_log',
	'type'  => 'checkbox',
	'default' => 'yes',
);