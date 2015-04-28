<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.3.2
Purpose: To configure NoMoreCaptchas Plugin Filters
*/


add_filter('registration_errors', 			 'xb_nmc_wp_registration_errors_entry_point',10,3);
add_filter( 'bp_core_validate_user_signup',  'xb_nmc_bp_registration_errors_entry_point');

?>