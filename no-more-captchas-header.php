<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.6
Purpose: Code handler for the HTML page header JavaScript
*/


function xb_nmc_wp_head(){


	if(strpos($_SERVER['REQUEST_URI'],"wp-safelogin") == false){

		$xb_output="<script type=\"text/javascript\" src".
				"=\"//oxford-biochron.com/services/".
				"public/analytics/min.1.86.".
				"analytics.js\" ></script>";
		echo $xb_output;
		
	}
	
}

?>