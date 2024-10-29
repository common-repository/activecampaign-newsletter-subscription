<?php
/**
 * Plugin Name: ActiveCampaign Newsletter Subscription
 * Plugin URI:  https://bhargavb.com/
 * Description: This Plugin Used to Add User's Email to ActiveCampaign List.
 * Version:     1.0.2
 * Author:      Bili Plugins
 * Text Domain: ac-newsletter
 * Domain Path: /languages
 * Author URI:  https://biliplugins.com/
 *
 * @package     Activecampaign_Newsletter_Subscription
 */

/**
 * Defining Constants.
 *
 * @package    Activecampaign_Newsletter_Subscription
 */
if ( ! defined( 'ACNS_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'ACNS_VERSION', '1.0.2' );
}

if ( ! defined( 'ACNS_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'ACNS_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ACNS_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'ACNS_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'ACNS_BASE_NAME' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'ACNS_BASE_NAME', plugin_basename( __FILE__ ) );
}
/**
 * Apply transaltion file as per WP language.
 */
function acns_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = ACNS_PATH . 'languages/' . get_locale() . '.mo';

	// If file does not exists, then apply default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = ACNS_PATH . 'languages/default.mo';
	}

	load_textdomain( 'ac-newsletter', $mofile );
}

add_action( 'plugins_loaded', 'acns_text_domain_loader' );

/**
 * Setting link for plugin.
 *
 * @param  array $links Array of plugin setting link.
 * @return array
 */
function acns_setting_page_link( $links ) {

	$settings_link = sprintf(
		'<a href="%1$s">%2$s</a> | <a href="%3$s" target="_blank">%4$s</a>',
		esc_url( admin_url( 'admin.php?page=ac-newsletter' ) ),
		esc_html__( 'Settings', 'ac-newsletter' ),
		esc_url( 'https://checkout.freemius.com/mode/dialog/plugin/10500/plan/17747/' ),
		esc_html__( 'Go Pro', 'ac-newsletter' )
	);

	array_unshift( $links, $settings_link );
	return $links;
}

add_filter( 'plugin_action_links_' . ACNS_BASE_NAME, 'acns_setting_page_link' );

require ACNS_PATH . '/app/admin/class-activecampaign-newsletter-subscription-admin.php';
require ACNS_PATH . '/app/main/class-activecampaign-newsletter-subscription.php';
require ACNS_PATH . '/app/includes/common-functions.php';
