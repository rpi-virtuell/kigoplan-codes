<?php

/**
 * Create the post type to hold the codes. We use a normal WordPress Post Type and post meta to create and manage codes and code meta data.
 *
 * Post Type: kigoplan_codes
 *
 * @since  0.1
 *
 */
function cptui_register_my_cpts_kigoplan_codes() {

	/**
	 * Post Type: Kigoplan Codes.
	 */

	$labels = [
		"name" => __( "Kigoplan Codes", "kigoplan-codes" ),
		"singular_name" => __( "Kigoplan Code", "kigoplan-codes" ),
		"menu_name" => __( "Kigoplan", "kigoplan-codes" ),
		"all_items" => __( "Alle Schlüssel", "kigoplan-codes" ),
		"add_new_item" => __( "Neuen Schlüssel erstellen", "kigoplan-codes" ),
		"edit_item" => __( "Schlüssel bearbeiten", "kigoplan-codes" ),
		"new_item" => __( "Neuer Schlüssel", "kigoplan-codes" ),
		"view_item" => __( "Schlüssel zeigen", "kigoplan-codes" ),
		"view_items" => __( "Schlüssel zeigen", "kigoplan-codes" ),
		"search_items" => __( "Schlüssel suchen", "kigoplan-codes" ),
	];

	$args = [
		"label" => __( "Kigoplan Codes", "kigoplan-codes" ),
		"labels" => $labels,
		"description" => "Codes, die bei der Registrierung zum Kigoplan verwendet werden",
		"public" => false,
		"publicly_queryable" => false,
		"show_ui" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "kigoplan_code", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "password" ],
	];

	register_post_type( "kigoplan_codes", $args );
}

add_action( 'init', 'cptui_register_my_cpts_kigoplan_codes' );


