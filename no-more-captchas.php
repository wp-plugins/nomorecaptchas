<?php
/*
Plugin Name: NoMoreCaptchas
Plugin URI: http://nomorecaptchas.com/download-support/
Description: NoMoreCaptchas uses biochronometric behavior to determine if the thing knocking on your door is a human or a bot. We send all bots away and let humans in.
Author: Oxford BioChronometrics SA
Version: 1.2.5
Author URI: http://nomorecaptchas.com
*/

include_once dirname( __FILE__ ) . '/no-more-captchas-admin.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-options.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-header.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-filters.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-actions.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-comms.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-login.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-registration.php';
include_once dirname( __FILE__ ) . '/no-more-captchas-iframe.php';

// Add settings link on plugin page
function your_plugin_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=xb_nmc_config">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );

?>