<?php
/*
NoMoreCaptchas
Oxford BioChronometrics SA
Version: 1.2.7
Purpose: To handle code to add Admin menu item
*/


function xb_nmc_add_options_page()
{
    add_options_page(
        'NoMoreCaptchas Settings Page',
        'NoMoreCaptchas',
        'manage_options',
        'xb_nmc_config',
        'xb_nmc_render_page'
    );
}

function xb_nmc_render_page()
{
    ?>
    <div class="wrap">
        <h2><?php print $GLOBALS['title']; ?></h2>
        <form action="options.php" method="POST">
            <?php
            settings_fields( 'plugin:xb_nmc_option_group' );
            do_settings_sections( 'xb_nmc_slug' );
			submit_button( 'Validate Liciense Key', 'primary' );
            ?>
        </form>
    </div>
	<div><p>You can view the latest bot/human activity hitting your website on the NoMoreCaptchas <a href="http://nomorecaptchas.com/customer-dashboard/"  target="_blank">Dashboard</a>. You will need the domain name that you registered for NoMoreCaptchas and your Authenticating Code, both of which can be found above.</p></div>
    <?php
}
?>