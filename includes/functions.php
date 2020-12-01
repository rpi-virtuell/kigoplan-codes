<?php

/**
 * Check if the WordPress default registration should get protected with an invite only form element
 *
 * @since  0.1
 *
 * return boolean
 */
function kigoplan_is_default_registration() {
	$kigoplan_general = get_option( 'kigoplan_general' );
	if ( isset( $kigoplan_general['default_registration'] ) && $kigoplan_general['default_registration'] == 'disable' ) {
		return false;
	}

	return true;
}


function  print_kigoplan_codes(){

	$args = array(
		'post_type' => 'kigoplan_codes',
	);

	if(isset($_GET['jahr'])){
		$year = sanitize_text_field($_GET['jahr']);
	}else{
		$year = null;
	}


	$codes = get_kigoplan_codes($year);

	$html =  count($codes) . " Schüssel gefunden";


	$strcodes = implode(", ", $codes);



	$html = '<textarea style="width:100%;min-height:600px;">'.$strcodes.'</textarea>';
	$html .= '<b>'.count($codes).'</b> aktive Registrierungsschlüssel';

	echo $html;

}


function print_kigoplan_as_csv(){
	if(
		isset($_GET["kigoplan_action"]) &&
		$_GET["kigoplan_action"]=="export" &&
		current_user_can('manage_options')
	){

		if(isset($_GET['jahr'])){
			$year = sanitize_text_field($_GET['jahr']);
		}else{
			$year = null;
		}


		$codes = get_kigoplan_codes($year);

		$csv =  "Schluessel;Registrierungsurl\n";

		foreach ($codes as $c){
			$csv .= "$c;" . home_url("/k/$c") ."\n";
		}

		header( "Content-type: application/vnd.ms-excel; charset=UTF-8" );
		header("Content-Disposition: attachment; filename=kigoplan.csv");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

		echo trim(utf8_decode($csv));

		die();

	}
}

add_action('init', 'print_kigoplan_as_csv');



function get_kigoplan_codes($year = null){

	if ($year === null){
		$andwhere = "AND post_password <>''AND post_password is not null";
	} else{
		$andwhere = "AND post_password ='{$year}'";
	}

	global $wpdb;
	$codes =  $wpdb->get_results("SELECT post_name 
                              FROM {$wpdb->posts} 
                              WHERE post_type = 'kigoplan_codes'
                              {$andwhere}
                              ORDER BY post_password", ARRAY_N );
	$return_codes = array();

	foreach ($codes as $code){
		$return_codes[]=$code[0];
	}
	return $return_codes;
}


function get_kigoplan_code_years(){
	global $wpdb;
	return $wpdb->get_results("SELECT DISTINCT post_password 
                              FROM {$wpdb->posts} 
                              WHERE post_type = 'kigoplan_codes'
                              AND post_password <>''
                              AND post_password is not null
                              ORDER BY post_password", ARRAY_N );

}

function print_kigoplan_code_years(){

	$years = get_kigoplan_code_years();


	$uri = explode('tab=export',$_SERVER["REQUEST_URI"]);

	$url = $uri[0].'tab=export';

	$html =  "<ul><li><a href=\"{$url}\">Alle</a></li>";

	foreach ($years as $year){

		$html .= "<li><a href=\"{$url}&jahr={$year[0]}\">{$year[0]}</a></li>";

	}
	$html .=  '</ul>';
	echo $html;
}

