<?php
/*
  NoMoreCaptchas
  Oxford BioChronometrics SA
  Version: 2.0
  Purpose: Main()
 */
require_once("geoip-api/src/geoip.inc");
/* * ******************************************************************************************* */
if (!empty($GLOBALS['pagenow']) and ('options-general.php' === $GLOBALS['pagenow'] or 'options.php' === $GLOBALS['pagenow'])) {
    add_action('admin_init', 'xb_nmc_register_settings');
    add_action('admin_init', 'xb_nmc_dashboard');
}
/* * ******************************************************************************************* */
add_action('admin_menu', 'xb_options');

function xb_options() {
    add_options_page('NoMoreCaptchas Settings Page', 'NoMoreCaptchas', 'manage_options', 'xb_nmc_config', 'xb_nmc_render_page');
}

/* * ******************************************************************************************* */
add_action('register_form', 'xb_iframe');
add_action('login_form', 'xb_iframe');
add_action('bp_after_register_page', 'xb_iframe');
add_action('comment_form', 'xb_iframe');

if (isset($_REQUEST['xb_bot_error']))
    add_action('comment_form', 'xb_bot_error');

function xb_iframe() {
    echo "<div id=xb-nmc-frm><iframe src='//ox-bio.com/ad.server/' width=270 height=180 frameBorder='0' ></iframe></div>";
}

function xb_bot_error() {
    echo "<div id=form-bot-error>You're a bot.</div>";
}

/* * ******************************************************************************************* */

function xb_enqueue_script() {
    $plugin = plugin_dir_url(__FILE__);
    wp_register_script('name-of-script', $plugin . 'nmc-script.js', array('jquery'));
    wp_enqueue_script('name-of-script');
}

function xb_custom_scripts() {
    $plugin = plugin_dir_url(__FILE__);
    wp_register_script('name-of-script', $plugin . 'nmc-script.js', array('jquery'));
    wp_enqueue_script('name-of-script');
}

function xb_custom_style() {
    $plugin = plugin_dir_url(__FILE__);
    wp_enqueue_style('prefix-style', $plugin . 'nmc-style.css', __FILE__);
}

add_action('login_enqueue_scripts', 'xb_enqueue_script', 1);
add_action('admin_enqueue_scripts', 'xb_custom_style');
add_action('wp_enqueue_scripts', 'xb_custom_scripts');



/* * ******************************************************************************************* */

//Include Iframe for Contact form
add_filter('wpcf7_form_elements', 'rl_wpcf7_form_elements');

function rl_wpcf7_form_elements($content) {
    // global $wpcf7_contact_form;

    $rl_pfind = '/<p><input/';
    $rl_preplace = "<p> <div id=xb-nmc-frm><iframe src='//ox-bio.com/ad.server/' width=270 height=180 frameBorder='0' ></iframe></div><input";
    $content = preg_replace($rl_pfind, $rl_preplace, $content, 2);

    return $content;
}

add_action('wpcf7_before_send_mail', 'xb_ct7');

function xb_ct7($ct7_ContactForm) {
    $WPCF7_ContactForm = WPCF7_Submission::get_instance();
    if ($WPCF7_ContactForm) {
        $xb_data = $WPCF7_ContactForm->get_posted_data();
        if (xb_validate($xb_data)) {
            $mail = $ct7_ContactForm->prop('mail');
            $mail['subject'] .= " [Validated Not Spam by NoMoreCaptchas]";
            $mail['body'] .= "\r\n\r\n\r\n==================================================\r\n\r\n NoMoreCaptchas Validated this e-mail as Not Spam \r\n\r\n==================================================\r\n\r\n\r\n";
            $ct7_ContactForm->set_properties(array('mail' => $mail));
            xb_nmc_wp_bot_log_file("human", "contact-us");
        } else {
            $WPCF7_ContactForm->skip_mail = true;
            xb_nmc_wp_bot_log_file("bot", "contact-us");
        }
    }
}

/* * ******************************************************************************************* */

function xb_validate($post_data) {
    if (isset($post_data['xbt0']) || isset($post_data['xbk0']) || isset($post_data['xbz0'])) {
        return true;
    } else {
        return false;
    }
}

/* * ******************************************************************************************* */

function get_files_list() {
    $file_list = glob(plugin_dir_path(__FILE__) . 'logs/' . "*.txt");
    if (sizeof($file_list) > 0) {
        for ($i = 0; $i < sizeof($file_list); $i++) {
            $files[$i] = ltrim($file_list[$i], plugin_dir_path(__FILE__) . "logs/");
        }
        return $files;
    }
    return;
}

function read_file_by_filename($filename) {
    $myfile = fopen(plugin_dir_path(__FILE__) . 'logs/' . $filename, "r") or die("Unable to open file!");
    $log_data = fread($myfile, filesize(plugin_dir_path(__FILE__) . 'logs/' . $filename));
    fclose($myfile);
    return $log_data;
}

function get_total_authentication_by_month() {
    $files = get_files_list();
    $month = current_time('m');
    $total_authentications = 0;
    $cur_month_total_auth = 0;
    if ($files)
        foreach ($files as $file) {
            $pos = strpos(substr($file, -9, 2), $month);

            $linecount = 0;
            $week4 = $week3 = $week2 = $week1 = 0;
            $cur_month_linecount = 0;
            $myfile = fopen(plugin_dir_path(__FILE__) . 'logs/' . $file, "r") or die("Unable to open file!");
            while (!feof($myfile)) {
                $line = fgets($myfile);
                $linecount++;
                if ($pos !== false) {
                    $cur_month_linecount++;
                }
            }

            $total_authentications = $total_authentications + $linecount - 1;
            if ($pos !== false) {
                $cur_month_total_auth = $cur_month_total_auth + $cur_month_linecount - 1;
            }
        }



    return $total_authentications;
}

function get_authentication_code() {
    $option_name = 'plugin:xb_nmc_option_name';
    $option_values = get_option($option_name);
    return $option_values['xb_nmc_authcode'];
}

function get_domain_name() {
    $option_name = 'plugin:xb_nmc_option_name';
    $option_values = get_option($option_name);
    return $option_values['xb_nmc_domain'];
}

function dashboard_head_output() {
    $total_authentications = get_total_authentication_by_month();

// Creating Sorting parametes for Table heads hrefs
    if (isset($_GET['ord']))
        $sorting_param = substr($_SERVER['REQUEST_URI'], 0, -20);
    else
        $sorting_param=$_SERVER['REQUEST_URI'];
    $ord = isset($_GET['ord']) ? $_GET['ord'] : 'SORT_DESC';
    if ($ord == 'SORT_DESC')
        $ord = 'SORT_ASC&';
    else if ($ord == 'SORT_ASC')
        $ord = 'SORT_DESC';

    // Table Header divs and header   
    $html = ' <div class="dashboard-info-div">
		<table class="dashboard-info" id="dashboard-info">
		<tr>
		<td>
		<div class="refresh_btn"><a href="javascript:document.location.reload();" class="refresh_btn">Refresh</a></div>
 		</td>
 		<td align="right"><div><b>Verifications for the Current Month </b> ' . $total_authentications . '</div></td>
 		</tr>
 		</table>
 		</div>
 		<div class="dashboard-table-div">
 		<table class="dashboard-table" id="dashboard-table">
        <thead>
        <tr>
            <th>No.</th>
            <th><a href="' . $sorting_param . '&col=0&ord=' . $ord . '">Time</a></th>
            <th><a href="' . $sorting_param . '&col=1&ord=' . $ord . '">Device IP</a></th>
            <th><a href="' . $sorting_param . '&col=4&ord=' . $ord . '">State</a></th>
            <th><a href="' . $sorting_param . '&col=2&ord=' . $ord . '">Page Accessed</a></th>
            <th><a href="' . $sorting_param . '&col=3&ord=' . $ord . '">Country of Origin</a></th>
        </tr>
        </thead>
        <tbody>
        ';
    return $html;
}

function dashboard_values_output($i, $gmt, $ip, $state, $page, $country) {
//    if($i%2 == 0)
//        $row_class = "even-row";
    $html = '
            <tr >
                <td>' . $i . '</td>
                <td>' . $gmt . '</td>
                <td>' . $ip . '</td>
                <td>' . $state . '</td>
                <td>' . $page . '</td>
                <td>' . $country . '</td>
            </tr>
        ';
    return $html;
}

function xb_nmc_render_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
    ?>
    <div class="wrap">

        <h2 class="nav-tab-wrapper">
            <a href="?page=xb_nmc_config&tab=dashboard" class="nav-tab <?php echo $active_tab == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
            <a href="?page=xb_nmc_config&tab=settings_page" class="nav-tab <?php echo $active_tab == 'settings_page' ? 'nav-tab-active' : ''; ?>">Settings Page</a>
        </h2>
        <form action="options.php" method="POST">
    <?php if ($active_tab == 'settings_page') { ?>
                <h2><?php print $GLOBALS['title']; ?></h2>
                <?php
                settings_fields('plugin:xb_nmc_option_group');
                do_settings_sections('xb_nmc_slug');
                submit_button('Validate Licence Key', 'primary');
            } else {
                settings_fields('plugin:xb_nmc_dashboard');
                do_settings_sections('xb_nmc_slug_dashboard');
                //get_total_authentication_by_month();
                //$total_authentications= "64";
                $html = dashboard_head_output();
                print($html);

                $files = get_files_list();
                $complete_log = array();
                for ($j = (sizeof($files) - 1); $j >= 0; $j--) {
                    unset($log_data_array);
                    unset($log_details);
                    $filename = $files[$j];
                    $log_data = read_file_by_filename($filename);
                    $log_data_array = explode("\n", $log_data);
                    foreach ($log_data_array as $key => $val) {
                        if (empty($val)) {
                            unset($log_data_array[$key]);
                        }
                    }
                    for ($i = 0; $i < sizeof($log_data_array); $i++) {
                        $log_details[$i] = explode("|", $log_data_array[$i]);
                    }
                    $complete_log = array_merge($complete_log, $log_details);
                }


                // Get posted parametes and do sorting according 
                $col = isset($_GET['col']) ? $_GET['col'] : '0';
                $ord = isset($_GET['ord']) ? $_GET['ord'] : 'SORT_DESC';
                if ($ord == 'SORT_DESC')
                    $ord = SORT_DESC;
                else if ($ord == 'SORT_ASC')
                    $ord = SORT_ASC;

                $tmp = Array();
                foreach ($complete_log as &$ma)
                    $tmp[] = &$ma[$col];
                array_multisort($tmp, $ord, $complete_log);

                // Data printing in table

                for ($i = 0; $i <= sizeof($complete_log) - 1; $i++) {
                    $html = dashboard_values_output($i + 1, $complete_log[$i][0], $complete_log[$i][1], $complete_log[$i][4], $complete_log[$i][2], $complete_log[$i][3]);
                    print($html);
                }
                $html_table = '</tbody></table></div>';
                print($html_table);
            } // end if/else
            ?>
        </form>
    </div>
            <?php
        }

        /*         * ******************************************************************************************* */
        add_filter('registration_errors', 'xb_nmc_wp_registration_errors_entry_point', 10, 3);
        add_filter('wp_authenticate_user', 'xb_nmc_wp_authenticate_user_errors_entry_point', 10, 2);
        add_filter('bp_core_validate_user_signup', 'xb_nmc_bp_registration_errors_entry_point', 10, 1);
        add_filter('preprocess_comment', 'xb_nmc_wp_comment_form_point', 10, 1);

        function xb_nmc_wp_bot_log_file($user_type, $page_name) {

            $ip = getenv('HTTP_CLIENT_IP')? :
                    getenv('HTTP_X_FORWARDED_FOR')? :
                            getenv('HTTP_X_FORWARDED')? :
                                    getenv('HTTP_FORWARDED_FOR')? :
                                            getenv('HTTP_FORWARDED')? :
                                                    getenv('REMOTE_ADDR');
            $date = current_time('mysql');

            $gi = geoip_open(plugin_dir_path(__FILE__) . '/geoip-api/src/GeoIP.dat', GEOIP_STANDARD);
            // to get country name
            $log_location = geoip_country_name_by_addr($gi, $ip);
            // close the database
            geoip_close($gi);


///////////////////  Update Service log files to maintain weekly and monthly no. of authentications
            $auth_code = get_authentication_code();
            $domain_name = get_domain_name();

            $total_week5 = $total_week4 = $total_week3 = $total_week2 = $total_week1 = 0;
            $month = current_time('m');


            $file_name = current_time('Y-m') . '.txt';
            $com_filename = plugin_dir_path(__FILE__) . 'logs/servicelog/servicelog-' . $file_name;
            $dirname = dirname($com_filename);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }
            $dirname = dirname($com_filename);
            if (is_dir($dirname)) {
                if (($servicelogfile = @fopen(plugin_dir_path(__FILE__) . 'logs/servicelog/servicelog-' . $file_name, "r") ) == FALSE) {
                    $servicelogfile = fopen(plugin_dir_path(__FILE__) . 'logs/servicelog/servicelog-' . $file_name, "w+") or die("Unable to open file!");
                    $servicelogVar = $auth_code . '|' . substr($auth_code, 0, 10) . '|' . $domain_name . '|0|0|0|0|0';
                    fwrite($servicelogfile, $servicelogVar);
                    fclose($servicelogfile);
                }
                $servicelogfile = fopen(plugin_dir_path(__FILE__) . 'logs/servicelog/servicelog-' . $file_name, "r+") or die("Unable to open file!");
                $line = fgets($servicelogfile);
                fclose($servicelogfile);
                $data = explode("|", $line);

                $week_number = ceil(current_time('d') / 7);

                if (sizeof($data) == 9) {
                    switch ($week_number) {
                        case 1:
                            $total_week1 = $data[7] + 1;

                            break;
                        case 2:
                            $total_week1 = $data[7];
                            $total_week2 = $data[6] + 1;

                            break;
                        case 3:
                            $total_week1 = $data[7];
                            $total_week2 = $data[6];
                            $total_week3 = $data[5] + 1;

                            break;
                        case 4:
                            $total_week1 = $data[7];
                            $total_week2 = $data[6];
                            $total_week3 = $data[5];
                            $total_week4 = $data[4] + 1;
                            break;
                        case 5:
                            $total_week1 = $data[7];
                            $total_week2 = $data[6];
                            $total_week3 = $data[5];
                            $total_week4 = $data[4];
                            $total_week5 = $data[3] + 1;
                            break;
                    }
                } else {
                    switch ($week_number) {
                        case 1:
                            $total_week1 = 1;
                            break;
                        case 2:
                            $total_week2 = 1;
                            break;
                        case 3:
                            $total_week3 = 1;
                            break;
                        case 4:
                            $total_week4 = 1;
                            break;
                        case 5:
                            $total_week5 = 1;
                            break;
                    }
                }
                $cur_month_total_auth = $total_week5 + $total_week1 + $total_week2 + $total_week3 + $total_week4;

                $servicelogfile = fopen(plugin_dir_path(__FILE__) . 'logs/servicelog/servicelog-' . $file_name, "w") or die("Unable to open file!");
                $servicelogVar = $auth_code . '|' . substr($auth_code, 0, 10) . '|' . $domain_name . '|' . $total_week5 . '|' . $total_week4 . '|' . $total_week3 . '|' . $total_week2 . '|' . $total_week1 . '|' . $cur_month_total_auth;
                fwrite($servicelogfile, $servicelogVar);
                fclose($servicelogfile);

                /////////////////////////////////////////////////////////////    

                $log_detail = $date . '|' . $ip . '|/' . $page_name . '/|' . $log_location . '|' . $user_type . "\r\n";
                $file_name = current_time('Y-m-d') . '.txt';

                $logfile = fopen(plugin_dir_path(__FILE__) . 'logs/' . $file_name, "a+") or die("Unable to open file!");
                fwrite($logfile, $log_detail);
                fclose($logfile);
            }
        }

        function xb_nmc_wp_authenticate_user_errors_entry_point($user, $password) {
            $errors = new WP_Error();
            if (isset($_SERVER['REQUEST_METHOD'])) {
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    if (!xb_validate($_POST)) {
                        xb_nmc_wp_bot_log_file("bot", "login");
                        $errors->add('nmc_error_1', __('<strong>ERROR: </strong>NMC Error #1', ''));
                        return $errors;
                    } else {
                        xb_nmc_wp_bot_log_file("human", "login");
                    }
                }
            }
            return $user;
        }

        function xb_nmc_wp_registration_errors_entry_point($errors, $user_name, $user_email) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    if (!xb_validate($_POST)) {
                        xb_nmc_wp_bot_log_file("bot", "register");
                        $errors->add('nmc_error_1', __('<strong>ERROR: </strong>NMC Error #1', ''));
                    } else {
                        xb_nmc_wp_bot_log_file("human", "register");
                    }
                }
            }
            return $errors;
        }

        /*         * ******************************************************************************************* */

        function xb_nmc_bp_registration_errors_entry_point($result = array()) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    if (!xb_validate($_POST)) {
                        xb_nmc_wp_bot_log_file("bot", "register-buddypress");
                        $result['errors']->add('user_name', apply_filters('bppj_honeypot_fail_message', __("Invalid Username")));
                        $result['errors']->add('user_email', apply_filters('bppj_honeypot_fail_message', __("Invalid Email Address")));
                    } else {
                        xb_nmc_wp_bot_log_file("human", "register-buddypress");
                    }
                }
            }
            return $result;
        }

        /*         * ******************************************************************************************* */

        function xb_nmc_wp_comment_form_point($comment_data) {
            $errors = new WP_Error();
            if (isset($_SERVER['REQUEST_METHOD'])) {
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    if (!xb_validate($_POST)) {
                        xb_nmc_wp_bot_log_file("bot", "comment-form");
                        $errors->add('nmc_error_1', __('<strong>ERROR: </strong>NMC Error #1', ''));
                        wp_safe_redirect(get_permalink($_POST['comment_post_ID']) . '?xb_bot_error=formError', 302);
                        exit;
                    } else {
                        xb_nmc_wp_bot_log_file("human", "comment-form");
                    }
                }
            }
            return $comment_data;
        }

        /*         * ******************************************************************************************* */
        ?>