<?php
/**
 * Plugin Name: ActiveDEMAND
 * Plugin URI: https://www.activedemand.com/
 * Description: Adds the <a href="https://www.activedemand.com">ActiveDEMAND</a> tracking script to your website. As well this plugin gives you the ability to use shortcodes to embed ActiveDEMAND webforms into your widgets, pages, posts, and sidebars.
 * Version: 0.0.6
 * Author: JumpDEMAND Inc.
 * Author URI: https://www.ActiveDEMAND.com
 * License:GPL-2.0+
 * License URI:http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**=================================================
 * Admin Menu
 * ===================================================*/
include 'partials/helper-functions.php';

function activedemand_menu()
{
    global $activedemand_plugin_hook;
    $activedemand_plugin_hook = add_options_page('ActiveDEMAND options', 'ActiveDEMAND', 'manage_options', 'activedemand_options', 'activedemand_plugin_options');
    add_action('admin_init', 'register_activedemand_settings');

}

function register_activedemand_settings()
{
    register_setting('activedemand_options', 'activedemand_options_field');
}


function activedemand_enqueue_scripts()
{
    wp_enqueue_script('ActiveDEMAND-Track', 'https://activedemand-static.s3.amazonaws.com/public/javascript/jquery.tracker.compiled.js.gz');
}

function activedemand_admin_enqueue_scripts()
{
    global $pagenow;

    if ('post.php' == $pagenow || 'post-new.php' == $pagenow) {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');

    }
}


function activedemand_process_form_shortcode($atts, $content = null)
{
//[activedemand_form id='123']

    //$id exists after this call.
    extract(shortcode_atts(array('id' => ''), $atts));
    $options = get_option('activedemand_options_field');
    $activedemand_appkey = $options["activedemand_appkey"];


    if (is_numeric($id)) {
        $form_str = activedemand_getHTML("https://api.activedemand.com/v1/forms/" . $id . "?api-key=" . $activedemand_appkey . "", 4000);
    }


    return $form_str;
}

function activedemand_plugin_action_links($links, $file)
{
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=activedemand_options">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function activedemand_plugin_options()
{
    $options = get_option('activedemand_options_field');
    $activedemand_appkey = $options["activedemand_appkey"];

    ?>
    <div class="wrap">
        <img src="<?php echo get_base_url() ?>/images/ActiveDEMAND-Transparent.png"/>

        <h1>Settings</h1>

        <h2>Your ActiveDEMAND Account</h2><br/>
        You will require an ActiveDEMAND account to use this plugin. With an ActiveDEMAND account you will be able
        to:<br/>
        <ul style="list-style-type:circle;  margin-left: 50px;">
            <li>Build Webforms for your pages, posts, sidebars, etc</li>
            <li>Automatically send emails to those who fill out your web forms</li>
            <li>Automatically send emails to you when a form is filled out</li>
            <li>Send email campaigns to your subscribers</li>
        </ul>
        <?php if ("" == $activedemand_appkey || !isset($activedemand_appkey)) { ?>
            <div>
                <h3>To sign up for your ActiveDEMAND account, click <a
                        href="http://1jp.cc/s/vaiXT"><strong>here</strong></a>
                </h3>

                <p>
                    You will need to enter your application key in order to enable the form shortcodes. Your can find
                    your
                    ActiveDEMAND API key in your account settings:

                </p>

                <p>
                    <img src="<?php echo get_base_url() ?>/images/Screenshot2.png"/>
                </p>
            </div>
        <?php } ?>
        <form method="post" action="options.php">
            <?php
            wp_nonce_field('update-options');
            settings_fields('activedemand_options');
            $options = get_option('activedemand_options_field');
            $activedemand_appkey = $options["activedemand_appkey"];
            ?>

            <h3>ActiveDEMAND API Key</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">key</th>
                    <td><input style="width:400px" type='text' name="activedemand_options_field[activedemand_appkey]"
                               value="<?php echo $activedemand_appkey; ?>"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                        </p>
                    </td>
                </tr>
            </table>
        </form>

        <?php if ("" != $activedemand_appkey) { ?>
            <div>

                <h2>Using ActiveDEMAND Web Forms</h2>

                <p> The <a href="https://www.ActiveDEMAND.com">ActiveDEMAND</a> plugin adds a tracking script to your
                    WordPress
                    pages. As well this plugin offers the ability to use form shortcodes on your pages, posts, and
                    sidebars
                    that
                    will render an ActiveDEMAND Web Form. This allows you to maintain your form styling and
                    configuration
                    within
                    ActiveDEMAND
                    and keep your WordPress site in sync with your ActiveDEMAND account.
                </p>

                <?php
                $url = "https://api.activedemand.com/v1/forms.xml?api-key=" . $activedemand_appkey . "";
                $str = activedemand_getHTML($url, 9000);
                $xml = simplexml_load_string($str);
                ?>
                <?php if ("" != $xml) { ?>
                    <h3>Available Web Form Short Codes</h3>

                    <style scoped="scoped" type="text/css">
                        table#shrtcodetbl {
                            border: 1px solid black;
                        }

                        table#shrtcodetbl tr {
                            background-color: #ffffff;
                        }

                        table#shrtcodetbl tr:nth-child(even) {
                            background-color: #eeeeee;
                        }

                        table#shrtcodetbl tr td {
                            padding: 10px;
                        }

                        table#shrtcodetbl th {
                            color: white;
                            background-color: black;
                            padding: 10px;
                        }
                    </style>
                    <table id="shrtcodetbl">
                        <tr>
                            <th>Form Name</th>
                            <th>Shortcode</th>
                        </tr>
                        <?php
                        foreach ($xml->children() as $child) {
                            echo "<tr><td>";
                            echo $child->name;
                            echo "</td>";
                            echo "<td>[activedemand_form id='";
                            echo $child->id;
                            echo "']</td>";
                        }
                        ?>
                    </table>
                    <p>
                        In your visual editor, look for the 'Insert ActiveDEMAND Shortcut' button:<br/>
                        <img
                            src="<?php echo get_base_url() ?>/images/Screenshot3.png"/>.
                    </p>
                <?php } else { ?>
                    <h2>No Web Forms Configured</h2>
                    <p>To use the ActiveDEMAND web form shortcodes, you will first have to add some webforms to your
                        account in ActiveDEMAND. Once you do have webforms configured, the available shortcodes will be
                        displayed here.</p>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php
}

function activedemand_plugin_help($text, $screen_id, $screen)
{
    global $activedemand_plugin_hook;
    if ($screen_id == $activedemand_plugin_hook) {

        $text = "<h5>Need help with the ActiveDEMAND plugin?</h5>";
        $text .= "<p>Check out the documentation and support forums for help with this plugin.</p>";
        $text .= "<a href=\"http://wordpress.org/extend/plugins/activedemand/installation/\">Documentation</a><br /><a href=\"http://wordpress.org/tags/activedemand?forum_id=10\">Support forums</a><br /><a href=\"https://support.activedemand.com\">ActiveDEMAND Support portal</a>";

    }
    return $text;
}

function get_base_url()
{
    return plugins_url(null, __FILE__);
}

function activedemand_register_tinymce_javascript($plugin_array)
{
    $plugin_array['activedemand'] = plugins_url('/js/tinymce-plugin.js', __FILE__);
    return $plugin_array;
}


function activedemand_buttons()
{
    add_filter("mce_external_plugins", "activedemand_add_buttons");
    add_filter('mce_buttons', 'activedemand_register_buttons');
}

function activedemand_add_buttons($plugin_array)
{
    $plugin_array['activedemand'] = get_base_url() . '/includes/activedemand-plugin.js';
    return $plugin_array;
}

function activedemand_register_buttons($buttons)
{
    array_push($buttons, 'insert_form_shortcode');
    return $buttons;
}


function add_editor()
{

    global $pagenow;

    // Add html for shortcodes popup
    if ('post.php' == $pagenow || 'post-new.php' == $pagenow) {
        include 'partials/tinymce-editor.php';
    }

}


add_filter('contextual_help', 'activedemand_plugin_help', 10, 3);
add_action('wp_enqueue_scripts', 'activedemand_enqueue_scripts');

add_action('admin_enqueue_scripts', 'activedemand_admin_enqueue_scripts');


add_shortcode('activedemand_form', 'activedemand_process_form_shortcode');
add_action('admin_menu', 'activedemand_menu');
add_filter('plugin_action_links', 'activedemand_plugin_action_links', 10, 2);


//widgets
// add new buttons
add_action('init', 'activedemand_buttons');
add_action('in_admin_footer', 'add_editor');

?>
