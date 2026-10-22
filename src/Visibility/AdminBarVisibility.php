<?php
/**
 * Admin Bar Visibility Class.
 *
 * Handles hiding admin bar items.
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
 * AdminBarVisibility Class.
 *
 * @since 1.0.0
 */
class AdminBarVisibility {

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
	 * Transient key for available items.
	 *
	 * @var string
	 */
	const TRANSIENT_KEY = 'available_adminbar';

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
	 * Get the maximum number of admin bar items that can be hidden.
	 *
	 * @return int -1 for unlimited.
	 */
	public function get_max_hidden() {
		/**
		 * Filters the maximum number of hidden admin bar items.
		 *
		 * @since 1.0.0
		 *
		 * @param int $max Maximum count (default 5, -1 for unlimited).
		 */
		return apply_filters( 'uswl_visibility_max_hidden_adminbar', 5 );
	}

	/**
	 * Cache available admin bar items.
	 *
	 * Called at priority 9998 to capture items before they're hidden.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function cache_available( $wp_admin_bar ) {
		$nodes = $wp_admin_bar->get_nodes();

		if ( empty( $nodes ) ) {
			return;
		}

		$items = array();

		foreach ( $nodes as $node ) {
			// Skip sub-items (those with parents).
			if ( ! empty( $node->parent ) ) {
				continue;
			}

			// Skip items without titles.
			if ( empty( $node->title ) ) {
				continue;
			}

			$label = wp_strip_all_tags( $node->title );

			// Skip empty labels.
			if ( empty( trim( $label ) ) ) {
				continue;
			}

			$items[ $node->id ] = $label;
		}

		// Cache for 1 hour.
		$this->transient_manager->set( self::TRANSIENT_KEY, $items, HOUR_IN_SECONDS );
	}

	/**
	 * Hide configured admin bar items.
	 *
	 * Called at priority 9999 after caching.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function hide_items( $wp_admin_bar ) {
		// Only hide if branding should apply.
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$items_to_hide = $this->settings->get( 'visibility.adminbar', array() );

		if ( empty( $items_to_hide ) || ! is_array( $items_to_hide ) ) {
			return;
		}

		$hidden = array();

		foreach ( $items_to_hide as $node_id ) {
			$wp_admin_bar->remove_node( $node_id );
			$hidden[] = $node_id;
		}
	}

	/**
	 * Get available admin bar items.
	 *
	 * @return array Array of items [node_id => label].
	 */
	public function get_available() {
		// Try to get from transient first.
		$cached = $this->transient_manager->get( self::TRANSIENT_KEY );
		if ( false !== $cached && is_array( $cached ) ) {
			return $this->apply_available_filter( $cached );
		}

		// Return default common items if no cache.
		$items = $this->get_default_items();

		return $this->apply_available_filter( $items );
	}

	/**
	 * Get default admin bar items.
	 *
	 * These are common items that exist on most WordPress installs.
	 *
	 * @return array
	 */
	private function get_default_items() {
		return array(
			'wp-logo'      => __( 'WordPress Logo', 'unstoppable-white-label' ),
			'site-name'    => __( 'Site Name', 'unstoppable-white-label' ),
			'comments'     => __( 'Comments', 'unstoppable-white-label' ),
			'new-content'  => __( 'New', 'unstoppable-white-label' ),
			'updates'      => __( 'Updates', 'unstoppable-white-label' ),
			'search'       => __( 'Search', 'unstoppable-white-label' ),
			'my-account'   => __( 'My Account', 'unstoppable-white-label' ),
		);
	}

	/**
	 * Apply the available items filter.
	 *
	 * @param array $items Available items.
	 * @return array
	 */
	private function apply_available_filter( $items ) {
		/**
		 * Filters the available admin bar items shown in settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $items Array of items [node_id => label].
		 */
		return apply_filters( 'uswl_visibility_available_adminbar', $items );
	}
}
