<?php
/**
 * Theme Visibility Class.
 *
 * Handles hiding themes from the themes list.
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
 * ThemeVisibility Class.
 *
 * @since 1.2.0
 */
class ThemeVisibility {

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
	 * Get available themes.
	 *
	 * @return array
	 */
	public function get_available() {
		$themes = wp_get_themes();
		$available = array();

		foreach ( $themes as $stylesheet => $theme ) {
			$available[ $stylesheet ] = $theme->get( 'Name' );
		}

		// Sort alphabetically by name.
		asort( $available );

		return $available;
	}

	/**
	 * Filter themes to hide selected ones.
	 *
	 * @param array $themes All themes.
	 * @return array
	 */
	public function filter_themes( $themes ) {
		if ( ! $this->capabilities->should_apply() ) {
			return $themes;
		}

		$hidden = $this->settings->get( 'visibility.themes', array() );

		if ( empty( $hidden ) || ! is_array( $hidden ) ) {
			return $themes;
		}

		foreach ( $hidden as $stylesheet ) {
			// Don't hide the active theme.
			if ( $stylesheet === get_stylesheet() ) {
				continue;
			}
			unset( $themes[ $stylesheet ] );
		}

		return $themes;
	}
}
