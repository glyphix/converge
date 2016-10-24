<?php

function wonderplugin_audio_css_allow($allowed_attr) {

	if ( !is_array($allowed_attr) ) {
		$allowed_attr = array();
	}
	
	array_push($allowed_attr, 'position', 'top', 'left', 'bottom', 'right');

	return $allowed_attr;
}

function wonderplugin_audio_tags_allow( $allowedposttags ) {
	
	if ( isset($allowedposttags['a']) && is_array($allowedposttags['a']) )
		$allowedposttags['a']['download'] = true;
	
	return $allowedposttags;
}