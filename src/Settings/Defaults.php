<?php
/**
 * Settings Defaults Class.
 *
 * Provides default settings values.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Settings;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defaults Class.
 *
 * @since 1.0.0
 */
class Defaults {

	/**
	 * Get the default settings array.
	 *
	 * @return array
	 */
	public static function get() {
		$defaults = array(
			'version' => \Unstoppable\WhiteLabel\Plugin::VERSION,
			'general' => array(
				'enabled'             => false,
				'admin_ids'           => array(),
				'delete_on_uninstall' => false,
			),
			'login'   => array(
				'logo_id'                 => 0,
				'logo_url'                => '',
				'logo_width'              => '',
				'logo_height'             => '',
				'form_position'           => 'center',
				'background_color'        => '',
				'background_image_id'     => 0,
				'background_image_url'    => '',
				'business_name'           => '',
				'business_url'            => '',
				// Hide elements
				'hide_remember_me'        => false,
				'hide_lost_password'      => false,
				'hide_back_to_site'       => false,
				'hide_language_switcher'  => false,
				// Color scheme
				'form_background_color'   => '',
				'form_text_color'         => '',
				'link_color'              => '',
				'button_background_color' => '',
				'button_text_color'       => '',
			),
			'admin'   => array(
				// Admin bar
				'howdy_text'          => '',
				'hide_wp_logo'        => false,
				'hide_site_icon'      => false,
				'custom_logo_id'      => 0,
				'custom_logo_url'     => '',
				'remove_logo_link'    => false,
				'logo_link'           => '',
				// Footer
				'footer_text'         => '',
				'hide_wp_version'     => false,
				// Color scheme
				'enable_color_scheme' => false,
				'menu_background'     => '',
				'menu_text'           => '',
				'menu_highlight'      => '',
				'submenu_background'  => '',
				'submenu_text'        => '',
				'submenu_highlight'   => '',
				'notification_color'  => '',
				'link_color'          => '',
				'button_color'        => '',
			),
			'menus'   => array(
				'renamed'             => array(), // Array of slug => new_name
			),
			'dashboard' => array(
				'hidden_widgets'      => array(), // Array of widget IDs to hide
				'custom_widget_enabled' => false,
				'custom_widget_title' => '',
				'custom_widget_content' => '',
			),
			'visibility' => array(
				'menus'        => array(),
				'adminbar'     => array(),
				'plugins'      => array(),
				'themes'       => array(),
				'hide_notices' => false,
			),
			'tools'   => array(
				'last_export' => null,
			),
		);

		/**
		 * Filters the default settings array.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults Default settings array.
		 */
		return apply_filters( 'uswl_settings_defaults', $defaults );
	}

	/**
	 * Get a single default value using dot notation.
	 *
	 * @param string $key     Setting key in dot notation (e.g., 'login.logo_id').
	 * @param mixed  $default Default value if key not found.
	 * @return mixed
	 */
	public static function get_default( $key, $default = null ) {
		$defaults = self::get();
		$keys     = explode( '.', $key );
		$value    = $defaults;

		foreach ( $keys as $k ) {
			if ( ! isset( $value[ $k ] ) ) {
				return $default;
			}
			$value = $value[ $k ];
		}

		return $value;
	}
}
