<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.3.2
Purpose: Code handler for the Registration Page
*/


function xb_nmc_wp_registration_errors_entry_point($errors, $user_name, $user_email){

	if(isset($_SERVER['REQUEST_METHOD'])){
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$xb_nmc_state = xb_nmc_wp_validate_registration_post($user_name,$user_email);
			if($xb_nmc_state != 1){
				$errors->add( 'nmc_error_1', __('<strong>ERROR: </strong>Invalid Username or E-mail Address.',''));
			}
		}
	}
    return $errors;

}


function xb_nmc_wp_validate_registration_post($user_name,$user_email){

	$xb_state = 0;
	if(isset($_POST['oxbioxid']) ){
		$xb_oxbioxid=$_POST['oxbioxid'];
		//print_r($xb_oxbioxid);
		$xb_state = 1;
		//$xb_state = xb_nmc_maybe_human($xb_oxbioxid,$user_name,$user_email);
	}else{
		xb_nmc_definitely_bot($user_name,$user_email);
	}
	//echo "<div style='display: none;'>".$xb_state."</div>";
	return $xb_state;

}


function xb_nmc_bp_registration_errors_entry_point( $result = array()) {

	if(isset($_SERVER['REQUEST_METHOD'])){
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$xb_nmc_state = xb_nmc_wp_validate_registration_post("x","y");
			if($xb_nmc_state != 1){
				//$result['errors']->add( 'bp_members_signup_error_message', __( "<strong>BP1-ERROR: </strong>Invalid Username or E-mail Address." ) );
				//apply_filters('bp_members_signup_error_message', "<div class=\"error\">BP2-ERROR</div>" );
				$result['errors']->add( 'user_name', apply_filters( 'bppj_honeypot_fail_message', __( "Invalid Username" ) ) );
				$result['errors']->add( 'user_email', apply_filters( 'bppj_honeypot_fail_message', __( "Invalid Email Address" ) ) );
			}
		}
	}




	return $result;
}
?>