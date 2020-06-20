<?php

/**
 * Plugin Name: Kigoplan Codes
 * Plugin URI:  https://themekraft.com/kigoplan-codes/
 * Description: Erstellt Registreirungsschlüssel für die Kigoplan Seite.
 * Version: 100.0.5
 * Author: Joachim Happel
 * Author URI: https://comenius.de/
 * Licence: GPLv3
 * Network: false
 * Text Domain: kigoplan-codes
 *
 * ****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 ****************************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'KigoplanCodes' ) ) {
	/**
	 * Class KigoplanCodes
	 */
	class KigoplanCodes {

		/**
		 * @var string
		 */
		public $version = '100.0.5';

		/**
		 * @var string Assets URL
		 */
		public static $assets;

		/**
		 * Initiate the class
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		public function __construct() {

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

			$this->load_constants();

			add_action( 'init', array( $this, 'init_hook' ), 1, 1 );
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 102, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ), 102, 1 );


			add_action( 'wp_enqueue_scripts', array( $this, 'front_js_loader' ), 102, 1 );

			add_filter( 'render_block',  array( $this, 'user_can_read_fullcontent' ), 999, 2 );

			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
		}

		/**
		 * Defines constants needed throughout KigoplanCodes
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		public function load_constants() {

			/**
			 * Define the plugin version
			 */
			define( 'TK_ALL_IN_ONE_INVITE_CODES_VERSION', $this->version );

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL' ) ) {
				/**
				 * Define the plugin url
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH' ) ) {
				/**
				 * Define the install path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH', dirname( __FILE__ ) . '/' );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH' ) ) {
				/**
				 * Define the include path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH', TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH . 'includes/' );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_TEMPLATE_PATH' ) ) {
				/**
				 * Define the template path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_TEMPLATE_PATH', TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH . 'templates/' );
			}

		}

		/**
		 * Defines kigoplan_init action
		 *
		 * This action fires on WP's init action and provides a way for the rest of WP,
		 * as well as other dependent plugins, to hook into the loading process in an
		 * orderly fashion.
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		public function init_hook() {
			$this->set_globals();
			do_action( 'kigoplan_init' );
		}

		/**
		 * Setup all globals
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		static function set_globals() {
			global $kigoplan;

			/*
			 * Get KigoplanCodes options
			 *
			 * @filter: kigoplan_set_globals
			 *
			 */
			$kigoplan = apply_filters( 'tk_kigoplan_set_globals', get_option( 'tk_kigoplan_options' ) );

			return $kigoplan;
		}

		/**
		 * Include files needed by KigoplanCodes
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		public function includes() {

			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'functions.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'default-registration.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'process-invite-code.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'generate-invite-codes.php' );

			if ( is_admin() ) {
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/admin-settings.php' );
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/invite-codes-post-type.php' );
			}
		}

		/**
		 * Load the textdomain for the plugin
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'kigoplan', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Enqueue the needed CSS for the admin screen
		 *
		 * @package kigoplan
		 * @since  0.1
		 *
		 * @param $hook_suffix
		 */
		function admin_styles( $hook_suffix ) {

		}

		/**
		 * Enqueue the needed JS for the admin screen
		 *
		 * @package kigoplan
		 * @since  0.1
		 *
		 * @param $hook_suffix
		 */
		function admin_js( $hook_suffix ) {

            wp_register_script( 'kigoplan_codes-admin-js', plugins_url( 'assets/admin/js/admin.js', __FILE__ ), array(), $this->version );
            wp_enqueue_script( 'kigoplan_codes-admin-js' );

            wp_localize_script(
            	'kigoplan_codes-admin-js',
	            'generateKigoplanCodesAdminJs',
	            array( 'nonce' => wp_create_nonce('generate_kigoplan_codes_nonce') )
            );

		}

		/**
		 * Check if a kigoplan view is displayed and load the needed styles and scripts
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		function front_js_loader() {
			/*
			wp_register_script( 'kigoplan_codes-front-js', plugins_url( 'assets/js/front.js', __FILE__ ), array('jquery'), $this->version );
			wp_enqueue_script( 'kigoplan_codes-front-js' );
			wp_localize_script('kigoplan_codes-front-js', 'kigoplanCodesFrontJs', array( 'nonce' => wp_create_nonce('kigoplan_code_nonce') ) );
			*/
		}

		/**
		 * Enqueue the needed JS for the form in the frontend
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		function front_js_css() {

		}

		/**
		 * Update form 1.x version
		 *
		 * @package kigoplan
		 * @since  0.1
		 */
		function update_db_check() {

			if ( ! is_admin() ) {
				return;
			}

		}
        function user_can_read_fullcontent($block_content, $block){

		    if( is_user_logged_in() && !current_user_can('manage_options') && (isset($block['attrs']['editorskit']['loggedin'])   || isset($block['attrs']['editorskit']['loggedout'] ) )){



			    $endtime = get_user_meta(get_current_user_id(),'kigoplan-validtime',true);
			    $post = get_post();
			    $valid = strtotime( $post->post_date ) < $endtime;
                $show = ($block['attrs']['editorskit']['loggedin'] === true);
                $hide = ($block['attrs']['editorskit']['loggedout'] === true);



                if($show && $valid){
	                return $block_content;
                }elseif ($show && !$valid){
                    return '';
                }elseif ($hide && $valid){
	                return '';
                }elseif ($hide && !$valid){

                	return do_shortcode( strval($block['innerHTML']) );
                }

            }else{
			    return $block_content;
            }

        }


		/**
		 * Plugin activation
		 * @since  0.1
		 */
		function plugin_activation() {

		}

		/**
		 * Plugin deactivation
		 * @since  0.1
		 */
		function plugin_deactivation() {

		}
	}


	
	function kigoplan_php_version_admin_notice() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'PHP Version Update Required!', 'kigoplan' ); ?></p>
            <p><?php _e( 'You are using PHP Version ' . PHP_VERSION, 'kigoplan' ); ?></p>
            <p><?php _e( 'Please make sure you have at least php version 5.3 installed.', 'kigoplan' ); ?></p>
        </div>
		<?php
	}

	function activate_kigoplan_at_plugin_loader() {
		// KigoplanCodes requires php version 5.3 or higher.
		if ( PHP_VERSION < 5.3 ) {
			add_action( 'admin_notices', 'kigoplan_php_version_admin_notice' );
		} else {
			// Init KigoplanCodes.
			$GLOBALS['kigoplan_new'] = new KigoplanCodes();
			
		}
	}

	activate_kigoplan_at_plugin_loader();

	function remove_editor_kits_render(){
		remove_action( 'render_block', array( 'EditorsKit_Render_Block', 'render_block' ), 5, 2 );
	}
	add_action('wp_head', 'remove_editor_kits_render');
}

