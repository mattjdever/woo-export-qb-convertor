<?php
/**
 * @link              http://code.tutsplus.com/tutorials/adding-custom-fields-to-simple-products-with-woocommerce--cms-27904
 * @package           CWF
 *
 * @wordpress-plugin
 * Plugin Name:       OSC WooCommerce Accounting Fields
 * Plugin URI:        http://siteapeel.com/plugins/woo-acct-fields
 * Description:       Adds Fields for Exporting Orders with Accounting Fields
 * Version:           0.2.57
 * Author:            Matthew Dever
 * Author URI:        http://siteapeel.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'WPINC' ) || die;
$pluginfo=get_plugin_data(__FILE__);
$OSCWOE_VERSION = $pluginfo['Version'];

include_once 'admin/class-woo-acct-fields-field.php';
include_once 'admin/class-woo-acct-fields-menu.php';
include_once 'admin/class-woo-acct-fields-exports.php';
//include_once 'public/class-woo-acct-fields-display.php';

add_action( 'plugins_loaded', 'waf_wc_input_start' );
/**
 * Start the plugin.
 */
function waf_wc_input_start() {

    if ( is_admin() ) {

        $admin = new Woo_Acct_Fields_Item( 'waf_item' );
        $admin->init();
    } else {

    }
}
