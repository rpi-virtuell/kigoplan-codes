<?php

/**
 * Add the Settings Page to the All in One Invite Codes Menu
 */
function kigoplan_settings_menu() {
	add_submenu_page( "options-general.php", __( 'Kigoplan Settings', 'kigoplan' ), __( 'Kigoplan', 'kigoplan' ), 'manage_options', 'kigoplan_settings', 'kigoplan_settings_page' );
}

add_action( 'admin_menu', 'kigoplan_settings_menu' );

/**
 * Settings Page Content
 */
function kigoplan_settings_page() { ?>

    <div id="post" class="wrap">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

                <div id="postbox-container-1" class="postbox-container">
					<?php kigoplan_settings_page_sidebar(); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
					<?php kigoplan_settings_page_tabs_content(); ?>
                </div>
            </div>
        </div>

    </div> <!-- .wrap -->
	<?php
}

/**
 * Settings Tabs Navigation
 *
 * @param string $current
 */
function kigoplan_admin_tabs( $current = 'general' ) {
	$tabs = array( 'general' => 'Kigoplan Einstellungen' );

	$tabs         = apply_filters( 'kigoplan_admin_tabs', $tabs );
	$tabs['generate'] = 'Schlüssel erzeugen';
	$tabs['export'] = 'Export';


	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='?page=kigoplan_settings&tab=$tab'>$name</a>";
	}
	echo '</h2>';
}

/**
 * Register Settings Options
 *
 */
function kigoplan_register_option() {

	// General Settings
	register_setting( 'kigoplan_general', 'kigoplan_general', 'kigoplan_default_sanitize' );

	// Mail Templates
	register_setting( 'kigoplan_export_templates', 'kigoplan_export_templates', 'kigoplan_default_sanitize' );

}

add_action( 'admin_init', 'kigoplan_register_option' );

/**
 * @param $new
 *
 * @return mixed
 */
function kigoplan_default_sanitize( $new ) {
	return $new;
}

/**
 *
 * Tabs content with the options
 *
 */
function kigoplan_settings_page_tabs_content() {
	global $pagenow, $kigoplan; ?>
    <div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>All in One Invite Codes...</p></div>';
		}

		if ( isset ( $_GET['tab'] ) ) {
			kigoplan_admin_tabs( $_GET['tab'] );
		} else {
			kigoplan_admin_tabs( 'general' );
		}

		if ( $pagenow == 'options-general.php' && $_GET['page'] == 'kigoplan_settings' ) {

			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'general';
			}

			switch ( $tab ) {
				case 'general' :
					$kigoplan_general = get_option( 'kigoplan_general' ); ?>
                    <div class="metabox-holder">
                        <div class="postbox kigoplan-metabox">
                            <div class="inside">
                                <form method="post" action="options.php">

									<?php settings_fields( 'kigoplan_general' ); ?>


                                    <table class="form-table">
                                        <tbody>

                                        <!-- Registration Settings -->
                                        <tr>
                                            <th colspan="2">
                                                <h3>
                                                    <span><?php _e( 'Allgemein', 'kigoplan' ); ?></span>
                                                </h3>
	                                            <p><?php _e( 'Wenn Schlüsselregistrierung aktiv ist, fügt das Plugin ein Eingabefeld für Registrierungschlüssel zum Registrierungsformular hinzu', 'kigoplan-codes' ); ?></p>
                                            </th>
                                        </tr>

                                        <tr valign="top">
                                            <th scope="row" valign="top">
												<?php _e( 'Schlüsselregistrierung', 'kigoplan-codes' ); ?>
                                            </th>
                                            <td>
												<?php
												$pages['enabled'] = 'Aktiv';
												$pages['disable'] = 'Inaktiv';

												if ( isset( $pages ) && is_array( $pages ) ) {
													echo '<select name="kigoplan_general[default_registration]" id="kigoplan_general">';

													foreach ( $pages as $page_id => $page_name ) {
														echo '<option ' . selected( $kigoplan_general['default_registration'], $page_id ) . 'value="' . $page_id . '">' . $page_name . '</option>';
													}
													echo '</select>';
												}
												?>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>

									<?php submit_button(); ?>

                                </form>



                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                    </div><!-- .metabox-holder -->
					<?php
					break;
				case 'generate' :
					?>
					<div class="metabox-holder">
						<div class="postbox kigoplan-metabox">
							<div class="inside">
								<table class="form-table">
									<tbody>

										<!-- Registration Settings -->
										<tr>
											<th colspan="2">
												<h3>
													<span><?php _e( 'Registrierungschlüssel generieren', 'kigoplan' ); ?></span>
												</h3>
												<p><?php _e( 'Wieviel neue Registrierungschlüssel sollen generiert werden?', 'kigoplan-codes' ); ?></p>
											</th>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php _e( 'Anzahl', 'kigoplan-codes' ); ?>

											</th>
											<td>

												<input type="number" id="kigoplan_general_generate_codes_amount" value="0">


											</td>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php _e( 'Ab dem Jahr', 'kigoplan-codes' ); ?>

											</th>
											<td>

												<input type="text" id="kigoplan_general_generate_codes_year" value="2021">


											</td>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">


											</th>
											<td>

												<button class="button-primary button" id="generate_kigoplan_codes">Schlüssel jetzt erstellen</button>

											</td>
										</tr>
									</tbody>
								</table>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->

					<?php
					break;
				case 'export' :


					$kigoplan_export_templates = get_option( 'kigoplan_export_templates' );



                    ?>
                    <div class="metabox-holder">
                        <div class="postbox kigoplan-metabox">

                            <div class="inside">
	                            <div style="width:70%;float:left;">
		                            <h4>Schlüssel</h4>
		                            <?php print_kigoplan_codes();?>
	                            </div>
	                            <div style="width:20%;min-width: 100px;float:right;">
		                            <h4>Jahrgänge</h4>
	                                <?php print_kigoplan_code_years(); ?>
	                            </div>
	                            <div style="clear: both; width: 100%"></div>
                            </div><!-- .inside -->
                        </div><!-- .postbox -->
                    </div><!-- .metabox-holder -->
					<?php
					break;

				default:
					do_action( 'kigoplan_settings_page_tab', $tab );
					break;
			}
		}
		?>
    </div> <!-- #poststuff -->
	<?php
}

function kigoplan_settings_page_sidebar() {
	echo '<p>Plugin Autor: <a href="mailto:happel@comenius.de">Joachim Happel</a></p>';
}