<?php
/**
 * Plugin Name: Unstoppable White Label
 * Plugin URI: https://unstoppablesolutions.app/plugins/unstoppable-white-label
 * Description: Remove WordPress branding and customize your admin experience. Replace logos, customize the login page, hide admin menus, and more.
 * Version: 1.2.27
 * Author: Unstoppable Solutions
 * Author URI: https://unstoppablesolutions.app
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unstoppable-white-label
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 7.1.3
 * Requires PHP: 7.4
 *
 * @package Unstoppable_White_Label
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @var string
 */
define( 'USWL_VERSION', '1.2.27' );

/**
 * Minimum WordPress version required.
 *
 * @var string
 */
define( 'USWL_MIN_WP_VERSION', '5.8' );

/**
 * Minimum PHP version required.
 *
 * @var string
 */
define( 'USWL_MIN_PHP_VERSION', '7.4' );

/**
 * Plugin file path.
 *
 * @var string
 */
define( 'USWL_PLUGIN_FILE', __FILE__ );

/**
 * Plugin directory path.
 *
 * @var string
 */
define( 'USWL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 *
 * @var string
 */
define( 'USWL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 *
 * @var string
 */
define( 'USWL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check minimum requirements before loading the plugin.
 *
 * @return bool True if requirements are met, false otherwise.
 */
function uswl_meets_requirements() {
	// Check PHP version.
	if ( version_compare( PHP_VERSION, USWL_MIN_PHP_VERSION, '<' ) ) {
		return false;
	}

	// Check WordPress version.
	if ( version_compare( get_bloginfo( 'version' ), USWL_MIN_WP_VERSION, '<' ) ) {
		return false;
	}

	return true;
}

/**
 * Display admin notice for unmet requirements.
 *
 * @return void
 */
function uswl_requirements_notice() {
	$message = '';

	if ( version_compare( PHP_VERSION, USWL_MIN_PHP_VERSION, '<' ) ) {
		$message = sprintf(
			/* translators: 1: Current PHP version, 2: Required PHP version */
			__( 'Unstoppable White Label requires PHP %2$s or higher. You are running PHP %1$s.', 'unstoppable-white-label' ),
			PHP_VERSION,
			USWL_MIN_PHP_VERSION
		);
	} elseif ( version_compare( get_bloginfo( 'version' ), USWL_MIN_WP_VERSION, '<' ) ) {
		$message = sprintf(
			/* translators: 1: Current WordPress version, 2: Required WordPress version */
			__( 'Unstoppable White Label requires WordPress %2$s or higher. You are running WordPress %1$s.', 'unstoppable-white-label' ),
			get_bloginfo( 'version' ),
			USWL_MIN_WP_VERSION
		);
	}

	if ( $message ) {
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html( $message )
		);
	}
}

/**
 * Initialize the plugin.
 *
 * @return void
 */
function uswl_init() {
	// Check requirements.
	if ( ! uswl_meets_requirements() ) {
		add_action( 'admin_notices', 'uswl_requirements_notice' );
		return;
	}

	// Load autoloader.
	require_once USWL_PLUGIN_DIR . 'includes/autoload.php';

	// Initialize the plugin.
	\Unstoppable\WhiteLabel\Plugin::instance();
}
add_action( 'plugins_loaded', 'uswl_init', 10 );

/**
 * Plugin activation hook.
 *
 * @param bool $network_wide Whether the plugin is being activated network-wide.
 * @return void
 */
function uswl_activate( $network_wide ) {
	// Check requirements before activation.
	if ( ! uswl_meets_requirements() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );

		$message = sprintf(
			/* translators: 1: Required PHP version, 2: Required WordPress version */
			__( 'Unstoppable White Label requires PHP %1$s and WordPress %2$s or higher.', 'unstoppable-white-label' ),
			USWL_MIN_PHP_VERSION,
			USWL_MIN_WP_VERSION
		);

		wp_die(
			esc_html( $message ),
			esc_html__( 'Plugin Activation Error', 'unstoppable-white-label' ),
			array( 'back_link' => true )
		);
	}

	// Load autoloader for activation tasks.
	require_once USWL_PLUGIN_DIR . 'includes/autoload.php';

	// Run activation tasks.
	\Unstoppable\WhiteLabel\Plugin::on_activate( $network_wide );
}
register_activation_hook( __FILE__, 'uswl_activate' );

/**
 * Plugin deactivation hook.
 *
 * @param bool $network_wide Whether the plugin is being deactivated network-wide.
 * @return void
 */
function uswl_deactivate( $network_wide ) {
	// Load autoloader for deactivation tasks.
	require_once USWL_PLUGIN_DIR . 'includes/autoload.php';

	// Run deactivation tasks.
	\Unstoppable\WhiteLabel\Plugin::on_deactivate( $network_wide );
}
register_deactivation_hook( __FILE__, 'uswl_deactivate' );
