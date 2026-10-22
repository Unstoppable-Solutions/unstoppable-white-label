<?php
/**
 * Widget Visibility Class.
 *
 * Handles hiding dashboard widgets.
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
 * WidgetVisibility Class.
 *
 * @since 1.0.0
 */
class WidgetVisibility {

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
	 * Core WordPress dashboard widgets.
	 *
	 * @var array
	 */
	const CORE_WIDGETS = array(
		'dashboard_site_health'       => 'Site Health Status',
		'dashboard_right_now'         => 'At a Glance',
		'dashboard_activity'          => 'Activity',
		'dashboard_quick_press'       => 'Quick Draft',
		'dashboard_primary'           => 'WordPress Events and News',
	);

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
	 * Hide configured dashboard widgets.
	 *
	 * @return void
	 */
	public function hide_widgets() {
		// Only hide if branding should apply.
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$widgets_to_hide = $this->settings->get( 'visibility.hidden_widgets', array() );

		if ( empty( $widgets_to_hide ) || ! is_array( $widgets_to_hide ) ) {
			return;
		}

		/**
		 * Fires before dashboard widgets are hidden.
		 *
		 * @since 1.0.0
		 *
		 * @param array $widgets_to_hide Array of widget IDs to hide.
		 */
		do_action( 'uswl_before_hide_widgets', $widgets_to_hide );

		$hidden = array();

		foreach ( $widgets_to_hide as $widget_id ) {
			// Try all possible contexts.
			remove_meta_box( $widget_id, 'dashboard', 'normal' );
			remove_meta_box( $widget_id, 'dashboard', 'side' );
			remove_meta_box( $widget_id, 'dashboard', 'column3' );
			remove_meta_box( $widget_id, 'dashboard', 'column4' );

			$hidden[] = $widget_id;
		}

		/**
		 * Fires after dashboard widgets are hidden.
		 *
		 * @since 1.0.0
		 *
		 * @param array $hidden Array of widget IDs that were hidden.
		 */
		do_action( 'uswl_after_hide_widgets', $hidden );
	}

	/**
	 * Get available dashboard widgets.
	 *
	 * @return array Array of widgets [widget_id => label].
	 */
	public function get_available() {
		// Start with core widgets.
		$widgets = $this->get_core_widgets();

		return $this->apply_available_filter( $widgets, true );
	}

	/**
	 * Get core WordPress dashboard widgets.
	 *
	 * @return array
	 */
	public function get_core_widgets() {
		$widgets = array();

		foreach ( self::CORE_WIDGETS as $id => $label ) {
			$widgets[ $id ] = $label;
		}

		return $widgets;
	}

	/**
	 * Apply the available widgets filter.
	 *
	 * @param array $widgets Available widgets.
	 * @param bool  $is_core Whether requesting core widgets only.
	 * @return array
	 */
	private function apply_available_filter( $widgets, $is_core = true ) {
		/**
		 * Filters the available dashboard widgets shown in settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $widgets Array of widgets [widget_id => label].
		 * @param bool  $is_core Whether requesting core widgets only.
		 */
		return apply_filters( 'uswl_visibility_available_widgets', $widgets, $is_core );
	}
}
