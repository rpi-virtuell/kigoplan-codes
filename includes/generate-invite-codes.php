<?php

// Coming Soon
//
// 1. Options to generate new invite codes for existing users
// 2. Bulk create invite codes


function kigoplan_generate_codes(  $total_codes, $pass ) {

	$total_codes = intval($total_codes);

	if ( $total_codes < 1 ) {
		return;
	}


	for ( $i = 1; $i <= $total_codes; $i ++ ) {


		$code = wp_generate_password(24, false, false);



		$args        = array(
			'post_type'   => 'kigoplan_codes',
			'post_author' => get_current_user_id(),
			'post_parent' => 0,
			'post_status' => 'publish',
			'post_title'  => $code,
			'post_name'  => $code,
			'post_password' => $pass,
			'comment_status'  => 'closed',
			'ping_status'  => 'closed',
		);
		$new_code_id = wp_insert_post( $args );



	}

}


function generate_kigoplan_codes() {

	if (! (is_array($_POST) && defined('DOING_AJAX') && DOING_AJAX)) {
		wp_die();
	}

	if ( ! isset($_POST['action']) || wp_verify_nonce($_POST['nonce'], 'generate_kigoplan_codes_nonce') === false ) {
		wp_die();
	}

	if ( !isset( $_POST['total'] )) {
		wp_die();
	}

	if ( !isset( $_POST['year'] )) {
		$year = '2021';
	}else{
		$year = sanitize_text_field( $_POST['year']);
	}

	$total = intval( $_POST['total'] );

	kigoplan_generate_codes($total, $year);

	$json['success'] = 'true';

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_generate_kigoplan_codes', 'generate_kigoplan_codes' );