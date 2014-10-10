<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.8
Purpose: Code handler for Contact Form Seven
*/


function xb_nmc_wp_ct7_entry_point($WPCF7_ContactForm){

	$xb_data = $WPCF7_ContactForm->posted_data;
	if(isset($xb_data['oxbioxid'])){			
		$xb_state = xb_nmc_maybe_human($xb_data['oxbioxid'],'ct71','ct71');
		if($xb_state != 1){
			$WPCF7_ContactForm->skip_mail = true;
		}else{
			$WPCF7_ContactForm->mail['subject'] .= " [Validated Not Spam by NoMoreCaptchas]";
			$WPCF7_ContactForm->mail['body'] .= "\r\n\r\n";
			$WPCF7_ContactForm->mail['body'] .= "\r\n==================================================\r\n";
			$WPCF7_ContactForm->mail['body'] .= "\r\n NoMoreCaptchas Validated this e-mail as Not Spam \r\n";
			$WPCF7_ContactForm->mail['body'] .= "\r\n==================================================\r\n";
			$WPCF7_ContactForm->mail['body'] .= "\r\n\r\n";
		}
	}else{
		$WPCF7_ContactForm->skip_mail = false;
		xb_nmc_definitely_bot('ct72','ct72');
	}
}

?>