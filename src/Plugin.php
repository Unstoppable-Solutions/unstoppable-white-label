<?php
/**
 * Main Plugin Class.
 *
 * The singleton container that bootstraps all plugin functionality.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = USWL_VERSION;

	/**
	 * Minimum WordPress version.
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '5.8';

	/**
	 * Minimum PHP version.
	 *
	 * @var string
	 */
	const MIN_PHP_VERSION = '7.4';

	/**
	 * Single instance of the class.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Loader instance.
	 *
	 * @var Loader|null
	 */
	private $loader = null;

	/**
	 * Settings instance.
	 *
	 * @var Settings\Settings|null
	 */
	private $settings = null;

	/**
	 * Whether the plugin has been initialized.
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Get the single instance of the class.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Private to enforce singleton pattern.
	 */
	private function __construct() {
		// Set instance before init to prevent recursion when init_modules() calls instance().
		self::$instance = $this;
		$this->init();
	}

	/**
	 * Prevent cloning.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization.
	 *
	 * @return void
	 * @throws \Exception If attempting to unserialize.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	private function init() {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;

		// Initialize the loader.
		$this->loader = new Loader();

		// Register all hooks.
		$this->loader->register();

		// Initialize modules.
		$this->loader->init_modules();

		/**
		 * Fires when the plugin is fully loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_loaded' );
	}

	/**
	 * Get the main plugin file path.
	 *
	 * @return string
	 */
	public function file() {
		return USWL_PLUGIN_FILE;
	}

	/**
	 * Get the plugin directory path.
	 *
	 * @param string $relative_path Optional. Relative path to append.
	 * @return string
	 */
	public function path( $relative_path = '' ) {
		return USWL_PLUGIN_DIR . ltrim( $relative_path, '/' );
	}

	/**
	 * Get the plugin directory URL.
	 *
	 * @param string $relative_path Optional. Relative path to append.
	 * @return string
	 */
	public function url( $relative_path = '' ) {
		return USWL_PLUGIN_URL . ltrim( $relative_path, '/' );
	}

	/**
	 * Get the Settings instance.
	 *
	 * @return Settings\Settings
	 */
	public function settings() {
		if ( null === $this->settings ) {
			$this->settings = new Settings\Settings();
		}

		return $this->settings;
	}

	/**
	 * Get the Loader instance.
	 *
	 * @return Loader
	 */
	public function loader() {
		return $this->loader;
	}

	/**
	 * Check if the plugin meets all requirements.
	 *
	 * @return bool
	 */
	public function meets_requirements() {
		// Check PHP version.
		if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '<' ) ) {
			return false;
		}

		// Check WordPress version.
		if ( version_compare( get_bloginfo( 'version' ), self::MIN_WP_VERSION, '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Plugin activation handler.
	 *
	 * @param bool $network_wide Whether the plugin is being activated network-wide.
	 * @return void
	 */
	public static function on_activate( $network_wide ) {
		// Ensure settings exist with defaults.
		$settings = new Settings\Settings();
		if ( ! $settings->exists() ) {
			$settings->create();
		}

		// Set installation time if not set.
		if ( ! get_option( 'uswl_installed_at' ) ) {
			add_option( 'uswl_installed_at', time(), '', 'no' );
		}

		/**
		 * Fires when the plugin is activated.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $network_wide Whether the plugin is being activated network-wide.
		 */
		do_action( 'uswl_activated', $network_wide );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * @param bool $network_wide Whether the plugin is being deactivated network-wide.
	 * @return void
	 */
	public static function on_deactivate( $network_wide ) {
		// Clear transients.
		$transient_manager = new Core\TransientManager();
		$transient_manager->clear_all();

		/**
		 * Fires when the plugin is deactivated.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $network_wide Whether the plugin is being deactivated network-wide.
		 */
		do_action( 'uswl_deactivated', $network_wide );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}
}
