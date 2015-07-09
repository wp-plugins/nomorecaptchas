<?php

/*
  Plugin Name: NoMoreCaptchas
  Plugin URI: http://nomorecaptchas.com
  Description: NoMoreCaptchas uses behavior to determine if the thing knocking on your door is a human or a bot. We send all bots away and let humans in without annoying puzzles or Captcha codes.
  Author: Oxford BioChronometrics SA
  Version: 2.0
  Author URI: http://nomorecaptchas.com
 */
/* * ******************************************************************************************* */
include_once dirname(__FILE__) . '/no-more-captchas-validate.php';

include_once dirname(__FILE__) . '/no-more-captchas-options.php';

/* * ******************************************************************************************* */

function xb_nmc_settings_link($links) {

    $settings_link = '<a href="options-general.php?page=xb_nmc_config">Settings</a>';

    array_unshift($links, $settings_link);

    return $links;
}

/* * ******************************************************************************************* */

$plugin = plugin_basename(__FILE__);

add_filter("plugin_action_links_$plugin", 'xb_nmc_settings_link');

/* * ******************************************************************************************* */
global $pagenow;
if ($pagenow == 'options-general.php' || $pagenow == 'plugins.php') :
    add_action('admin_notices', 'disable_trackbacks_admin_notice');
    add_action('admin_notices', 'register_admin_notice');

    function disable_trackbacks_admin_notice() {
        global $current_user;

        $user_id = $current_user->ID;

        /* Check that the user hasn't already clicked to ignore the message */

        if (!get_user_meta($user_id, 'disbale_nag_ignore')) {
            $pageUrl = 'options-general.php?page=xb_nmc_config&tab=settings_page&disbale_nag_ignore=0';
            echo '<div class="error"><p>';

            printf(__('<strong>Please go to Settings>Discussion to disable trackbacks and pingbacks and enable the option that users must be registered to comment.</strong> That will shut the back door to your site that spammers exploit. | <a href="%1$s">Hide Notice</a>'), $pageUrl);

            echo "</p></div>";
        }
    }

    function register_admin_notice() {
        global $current_user;

        $user_id = $current_user->ID;

        /* Check that the user hasn't already clicked to ignore the message */

        if (!get_user_meta($user_id, 'register_ignore_notice')) {
            $pageUrl = 'options-general.php?page=xb_nmc_config&tab=settings_page&register_nag_ignore=0';

            echo '<div class="updated"><p>';

            printf(__('Please be sure to <a href=http://nomorecaptchas.com/#!/register target="_blank">register NoMoreCaptchas</a> in order to activate protection. | <a href="%1$s">Hide Notice</a>'), $pageUrl);

            echo "</p></div>";
        }
    }

endif;

add_action('admin_init', 'disable_trackbacks_nag_ignore');
add_action('admin_init', 'register_nag_ignore');

function disable_trackbacks_nag_ignore() {

    global $current_user;

    $user_id = $current_user->ID;

    if (isset($_GET['disbale_nag_ignore']) && '0' == $_GET['disbale_nag_ignore']) {

        add_user_meta($user_id, 'disbale_nag_ignore', 'true', true);
    }
}

function register_nag_ignore() {

    global $current_user;

    $user_id = $current_user->ID;

    if (isset($_GET['register_nag_ignore']) && '0' == $_GET['register_nag_ignore']) {

        add_user_meta($user_id, 'register_ignore_notice', 'true', true);
    }
}

?>