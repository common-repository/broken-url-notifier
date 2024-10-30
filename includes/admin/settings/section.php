<?php
global $section;

$section['settings_general'][] = array(
	'id'=>'general',
	'title'=>'', 
	'validate_callback' =>array( $this, 'validate_section' )
);

$section['settings_logging_notification'][] = array(
    'id'=>'logging_notification',
    'title'=>'', 
    'desc' => '',
    'validate_callback'=>array( $this, 'validate_section' ),
);