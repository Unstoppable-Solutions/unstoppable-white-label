<?php
/**
 * Menu Visibility Class.
 *
 * Handles hiding sidebar menus in the admin.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Visibility;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;
use Unstoppable\WhiteLabel\Core\TransientManager;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MenuVisibility Class.
 *
 * @since 1.0.0
 */
class MenuVisibility {

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
	 * TransientManager instance.
	 *
	 * @var TransientManager
	 */
	private $transient_manager;

	/**
	 * Transient key for available menus.
	 *
	 * @var string
	 */
	const TRANSIENT_KEY = 'available_menus';

	/**
	 * Constructor.
	 *
	 * @param Settings         $settings          Settings instance.
	 * @param Capabilities     $capabilities      Capabilities instance.
	 * @param TransientManager $transient_manager TransientManager instance.
	 */
	public function __construct( Settings $settings, Capabilities $capabilities, TransientManager $transient_manager ) {
		$this->settings          = $settings;
		$this->capabilities      = $capabilities;
		$this->transient_manager = $transient_manager;
	}

	/**
	 * Get the maximum number of menus that can be hidden.
	 *
	 * The free version imposes no limit. This matches
	 * AdminAssets::get_max_limit(), which feeds the same figure to the
	 * settings screen through the same filter.
	 *
	 * @return int -1 for unlimited.
	 */
	public function get_max_hidden() {
		/**
		 * Filters the maximum number of hidden menus.
		 *
		 * @since 1.0.0
		 *
		 * @param int $max Maximum count (-1 for unlimited).
		 */
		return apply_filters( 'uswl_visibility_max_hidden_menus', -1 );
	}

	/**
	 * Hide configured menus.
	 *
	 * @return void
	 */
	public function hide_menus() {
		// Always cache available menus for the settings page.
		$this->cache_available();

		// Only hide if branding should apply.
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$menus_to_hide = $this->settings->get( 'visibility.menus', array() );

		if ( empty( $menus_to_hide ) || ! is_array( $menus_to_hide ) ) {
			return;
		}

		/**
		 * Filters the menus to hide before applying.
		 *
		 * @since 1.0.0
		 *
		 * @param array $menus_to_hide Array of menu slugs to hide.
		 */
		$menus_to_hide = apply_filters( 'uswl_visibility_hidden_menus', $menus_to_hide );

		/**
		 * Fires before menus are hidden.
		 *
		 * @since 1.0.0
		 *
		 * @param array $menus_to_hide Array of menu slugs to hide.
		 */
		do_action( 'uswl_before_hide_menus', $menus_to_hide );

		$hidden = array();

		// Never hide our own menu, under any circumstances. Hiding it removes
		// the only discoverable route back into the settings screen, leaving
		// the admin locked out until they recall the direct page URL. This was
		// previously guarded only while preview mode was active.
		$uswl_own_menu = \Unstoppable\WhiteLabel\Admin\AdminPage::PAGE_SLUG;

		foreach ( $menus_to_hide as $menu_slug ) {
			if ( $uswl_own_menu === $menu_slug ) {
				continue;
			}

			remove_menu_page( $menu_slug );
			$hidden[] = $menu_slug;
		}

		/**
		 * Fires after menus are hidden.
		 *
		 * @since 1.0.0
		 *
		 * @param array $hidden Array of menu slugs that were hidden.
		 */
		do_action( 'uswl_after_hide_menus', $hidden );
	}

	/**
	 * Get available menus.
	 *
	 * @return array Array of menu items [slug => label].
	 */
	public function get_available() {
		// Try to get from transient first.
		$cached = $this->transient_manager->get( self::TRANSIENT_KEY );
		if ( false !== $cached && is_array( $cached ) ) {
			return $this->apply_available_filter( $cached );
		}

		// Build from global menu.
		$menus = $this->build_available();

		return $this->apply_available_filter( $menus );
	}

	/**
	 * Cache available menus.
	 *
	 * Should be called during admin_menu to capture the full menu.
	 *
	 * @return void
	 */
	public function cache_available() {
		global $menu;

		if ( empty( $menu ) ) {
			return;
		}

		$menus = $this->build_available();

		// Cache for 1 hour.
		$this->transient_manager->set( self::TRANSIENT_KEY, $menus, HOUR_IN_SECONDS );
	}

	/**
	 * Build available menus from global $menu.
	 *
	 * @return array
	 */
	private function build_available() {
		global $menu;

		$menus = array();

		if ( empty( $menu ) ) {
			return $menus;
		}

		foreach ( $menu as $item ) {
			// Skip separators.
			if ( empty( $item[0] ) || empty( $item[2] ) ) {
				continue;
			}

			$slug = $item[2];
			// Clean up the label (remove HTML and counts).
			$label = wp_strip_all_tags( $item[0] );
			$label = preg_replace( '/\s*\d+\s*$/', '', $label ); // Remove trailing numbers.

			$menus[ $slug ] = $label;
		}

		// Sort alphabetically by label.
		asort( $menus );

		return $menus;
	}

	/**
	 * Apply the available menus filter.
	 *
	 * @param array $menus Available menus.
	 * @return array
	 */
	private function apply_available_filter( $menus ) {
		/**
		 * Filters the available menus shown in settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $menus Array of menu items [slug => label].
		 */
		return apply_filters( 'uswl_visibility_available_menus', $menus );
	}
}
