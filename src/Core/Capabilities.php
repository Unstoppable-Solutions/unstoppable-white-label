<?php
/**
 * Capabilities Class.
 *
 * Handles permission checks for white labeling.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Core;

use Unstoppable\WhiteLabel\Settings\Settings;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capabilities Class.
 *
 * @since 1.0.0
 */
class Capabilities {

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Cached white-label admin status.
	 *
	 * @var array
	 */
	private static $wl_admin_cache = array();

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings instance.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Check if a user is a white-label admin (sees real WordPress).
	 *
	 * @param int|null $user_id User ID. Defaults to current user.
	 * @return bool
	 */
	public function is_whitelabel_admin( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Return cached result if available.
		if ( isset( self::$wl_admin_cache[ $user_id ] ) ) {
			return self::$wl_admin_cache[ $user_id ];
		}

		// Not logged in = not a WL admin.
		if ( ! $user_id ) {
			self::$wl_admin_cache[ $user_id ] = false;
			return false;
		}

		// Get configured WL admin IDs.
		$admin_ids = $this->get_admin_users();

		// If no admins configured, nobody is a WL admin.
		// This means everyone sees the white-labeled interface.
		if ( empty( $admin_ids ) ) {
			$is_wl_admin = false;
		} else {
			// Check if user is in the list.
			$is_wl_admin = in_array( $user_id, $admin_ids, true );
		}

		/**
		 * Filters whether a user is a white-label admin.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_wl_admin Whether the user is a WL admin.
		 * @param int  $user_id     User ID being checked.
		 */
		$is_wl_admin = apply_filters( 'uswl_is_whitelabel_admin', $is_wl_admin, $user_id );

		// Cache the result.
		self::$wl_admin_cache[ $user_id ] = $is_wl_admin;

		return $is_wl_admin;
	}

	/**
	 * Check if white labeling should apply to the current request.
	 *
	 * @return bool
	 */
	public function should_apply() {
		// Must be enabled.
		if ( ! $this->settings->is_enabled() ) {
			return false;
		}

		// Must be logged in.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// Check for preview mode - if active, apply branding even for WL admins.
		if ( $this->is_preview_mode() ) {
			return true;
		}

		// WL admins see real WordPress.
		if ( $this->is_whitelabel_admin() ) {
			return false;
		}

		/**
		 * Filters whether white labeling should apply.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $should_apply Whether branding should apply.
		 */
		return apply_filters( 'uswl_should_apply_branding', true );
	}

	/**
	 * Check if preview mode is active.
	 *
	 * @return bool
	 */
	public function is_preview_mode() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		$preview_until = get_user_meta( $user_id, 'uswl_preview_mode', true );
		
		// Check if preview mode is active and not expired (10 minute limit).
		if ( $preview_until && time() < (int) $preview_until ) {
			return true;
		}

		// Clean up expired preview mode.
		if ( $preview_until ) {
			delete_user_meta( $user_id, 'uswl_preview_mode' );
		}

		return false;
	}

	/**
	 * Enable preview mode for current user.
	 *
	 * @return bool
	 */
	public function enable_preview_mode() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		// Set preview mode for 10 minutes.
		$expires = time() + ( 10 * MINUTE_IN_SECONDS );
		return update_user_meta( $user_id, 'uswl_preview_mode', $expires );
	}

	/**
	 * Disable preview mode for current user.
	 *
	 * @return bool
	 */
	public function disable_preview_mode() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		return delete_user_meta( $user_id, 'uswl_preview_mode' );
	}

	/**
	 * Get the list of white-label admin user IDs.
	 *
	 * @return array
	 */
	public function get_admin_users() {
		$admin_ids = $this->settings->get( 'general.admin_ids', array() );

		// Ensure it's an array of integers.
		if ( ! is_array( $admin_ids ) ) {
			$admin_ids = array();
		}

		return array_map( 'absint', $admin_ids );
	}

	/**
	 * Check if a user can manage plugin settings.
	 *
	 * @param int|null $user_id User ID. Defaults to current user.
	 * @return bool
	 */
	public function can_manage_settings( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Must be able to manage options.
		$can_manage = user_can( $user_id, 'manage_options' );

		/**
		 * Filters whether a user can manage plugin settings.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $can_manage Whether the user can manage settings.
		 * @param int  $user_id    User ID being checked.
		 */
		return apply_filters( 'uswl_can_manage_settings', $can_manage, $user_id );
	}

	/**
	 * Clear the cached white-label admin status.
	 *
	 * @param int|null $user_id User ID to clear, or null to clear all.
	 * @return void
	 */
	public function clear_cache( $user_id = null ) {
		if ( null === $user_id ) {
			self::$wl_admin_cache = array();
		} else {
			unset( self::$wl_admin_cache[ $user_id ] );
		}
	}
}
