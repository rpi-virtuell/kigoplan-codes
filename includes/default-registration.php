<?php
/**
 * Add the invite only form element to the WordPress default registration
 *
 * @since  0.1
 *
 * return html
 */
function kigoplan_register_form() {

	// Check if default registration integration is enabled
	if ( ! kigoplan_is_default_registration() ) {
		return;
	}

	// Check if the invite code is coming from a link
	$tk_invite_code = ( ! empty( $_GET['code'] ) ) ? sanitize_key( trim( $_GET['code'] ) ) : '';

	?>
    <p>
        <label for="tk_invite_code"><?php _e( 'Registrierungsschlüssel', 'kigoplan-code' ) ?><br/>
            <input type="text" name="tk_invite_code" id="tk_invite_code" class="input"
                   value="<?php echo esc_attr( $tk_invite_code ); ?>" size="25"/></label>
    </p>
	<?php

}
add_action( 'register_form', 'kigoplan_register_form' );

function check_kigoplan_shortlinks() {
	if ( is_404() ) {
		$uri = $_SERVER[ 'REQUEST_URI' ];
		if ( strpos( $uri, '/k/') !== false ) {
			$temp = (int) substr( $uri, 3 );
			wp_redirect( wp_registration_url(). "&code=" . $temp);
			exit;

		}
		if ( strpos( $uri, '/registrieren') !== false ) {
			wp_redirect( wp_registration_url());
			exit;

		}
	}
}
add_filter('template_redirect', 'check_kigoplan_shortlinks' );

/**
 * Validate the registration form element
 *
 * @since  0.1
 *
 * return object
 */
function kigoplan_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	// Check if default registration integration is enabled
	if ( ! kigoplan_is_default_registration() ) {
		$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'Entschuldigung!', 'kigoplan-code' ), __( 'Die Registrierung für den Kigoplan wurde deaktiviert. Versuche es bitte später noch einmal.', 'kigoplan-code' ) ) );
		return $errors;
	}
	// Check if the field has a code
	if ( empty( $_POST['tk_invite_code'] ) || ! empty( $_POST['tk_invite_code'] ) && trim( $_POST['tk_invite_code'] ) == '' ) {
		$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'kigoplan-code' ), __( 'You must include a Kigoplan Code.', 'kigoplan-code' ) ) );
	} else {

		$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

		// Validate the code
		if ( !kigoplan_validate_code( $tk_invite_code ) ) {
			$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'kigoplan-code' ), __('Ungültiger Registrierungsschlüssel', 'kigoplan-code')  ) );
		}

	}

	return $errors;
}
add_filter( 'registration_errors', 'kigoplan_registration_errors', 10, 3 );

function kigo_code_registration_registerform(){

    // Check if default registration integration is enabled
	if ( ! kigoplan_is_default_registration() ) {
		echo __( 'Die Registrierung für den Kigoplan wurde deaktiviert.<br> Versuche es bitte später noch einmal.', 'kigoplan-code' );
		echo '<hr><div><a href="?">';
		echo __( 'Login' );
		echo '</a></div>';
		wp_die();

	}

}

add_action( 'login_form_register', 'kigo_code_registration_registerform' );

function kigo_code_registration_data($data, $update){
	$errors = new WP_Error();

    if(!$update && kigoplan_is_default_registration() && !current_user_can('create_users')){
	    $tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

	    // Validate the code
	    if ( !kigoplan_validate_code( $tk_invite_code ) ) {
		    $errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'kigoplan-code' ), 'Ungültiger oder fehlender Registrierungsschlüssel' ) );
		    add_action('admin_notices', function(){
			    $class = 'notice notice-error';
			    $message = __( 'Solange Kigoplan Validation auf aktiv gesetzt ist, können auch im Backend keine neuen benutzer hinzugefügt werden. ', 'kigoplan-code' );

			    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		    });

		    return $errors;
	    }
    }
    return $data;
}
add_filter( 'wp_pre_insert_user_data', 'kigo_code_registration_data', 10, 3 );


function delete_used_kigoplan_code($meta, $user, $update){
	if( !$update && isset($_POST['tk_invite_code']  )   ) {
		$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

		if($post = get_page_by_title($tk_invite_code,'OBJECT','kigoplan_codes')){
			$meta['kigoplan-year']=$post->post_password;

			$licencetime = (24 * 60 * 60 * 365 *3); //3 Jears
			$starttime = strtotime(substr($post->post_password,0,4).'-01-01');
			$endtime = $starttime + $licencetime;
			$meta['kigoplan-validtime'] = $endtime;
			$meta['kigoplan-code']=$post->post_name;
		}

	}
	wp_delete_post($post->ID);
	return $meta;
}

add_filter('insert_user_meta', 'delete_used_kigoplan_code',1,3);

