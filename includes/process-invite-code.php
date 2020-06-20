<?php

/**
 * Validate and process the code.
 *
 * @since  0.1
 * @return text
 */
function kigoplan_validate_code( $code ) {

	$post = get_page_by_title($code,'OBJECT','kigoplan_codes');
	if($post->ID>0){
		return true;
	}
	return false;
	//return  post_exists($code,'','','kigoplan_codes');

}
