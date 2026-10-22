<?php
/**
 * Notification Visibility Class.
 *
 * Handles hiding admin notifications/notices.
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
 * NotificationVisibility Class.
 *
 * @since 1.2.0
 */
class NotificationVisibility {

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
	 * Remove all admin notices.
	 *
	 * @return void
	 */
	public function remove_notices() {
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$hide_notices = $this->settings->get( 'visibility.hide_notices', false );

		if ( ! $hide_notices ) {
			return;
		}

		// Remove all notice hooks.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'network_admin_notices' );
	}

	/**
	 * Enqueue CSS to hide any remaining notices.
	 *
	 * @return void
	 */
	public function enqueue_hide_css() {
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$hide_notices = $this->settings->get( 'visibility.hide_notices', false );

		if ( ! $hide_notices ) {
			return;
		}

		// Register and enqueue inline CSS.
		wp_register_style( 'uswl-hide-notices', false, array(), USWL_VERSION );
		wp_enqueue_style( 'uswl-hide-notices' );

		$css = '
			/* Hide all admin notices */
			.notice:not(.uswl-notice),
			.update-nag,
			.updated:not(.uswl-notice),
			.error:not(.uswl-notice),
			.is-dismissible:not(.uswl-notice),
			#wpbody-content > .notice,
			#wpbody-content > .updated,
			#wpbody-content > .error,
			#wpbody-content > .update-nag {
				display: none !important;
			}
		';

		wp_add_inline_style( 'uswl-hide-notices', $css );
	}
}
