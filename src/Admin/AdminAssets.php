<?php
/**
 * Admin Assets Class.
 *
 * Handles enqueueing CSS and JavaScript for the admin settings page.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Admin;

use Unstoppable\WhiteLabel\Plugin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AdminAssets Class.
 *
 * @since 1.0.0
 */
class AdminAssets {

	/**
	 * AdminPage instance.
	 *
	 * @var AdminPage
	 */
	private $admin_page;

	/**
	 * Constructor.
	 *
	 * @param AdminPage $admin_page AdminPage instance.
	 */
	public function __construct( AdminPage $admin_page ) {
		$this->admin_page = $admin_page;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue( $hook_suffix ) {
		// Only enqueue on our settings page.
		if ( 'toplevel_page_' . AdminPage::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		$this->enqueue_styles();
		$this->enqueue_scripts();
	}

	/**
	 * Enqueue admin menu brand color CSS.
	 *
	 * This runs on all admin pages, not just the settings page.
	 * Uses wp_add_inline_style() for WordPress.org compliance.
	 *
	 * @return void
	 */
	public function admin_menu_color() {
		// Register an empty stylesheet as anchor for inline styles.
		wp_register_style( 'uswl-menu-branding', false, array(), USWL_VERSION );
		wp_enqueue_style( 'uswl-menu-branding' );

		$uswl_menu_css = '
			/* Unstoppable Solutions brand color for menu text */
			#adminmenu .toplevel_page_unstoppable-white-label .wp-menu-name {
				color: #f0b849 !important;
			}
			#adminmenu .toplevel_page_unstoppable-white-label:hover .wp-menu-name,
			#adminmenu .toplevel_page_unstoppable-white-label.current .wp-menu-name {
				color: #fff !important;
			}
			/* Also color the icon */
			#adminmenu .toplevel_page_unstoppable-white-label .wp-menu-image:before {
				color: #f0b849;
			}
			#adminmenu .toplevel_page_unstoppable-white-label:hover .wp-menu-image:before,
			#adminmenu .toplevel_page_unstoppable-white-label.current .wp-menu-image:before {
				color: #fff;
			}
		';

		wp_add_inline_style( 'uswl-menu-branding', $uswl_menu_css );
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @return void
	 */
	private function enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style(
			'uswl-admin',
			Plugin::instance()->url( 'assets/css/uswl-admin.css' ),
			array( 'wp-color-picker' ),
			Plugin::VERSION
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	private function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script(
			'uswl-admin',
			Plugin::instance()->url( 'assets/js/uswl-admin.js' ),
			array( 'jquery', 'wp-color-picker' ),
			Plugin::VERSION,
			true
		);

		wp_localize_script( 'uswl-admin', 'uswlAdmin', $this->get_script_data() );
	}

	/**
	 * Get localized script data.
	 *
	 * @return array
	 */
	private function get_script_data() {
		return array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'uswl_admin_nonce' ),
			'strings'   => array(
				'saving'           => __( 'Saving...', 'unstoppable-white-label' ),
				'saved'            => __( 'Saved', 'unstoppable-white-label' ),
				'saveSettings'     => __( 'Save Settings', 'unstoppable-white-label' ),
				'error'            => __( 'Error', 'unstoppable-white-label' ),
				'confirmReset'     => __( 'Are you sure you want to reset all settings to defaults? This cannot be undone.', 'unstoppable-white-label' ),
				'confirmImport'    => __( 'This will overwrite your current settings. Continue?', 'unstoppable-white-label' ),
				'selectImage'      => __( 'Select Image', 'unstoppable-white-label' ),
				'changeImage'      => __( 'Change Image', 'unstoppable-white-label' ),
				'removeImage'      => __( 'Remove', 'unstoppable-white-label' ),
				'uploadTitle'      => __( 'Select or Upload Image', 'unstoppable-white-label' ),
				'uploadButton'     => __( 'Use this image', 'unstoppable-white-label' ),
				'unsavedChanges'   => __( 'You have unsaved changes. Leave anyway?', 'unstoppable-white-label' ),
				'exportSuccess'    => __( 'Settings exported successfully.', 'unstoppable-white-label' ),
				'importSuccess'    => __( 'Settings imported successfully.', 'unstoppable-white-label' ),
				'resetSuccess'     => __( 'Settings reset to defaults.', 'unstoppable-white-label' ),
				'invalidFile'      => __( 'Invalid file. Please select a valid JSON export file.', 'unstoppable-white-label' ),
			),
			'limits'    => array(
				'menus'    => $this->get_max_limit( 'menus' ),
				'adminbar' => $this->get_max_limit( 'adminbar' ),
				'plugins'  => $this->get_max_limit( 'plugins' ),
			),
		);
	}

	/**
	 * Get maximum limit for a feature.
	 *
	 * @param string $feature Feature name (menus, adminbar, plugins).
	 * @return int
	 */
	private function get_max_limit( $feature ) {
		// All features are unlimited in free version.
		$uswl_default_limit = -1;

		switch ( $feature ) {
			case 'menus':
				/**
				 * Filters the maximum number of hidden menus.
				 *
				 * @since 1.0.0
				 *
				 * @param int $max Maximum count (-1 for unlimited).
				 */
				return apply_filters( 'uswl_visibility_max_hidden_menus', $uswl_default_limit );

			case 'adminbar':
				/**
				 * Filters the maximum number of hidden admin bar items.
				 *
				 * @since 1.0.0
				 *
				 * @param int $max Maximum count (-1 for unlimited).
				 */
				return apply_filters( 'uswl_visibility_max_hidden_adminbar', $uswl_default_limit );

			case 'plugins':
				/**
				 * Filters the maximum number of hidden plugins.
				 *
				 * @since 1.0.0
				 *
				 * @param int $max Maximum count (-1 for unlimited).
				 */
				return apply_filters( 'uswl_visibility_max_hidden_plugins', $uswl_default_limit );

			default:
				return $uswl_default_limit;
		}
	}
}
