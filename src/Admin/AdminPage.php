<?php
/**
 * Admin Page Class.
 *
 * Handles the plugin settings page in WordPress admin.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Admin;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;
use Unstoppable\WhiteLabel\Plugin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AdminPage Class.
 *
 * @since 1.0.0
 */
class AdminPage {

	/**
	 * Page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'unstoppable-white-label';

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
	 * Register the admin page.
	 *
	 * @return void
	 */
	public function register() {
		add_menu_page(
			__( 'Unstoppable White Label', 'unstoppable-white-label' ),
			__( 'White Label', 'unstoppable-white-label' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render' ),
			'dashicons-admin-appearance',
			80
		);
	}

	/**
	 * Check if we're on the settings page.
	 *
	 * @return bool
	 */
	public function is_settings_page() {
		if ( ! is_admin() ) {
			return false;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}

		return 'toplevel_page_' . self::PAGE_SLUG === $screen->id;
	}

	/**
	 * Get the settings page URL.
	 *
	 * @return string
	 */
	public function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_SLUG );
	}

	/**
	 * Get registered sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'general'    => array(
				'id'       => 'general',
				'title'    => __( 'General', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-admin-generic',
				'priority' => 10,
			),
			'login'      => array(
				'id'       => 'login',
				'title'    => __( 'Login', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-admin-network',
				'priority' => 20,
			),
			'admin'      => array(
				'id'       => 'admin',
				'title'    => __( 'Admin', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-admin-appearance',
				'priority' => 30,
			),
			'dashboard'  => array(
				'id'       => 'dashboard',
				'title'    => __( 'Dashboard', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-dashboard',
				'priority' => 35,
			),
			'visibility' => array(
				'id'       => 'visibility',
				'title'    => __( 'Visibility', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-visibility',
				'priority' => 40,
			),
			'tools'      => array(
				'id'       => 'tools',
				'title'    => __( 'Tools', 'unstoppable-white-label' ),
				'icon'     => 'dashicons-admin-tools',
				'priority' => 50,
			),
		);

		/**
		 * Filters the settings page sections.
		 *
		 * @since 1.0.0
		 *
		 * @param array $sections Array of section definitions.
		 */
		$sections = apply_filters( 'uswl_admin_settings_sections', $sections );

		// Sort by priority.
		uasort(
			$sections,
			function ( $a, $b ) {
				return ( $a['priority'] ?? 50 ) - ( $b['priority'] ?? 50 );
			}
		);

		return $sections;
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render() {
		// Check permissions.
		if ( ! $this->capabilities->can_manage_settings() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'unstoppable-white-label' ) );
		}

		$uswl_settings = $this->settings->all();
		$uswl_sections = $this->get_sections();

		/**
		 * Fires at the top of the settings page.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_admin_page_header' );

		// Include the main template.
		include Plugin::instance()->path( 'templates/admin/settings-page.php' );

		/**
		 * Fires at the bottom of the settings page.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_admin_page_footer' );
	}

	/**
	 * Render a specific section.
	 *
	 * @param string $section_id Section ID.
	 * @param array  $settings   Current settings.
	 * @return void
	 */
	public function render_section( $section_id, $settings ) {
		$template = Plugin::instance()->path( 'templates/admin/section-' . $section_id . '.php' );

		if ( file_exists( $template ) ) {
			include $template;
		}

		/**
		 * Fires when rendering a settings section.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Current settings.
		 */
		do_action( 'uswl_admin_section_' . $section_id, $settings );
	}

	/**
	 * Get available administrators for the WL admin dropdown.
	 *
	 * @return array Array of user data.
	 */
	public function get_available_admins() {
		$users = get_users(
			array(
				'role__in' => array( 'administrator' ),
				'orderby'  => 'display_name',
				'order'    => 'ASC',
			)
		);

		$admins = array();
		$seen_ids = array();
		
		foreach ( $users as $user ) {
			// Skip duplicates (can happen in multisite or with certain plugins).
			if ( in_array( $user->ID, $seen_ids, true ) ) {
				continue;
			}
			$seen_ids[] = $user->ID;
			
			$admins[] = array(
				'id'    => $user->ID,
				'name'  => $user->display_name,
				'email' => $user->user_email,
			);
		}

		return $admins;
	}
}
