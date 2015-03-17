<?php
/**
 * Plugin Name: ActiveDEMAND
 * Plugin URI: https://www.activedemand.com/wp-content/uploads/
 * Description: This adds <a href="https://www.activedemand.com">ActiveDEMAND</a> integration to your website
 * Version: 0.0.1
 * Author: JumpDEMAND Inc.
 * Author URI: https://www.ActiveDEMAND.com                                                   
 * License:GPL-2.0+
 * License URI:http://www.gnu.org/licenses/gpl-2.0.txt
 */

function enqueue_scripts() {
      wp_enqueue_script( 'ActiveDEMAND-Track', 'https://activedemand-static.s3.amazonaws.com/public/javascript/jquery.tracker.compiled.js' );
} 
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' ); 

?>
