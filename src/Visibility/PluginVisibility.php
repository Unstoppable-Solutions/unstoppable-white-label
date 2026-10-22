<?php
/**
 * Plugin Visibility Class.
 *
 * Handles hiding plugins from the plugins list.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Visibility;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PluginVisibility Class.
 *
 * @since 1.0.0
 */
class PluginVisibility {

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Capabilities instance.
	 *
	 * @var Capabilities
	 */
	private $capabilities;

	/**
	 * Constructor.
	 *
	 * @param Settings     $settings     Settings instance.
	 * @param Capabilities $capabilities Capabilities instance.
	 */
	public function __construct( Settings $settings, Capabilities $capabilities ) {
		$this->settings     = $settings;
		$this->capabilities = $capabilities;
	}

	/**
	 * Get the maximum number of plugins that can be hidden.
	 *
	 * @return int -1 for unlimited.
	 */
	public function get_max_hidden() {
		/**
		 * Filters the maximum number of hidden plugins.
		 *
		 * @since 1.0.0
		 *
		 * @param int $max Maximum count (default 5, -1 for unlimited).
		 */
		return apply_filters( 'uswl_visibility_max_hidden_plugins', 5 );
	}

	/**
	 * Filter the plugins list.
	 *
	 * @param array $plugins All plugins.
	 * @return array Filtered plugins.
	 */
	public function filter_plugins( $plugins ) {
		// Only filter if branding should apply.
		if ( ! $this->capabilities->should_apply() ) {
			return $plugins;
		}

		$plugins_to_hide = $this->settings->get( 'visibility.plugins', array() );

		if ( empty( $plugins_to_hide ) || ! is_array( $plugins_to_hide ) ) {
			return $plugins;
		}

		/**
		 * Filters the plugins to hide before applying.
		 *
		 * @since 1.0.0
		 *
		 * @param array $plugins_to_hide Array of plugin basenames to hide.
		 */
		$plugins_to_hide = apply_filters( 'uswl_visibility_hidden_plugins', $plugins_to_hide );

		// Never hide this plugin itself.
		$plugins_to_hide = array_diff( $plugins_to_hide, array( USWL_PLUGIN_BASENAME ) );

		/**
		 * Fires before plugins are filtered.
		 *
		 * @since 1.0.0
		 *
		 * @param array $plugins_to_hide Array of plugin basenames to hide.
		 */
		do_action( 'uswl_before_filter_plugins', $plugins_to_hide );

		$hidden  = array();
		$visible = array();

		foreach ( $plugins as $basename => $plugin_data ) {
			if ( in_array( $basename, $plugins_to_hide, true ) ) {
				$hidden[] = $basename;
			} else {
				$visible[ $basename ] = $plugin_data;
			}
		}

		/**
		 * Fires after plugins are filtered.
		 *
		 * @since 1.0.0
		 *
		 * @param array $hidden  Array of plugin basenames that were hidden.
		 * @param array $visible Remaining visible plugins.
		 */
		do_action( 'uswl_after_filter_plugins', $hidden, $visible );

		return $visible;
	}

	/**
	 * Get available plugins.
	 *
	 * @return array Array of plugins [basename => name].
	 */
	public function get_available() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();
		$plugins     = array();

		foreach ( $all_plugins as $basename => $plugin_data ) {
			// Don't allow hiding this plugin.
			if ( USWL_PLUGIN_BASENAME === $basename ) {
				continue;
			}

			$plugins[ $basename ] = $plugin_data['Name'];
		}

		// Sort alphabetically by name.
		asort( $plugins );

		return $this->apply_available_filter( $plugins );
	}

	/**
	 * Apply the available plugins filter.
	 *
	 * @param array $plugins Available plugins.
	 * @return array
	 */
	private function apply_available_filter( $plugins ) {
		/**
		 * Filters the available plugins shown in settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $plugins Array of plugins [basename => name].
		 */
		return apply_filters( 'uswl_visibility_available_plugins', $plugins );
	}
}
