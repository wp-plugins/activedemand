<?php
/**
 * Plugin Name: ActiveDEMAND
 * Plugin URI: https://www.activedemand.com/
 * Description: This adds <a href="https://www.activedemand.com">ActiveDEMAND</a> integration to your website
 * Version: 0.0.4
 * Author: JumpDEMAND Inc.
 * Author URI: https://www.ActiveDEMAND.com                                                   
 * License:GPL-2.0+
 * License URI:http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**=================================================
 * Admin Menu
 ===================================================*/


function activedemand_menu() {
    global $activedemand_plugin_hook;
    $activedemand_plugin_hook = add_options_page('ActiveDEMAND options', 'ActiveDEMAND', 'manage_options', 'activedemand_options', 'activedemand_plugin_options');
    add_action( 'admin_init', 'register_activedemand_settings' );

}
function register_activedemand_settings() {
    register_setting( 'activedemand_options', 'activedemand_options_field' );
}
function enqueue_scripts() {
      wp_enqueue_script( 'ActiveDEMAND-Track', 'https://activedemand-static.s3.amazonaws.com/public/javascript/jquery.tracker.compiled.js.gz' );
}
function activedemand_getHTML($url,$timeout)
{
    $ch = curl_init($url); // initialize curl with given url
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
    return @curl_exec($ch);
}
function activedemand_process_form_shortcode( $atts,$content=null ) {
//[activedemand_form id='123']

    //$id exists after this call.
    extract(shortcode_atts( array('id' => ''), $atts));
    $options = get_option('activedemand_options_field');
    $activedemand_appkey = $options["activedemand_appkey"];

    $form_str="";
    if (is_numeric($id)) {
        //get form html
        $form_str = activedemand_getHTML("https://api.activedemand.com/v1/forms/".$id."?api-key=".$activedemand_appkey."", 4000);

    }


    return $form_str;
}
function activedemand_plugin_action_links($links, $file) {
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

function activedemand_plugin_options() {

    ?>
    <div class="wrap">
        <img src="<?php echo get_base_url() ?>/images/ActiveDEMAND-Transparent.png"/> <h2>Settings</h2> <br/>
        <p>
        The <a href="https://www.ActiveDEMAND.com">ActiveDEMAND</a> plugin adds a tracking script to your WordPress pages. As well this plugin offers the ability to use form shortcodes on your pages, posts, and sidebars that will render an ActiveDEMAND Web Form. This allows you to maintain your form styling and configuration within ActiveDEMAND
            and keep your WordPress site in sync with your ActiveDEMAND account<br/>
            <p>
            The format of the shortcode is <strong>[activedemand_form id="&lt;id&gt;"]</strong>.</p>
        </p>
        <p>You can find the form ID for your ActiveDEMAND Web Form on the Web Form section of ActiveDEMAND:<br/>
            <p>
            <img src="<?php echo get_base_url() ?>/images/Screenshot1.png"/>
            </p>
        </p>
        <br/>
        <p>
            You will need to enter your application key in order to enable the form shortcodes. Your can find your ActiveDEMAND API key in your account settings:
        <p>
            <img src="<?php echo get_base_url() ?>/images/Screenshot2.png"/>
        </p>
        </p>
        <form method="post" action="options.php">
            <?php
            wp_nonce_field('update-options');
            settings_fields( 'activedemand_options' );
            $options = get_option('activedemand_options_field');
            $activedemand_appkey = $options["activedemand_appkey"];
            ?>


            <table class="form-table">

                <tr><td colspan="2"><h3>ActiveDEMAND API Key</h3></td></tr>
                <tr valign="top">
                    <th scope="row">key</th>
                    <td><input type='text'  name="activedemand_options_field[activedemand_appkey]" size='42' value="<?php echo $activedemand_appkey;?>"  /></td>
                </tr>
                <tr><td></td><td>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}
function activedemand_plugin_help($text, $screen_id, $screen) {
    global $activedemand_plugin_hook;
    if ($screen_id == $activedemand_plugin_hook) {

        $text = "<h5>Need help with the ActiveDEMAND plugin?</h5>";
        $text .= "<p>Check out the documentation and support forums for help with this plugin.</p>";
        $text .= "<a href=\"http://wordpress.org/extend/plugins/activedemand/installation/\">Documentation</a><br /><a href=\"http://wordpress.org/tags/activedemand?forum_id=10\">Support forums</a><br /><a href=\"https://support.activedemand.com\">ActiveDEMAND Support portal</a>";

    }
    return $text;
}
function get_base_url(){
    return plugins_url(null, __FILE__);
}

add_filter('contextual_help', 'activedemand_plugin_help', 10, 3);
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
add_shortcode('activedemand_form', 'activedemand_process_form_shortcode');
add_action('admin_menu', 'activedemand_menu');
add_filter('plugin_action_links', 'activedemand_plugin_action_links', 10, 2);
?>
