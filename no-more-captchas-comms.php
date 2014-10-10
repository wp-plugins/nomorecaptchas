<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.8
Purpose: To handle all communications between the webserver & OxBio Servers
*/


function xb_nmc_maybe_human($xb_oxbioxid,$user_name,$user){

	$xb_result = xb_wait4response($xb_oxbioxid,$user_name,$user);
	return $xb_result;

}


function xb_wait4response($xb_oxbioxid,$user_name,$user){

	xb_fireNforget($xb_oxbioxid,$user_name,$user);

	$xb_nmc_authkey = get_option( 'xb_nmc_authkey', '' );
	$xb_nmc_url = "http://134.0.78.247/services/public/NmC4WordPress/NmC_Proxy.php";
	$xb_post_string = 'oxbioxid='.$xb_oxbioxid.'&authkey='.$xb_nmc_authkey;
//	$xb_post_string .= '&user1='.$user_name.'&user2='.$user;
	$xb_post_string .= '&user1='.$user_name;
	$xb_ch = curl_init();
		curl_setopt($xb_ch, CURLOPT_POST, 2);
		curl_setopt($xb_ch, CURLOPT_POSTFIELDS, $xb_post_string);
		curl_setopt($xb_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($xb_ch, CURLOPT_URL, $xb_nmc_url);
		$xb_result = curl_exec($xb_ch);
	curl_close($xb_ch);
	if(strpos($xb_result,"NMC-USER-OK") !== false){
		return 1;
	}else{
		return 0;
	}

}


function xb_nmc_definitely_bot($user_name,$user){

	xb_fireNforget(2,$user_name,$user);
	return;

}


function xb_fireNforget($xb_oxbioxid,$user_name,$user){

	$xb_nmc_authkey = get_option( 'xb_nmc_authkey', '' );
	$xb_nmc_url      = "http://134.0.78.247/services/public/NmC4WordPress/NmC_Log.php";
	$xb_post_string  = "authkey=".$xb_nmc_authkey."&chk=502";

	$XB_REQUEST_METHOD 				= 'not-set';
	$XB_REMOTE_ADDR 				= 'not-set';
	$XB_HTTP_CLIENT_IP 				= 'not-set';
	$XB_HTTP_X_FORWARDED_FOR 		= 'not-set';
	$XB_HTTP_X_FORWARDED 			= 'not-set';
	$XB_HTTP_X_CLUSTER_CLIENT_IP 	= 'not-set';
	$XB_HTTP_REFERER 				= 'not-set';
	$XB_REQUEST_URI 				= 'not-set';
	$XB_HTTP_USER_AGENT 			= 'not-set';

	if(isset($_SERVER['REQUEST_METHOD']))			{$XB_REQUEST_METHOD 			= $_SERVER['REQUEST_METHOD'];}
	if(isset($_SERVER['REMOTE_ADDR']))				{$XB_REMOTE_ADDR 				= $_SERVER['REMOTE_ADDR'];}
	if(isset($_SERVER['HTTP_CLIENT_IP']))			{$XB_HTTP_CLIENT_IP 			= $_SERVER['HTTP_CLIENT_IP'];}
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))		{$XB_HTTP_X_FORWARDED_FOR 		= $_SERVER['HTTP_X_FORWARDED_FOR'];}
	if(isset($_SERVER['HTTP_X_FORWARDED']))			{$XB_HTTP_X_FORWARDED 			= $_SERVER['HTTP_X_FORWARDED'];}
	if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))	{$XB_HTTP_X_CLUSTER_CLIENT_IP 	= $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];}
	if(isset($_SERVER['HTTP_REFERER']))				{$XB_HTTP_REFERER 				= $_SERVER['HTTP_REFERER'];}
	if(isset($_SERVER['REQUEST_URI']))				{$XB_REQUEST_URI 				= $_SERVER['REQUEST_URI'];}
	if(isset($_SERVER['HTTP_USER_AGENT']))			{$XB_HTTP_USER_AGENT 			= $_SERVER['HTTP_USER_AGENT'];}

	$xb_field_block  = '-spare-';
	$xb_field_block .= '|-|'.$XB_REQUEST_METHOD;
	$xb_field_block .= '|-|'.$XB_REMOTE_ADDR;
	$xb_field_block .= '|-|'.$XB_HTTP_CLIENT_IP;
	$xb_field_block .= '|-|'.$XB_HTTP_X_FORWARDED_FOR;
	$xb_field_block .= '|-|'.$XB_HTTP_X_FORWARDED;
	$xb_field_block .= '|-|'.$XB_HTTP_X_CLUSTER_CLIENT_IP;
	$xb_field_block .= '|-|'.$user_name;
	$xb_field_block .= '|-|'.file_get_contents("php://input");
	$xb_field_block .= '|-|'.$xb_oxbioxid;
	$xb_field_block .= '|-|'.$XB_HTTP_REFERER;
	$xb_field_block .= '|-|'.$XB_REQUEST_URI;
	$xb_field_block .= '|-|'.$XB_HTTP_USER_AGENT;

	$xb_post_string .= "&data=".base64_encode($xb_field_block);

	$xb_ch = curl_init();
		curl_setopt($xb_ch, CURLOPT_POST, 3);
		curl_setopt($xb_ch, CURLOPT_POSTFIELDS, $xb_post_string);
		curl_setopt($xb_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($xb_ch, CURLOPT_URL, $xb_nmc_url);
		$xb_result = curl_exec($xb_ch);
	curl_close($xb_ch);
}

?>