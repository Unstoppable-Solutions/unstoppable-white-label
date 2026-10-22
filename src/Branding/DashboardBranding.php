<?php
/**
 * Dashboard Branding Class.
 *
 * Handles dashboard widget hiding and custom widget.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Branding;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DashboardBranding Class.
 *
 * @since 1.2.0
 */
class DashboardBranding {

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
	 * Remove dashboard widgets.
	 *
	 * @return void
	 */
	public function remove_widgets() {
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$hidden_widgets = $this->settings->get( 'dashboard.hidden_widgets', array() );

		if ( empty( $hidden_widgets ) || ! is_array( $hidden_widgets ) ) {
			return;
		}

		// Map of widget IDs to their meta box locations.
		$widget_locations = array(
			// Core widgets - normal column.
			'dashboard_right_now'     => array( 'dashboard', 'normal' ),
			'dashboard_activity'      => array( 'dashboard', 'normal' ),
			'dashboard_quick_press'   => array( 'dashboard', 'side' ),
			'dashboard_primary'       => array( 'dashboard', 'side' ),
			'dashboard_site_health'   => array( 'dashboard', 'normal' ),
			
			// WooCommerce.
			'woocommerce_dashboard_status' => array( 'dashboard', 'normal' ),
			'woocommerce_dashboard_recent_reviews' => array( 'dashboard', 'normal' ),
			
			// Jetpack.
			'jetpack_summary_widget'  => array( 'dashboard', 'normal' ),
			
			// Elementor.
			'elementor_overview'      => array( 'dashboard', 'normal' ),
			'e-dashboard-overview'    => array( 'dashboard', 'normal' ),
			
			// Security.
			'wordfence_activity_report' => array( 'dashboard', 'normal' ),
			
			// Google Site Kit.
			'google_sitekit_widget'   => array( 'dashboard', 'normal' ),
			'google-site-kit'         => array( 'dashboard', 'normal' ),
			
			// SEO.
			'yoast_db_widget'         => array( 'dashboard', 'normal' ),
			'wpseo-dashboard-overview' => array( 'dashboard', 'normal' ),
			'rank_math_dashboard_widget' => array( 'dashboard', 'normal' ),
			
			// Forms.
			'wpforms_reports_widget'  => array( 'dashboard', 'normal' ),
			
			// Analytics.
			'monsterinsights_reports_widget' => array( 'dashboard', 'normal' ),
			
			// Email.
			'fluent_smtp_widget'      => array( 'dashboard', 'normal' ),
			'fluent-smtp-dashboard-widget' => array( 'dashboard', 'normal' ),
			
			// Backup.
			'updraftplus_widget'      => array( 'dashboard', 'normal' ),
			
			// AI.
			'ai_capabilities_widget'  => array( 'dashboard', 'normal' ),
			'ai_status_widget'        => array( 'dashboard', 'normal' ),
		);

		foreach ( $hidden_widgets as $widget_id ) {
			// Try both normal and side locations.
			remove_meta_box( $widget_id, 'dashboard', 'normal' );
			remove_meta_box( $widget_id, 'dashboard', 'side' );
			remove_meta_box( $widget_id, 'dashboard', 'column3' );
			remove_meta_box( $widget_id, 'dashboard', 'column4' );
		}
	}

	/**
	 * Register custom dashboard widget.
	 *
	 * @return void
	 */
	public function register_custom_widget() {
		$enabled = $this->settings->get( 'dashboard.custom_widget_enabled', false );

		if ( ! $enabled ) {
			return;
		}

		$title = $this->settings->get( 'dashboard.custom_widget_title', '' );

		if ( empty( $title ) ) {
			$title = __( 'Welcome', 'unstoppable-white-label' );
		}

		wp_add_dashboard_widget(
			'uswl_custom_widget',
			esc_html( $title ),
			array( $this, 'render_custom_widget' )
		);

		// Move widget to top.
		global $wp_meta_boxes;
		
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['uswl_custom_widget'] ) ) {
			$widget = $wp_meta_boxes['dashboard']['normal']['core']['uswl_custom_widget'];
			unset( $wp_meta_boxes['dashboard']['normal']['core']['uswl_custom_widget'] );
			
			// Add to beginning of high priority.
			$wp_meta_boxes['dashboard']['normal']['high'] = array_merge(
				array( 'uswl_custom_widget' => $widget ),
				$wp_meta_boxes['dashboard']['normal']['high'] ?? array()
			);
		}
	}

	/**
	 * Render custom dashboard widget.
	 *
	 * @return void
	 */
	public function render_custom_widget() {
		$content = $this->settings->get( 'dashboard.custom_widget_content', '' );

		if ( empty( $content ) ) {
			echo '<p>' . esc_html__( 'Welcome to your dashboard!', 'unstoppable-white-label' ) . '</p>';
			return;
		}

		// Output content with allowed HTML.
		echo wp_kses_post( wpautop( $content ) );
	}
}
