<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.3.2
Purpose: To configure NoMoreCaptchas Plugin Actions
*/


if ( !empty($GLOBALS['pagenow'])
	 and ('options-general.php' === $GLOBALS['pagenow'] or 'options.php' === $GLOBALS['pagenow'] )){
	 add_action( 'admin_init', 'xb_nmc_register_settings' );
}

add_action('wp_head',    				'xb_nmc_wp_head');
add_action('login_head', 				'xb_nmc_wp_head');
add_action('register_form',				'xb_nmc_show_iframe');
add_action('login_form',				'xb_nmc_show_iframe');
add_action('authenticate', 				'xb_nmc_wp_validate_login_entry_point', 100,   3);
add_action('admin_menu', 				'xb_nmc_add_options_page');
add_action('bp_after_register_page', 	'xb_nmc_show_iframe');
add_action('wpcf7_before_send_mail', 	'xb_nmc_wp_ct7_entry_point');


?>