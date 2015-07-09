<?php

/*
  NoMoreCaptchas
  Oxford BioChronometrics SA
  Version: 2.0
  Purpose: To configure NoMoreCaptchas Settings Page
 */

function xb_nmc_dashboard() {

    // If the social options don't exist, create them.
    if (false == get_option('plugin:xb_nmc_dashboard')) {
        add_option('plugin:xb_nmc_dashboard');
    } // end if
    add_settings_section(
            'dashboard_section_field_1', // ID used to identify this section and with which to register options
            'Dashboard', // Title to be displayed on the administration page
            '', // Callback used to render the description of the section
            'xb_nmc_slug_dashboard'      // Page on which to add this section of options
    );
    add_settings_section(
            'dashboard_section_field_2', '', 'xb_nmc_render_upgrade_section', 'xb_nmc_slug_dashboard'
    );
}

function xb_nmc_render_upgrade_section() {
    global $current_user;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if (get_user_meta($user_id, 'example_ignore_notice')) {
        echo '<div class="upgrade_notice"><p>';
        print('<a href="http://nomorecaptchas.com/#!/pricing" target="_blank"><strong>Upgrade</strong></a> for more detailed dashboard analytics.');
        echo "</p></div>";
    }
}

add_action('admin_init', 'disable_trackbacks_nag_ignore');
add_action('admin_init', 'register_nag_ignore');

function xb_nmc_register_settings() {
    $option_name = 'plugin:xb_nmc_option_name';
    $option_values = get_option($option_name);

    $default_values = array(
        'xb_nmc_authkey' => '',
        'xb_nmc_sub_level' => '',
        'xb_nmc_org_name' => '',
        'xb_nmc_org_address' => '',
        'xb_nmc_domain' => '',
        'xb_nmc_contact_name' => '',
        'xb_nmc_contact_email' => '',
        'xb_nmc_authcode' => ''
    );

    $data = shortcode_atts($default_values, $option_values);

    $xb_authkey64 = esc_attr($data['xb_nmc_authkey']);
    $xb_authkey_array = array('not-set', 'not-set', 'not-set', 'not-set',
        'not-set', 'not-set', 'not-set', 'not-set');
    if (strlen($xb_authkey64) > 50) {
        $xb_authkey64 = str_replace("=== START OF KEY ===", "", $xb_authkey64);
        $xb_authkey64 = str_replace("=== END OF KEY ===", "", $xb_authkey64);
        $xb_authkey = base64_decode($xb_authkey64);
        $xb_authkey_array_raw = explode("|", $xb_authkey);
        if (count($xb_authkey_array_raw) == 9) {
            $xb_authkey_array = $xb_authkey_array_raw;
            $xb_authkey_array[3] = $xb_authkey_array[3] . ' ' . $xb_authkey_array[4];
        }
    }

    register_setting(
            'plugin:xb_nmc_option_group', $option_name, ''
    );

    update_option('xb_nmc_authkey', $xb_authkey_array[8]);

    add_settings_section(
            'section_2', 'Subscription Licence', 'xb_nmc_render_subscription_section', 'xb_nmc_slug'
    );


    add_settings_field(
            'section_2_field_3', 'Licence Key', 'xb_nmc_render_subscription_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label3',
        'name' => 'xb_nmc_authkey',
        'value' => esc_attr($data['xb_nmc_authkey']),
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_9', 'Authenticating Code', 'xb_nmc_render_viewonly_and_copy_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label9',
        'name' => 'xb_nmc_authcode',
        'value' => $xb_authkey_array[8],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_6a', 'Primary Domain', 'xb_nmc_render_viewonly_and_copy_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label6a',
        'name' => 'xb_nmc_domain',
        'value' => $xb_authkey_array[5],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_4', 'Subscription Level', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label4',
        'name' => 'xb_nmc_sub_level',
        'value' => $xb_authkey_array[1],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_5x', 'Valid From', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label5x',
        'name' => 'xb_nmc_valid_from',
        'value' => $xb_authkey_array[0],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_5', 'Organisation Name', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label5',
        'name' => 'xb_nmc_org_name',
        'value' => $xb_authkey_array[2],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_6', 'Organisation City & Country', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label6',
        'name' => 'xb_nmc_org_address',
        'value' => $xb_authkey_array[3],
        'option_name' => $option_name
            )
    );


    add_settings_field(
            'section_2_field_7', 'Contact Person', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label7',
        'name' => 'xb_nmc_contact_name',
        'value' => $xb_authkey_array[6],
        'option_name' => $option_name
            )
    );

    add_settings_field(
            'section_2_field_8', 'Contact E-mail Address', 'xb_nmc_render_viewonly_field', 'xb_nmc_slug', 'section_2', array(
        'label_for' => 'label8',
        'name' => 'xb_nmc_contact_email',
        'value' => $xb_authkey_array[7],
        'option_name' => $option_name
            )
    );
}

function xb_nmc_render_subscription_section() {
    print '<p><b>Don&#39;t have a Licence Key? Register <a href="http://nomorecaptchas.com/#!/register" target="_blank">here</a> for one now!</b></p>';
    print '<p>Your Licence Key holds details about your organisation, domain and the subscription level you have purchased.' .
            ' In order to use NoMoreCapthas, you must first validate your Licence Key.</p>';
}

function xb_nmc_render_subscription_field($args) {
    printf(
            '<textarea name="%1$s[%2$s]" id="%3$s" rows="8" cols="55" placeholder="Paste your NoMoreCaptchas Licence Key here......." class="code">%4$s</textarea>', $args['option_name'], $args['name'], $args['label_for'], $args['value']
    );
}

function xb_nmc_render_viewonly_field($args) {
    printf(
            '<input DISABLED name="%1$s[%2$s]" id="%3$s"  value="%4$s" class="regular-text">', $args['option_name'], $args['name'], $args['label_for'], $args['value']
    );
}

function xb_nmc_render_viewonly_and_copy_field($args) {
    printf(
            '<input name="%1$s[%2$s]" id="%3$s"  value="%4$s" class="regular-text">', $args['option_name'], $args['name'], $args['label_for'], $args['value']
    );
}

?>