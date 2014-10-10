<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.8
Purpose: To handle placement of the NoMoreCaptchas iFrame
*/


function xb_nmc_show_iframe(){

	if(strpos($_SERVER['REQUEST_URI'],"wp-safelogin") == false){

		$xb_nmc_authkey = get_option( 'xb_nmc_authkey', '' );
		$src = "//134.0.78.247/services/public/iframes/nmc-iframe-centre.php";

		echo "	<div id=xb-nmc-frm>
					<iframe
						src='".$src.
						"?wih=55&ww=54&wtfs=16&nih=200&niw=249&wtms=3&key=".$xb_nmc_authkey."'
						width=270
						height=180
						frameBorder='0'
						>
					</iframe>
				</div>";
	}
}
?>