<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.9
Purpose: Code handler for the Login Page
*/


function xb_nmc_wp_validate_login_entry_point($user, $user_name, $password){

	if(strpos($_SERVER['REQUEST_URI'],"wp-safelogin") == false){

		if(isset($_SERVER['REQUEST_METHOD'])){
			if($_SERVER['REQUEST_METHOD'] == "POST"){
				$xb_nmc_state = xb_nmc_wp_validate_login_post($user_name, $user);
				if($xb_nmc_state != 1){
					return null;
				}else{
					return $user;
				}
				
			}else{
				return $user;
			}
			
		}else{
			return $user;
		}

	}else{
		return $user;
	}
}


function xb_nmc_wp_validate_login_post($user_name, $user){

	$xb_state = 0;
	if(isset($_POST['oxbioxid'])){
		$xb_oxbioxid = $_POST['oxbioxid'];
		$xb_state = 1;
//		$xb_state = xb_nmc_maybe_human($xb_oxbioxid,$user_name,$user);
	}else{
		xb_nmc_definitely_bot($user_name,$user);
	}
	return $xb_state;
}
?>