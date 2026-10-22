<?php
/**
 * AJAX Controller Class.
 *
 * Handles all AJAX requests for the admin settings page.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Admin;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;
use Unstoppable\WhiteLabel\Core\TransientManager;
use Unstoppable\WhiteLabel\Plugin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AjaxController Class.
 *
 * @since 1.0.0
 */
class AjaxController {

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
	 * Verify AJAX request.
	 *
	 * @return bool|\WP_Error True if valid, WP_Error otherwise.
	 */
	private function verify_request() {
		// Verify nonce.
		if ( ! check_ajax_referer( 'uswl_admin_nonce', 'nonce', false ) ) {
			return new \WP_Error(
				'invalid_nonce',
				__( 'Security check failed. Please refresh the page and try again.', 'unstoppable-white-label' )
			);
		}

		// Check permissions.
		if ( ! $this->capabilities->can_manage_settings() ) {
			return new \WP_Error(
				'permission_denied',
				__( 'You do not have permission to perform this action.', 'unstoppable-white-label' )
			);
		}

		return true;
	}

	/**
	 * Send error response.
	 *
	 * @param \WP_Error|string $error Error object or message.
	 * @return void
	 */
	private function send_error( $error ) {
		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = $error;
		}

		wp_send_json_error( array( 'message' => $message ) );
	}

	/**
	 * Handle settings save.
	 *
	 * @return void
	 */
	public function handle_save() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
			return;
		}

		// Get settings - sanitize the JSON string first, then decode and sanitize the array.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
		$raw_settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();

		if ( is_string( $raw_settings ) ) {
			// Decode JSON string to array.
			$raw_settings = json_decode( $raw_settings, true );
			
			// Check JSON decode error.
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$this->send_error( 'JSON decode error: ' . json_last_error_msg() );
				return;
			}
		}

		if ( ! is_array( $raw_settings ) || empty( $raw_settings ) ) {
			$this->send_error( 'Invalid settings data. Received: ' . gettype( $raw_settings ) );
			return;
		}

		// Sanitize all settings values.
		$sanitized = $this->sanitize_settings( $raw_settings );

		// Save settings.
		$this->settings->save( $sanitized );

		// Clear transients.
		$this->transient_manager->clear_all();

		$response = array(
			'message'  => __( 'Settings saved successfully.', 'unstoppable-white-label' ),
			'settings' => $sanitized,
		);

		/**
		 * Filters the AJAX save response.
		 *
		 * @since 1.0.0
		 *
		 * @param array $response  Response data.
		 * @param array $sanitized Saved settings.
		 */
		$response = apply_filters( 'uswl_ajax_save_response', $response, $sanitized );

		wp_send_json_success( $response );
	}

	/**
	 * Handle settings export.
	 *
	 * @return void
	 */
	public function handle_export() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$export_data = $this->settings->export();

		wp_send_json_success(
			array(
				'message' => __( 'Settings exported successfully.', 'unstoppable-white-label' ),
				'data'    => $export_data,
			)
		);
	}

	/**
	 * Handle settings import.
	 *
	 * @return void
	 */
	public function handle_import() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		// Get import data.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
		$import_data = isset( $_POST['import_data'] ) ? sanitize_text_field( wp_unslash( $_POST['import_data'] ) ) : '';

		if ( is_string( $import_data ) && ! empty( $import_data ) ) {
			$import_data = json_decode( $import_data, true );
		}

		if ( ! is_array( $import_data ) ) {
			$this->send_error( __( 'Invalid import data.', 'unstoppable-white-label' ) );
		}

		// Sanitize all imported data.
		$import_data = $this->sanitize_import_data( $import_data );

		$result = $this->settings->import( $import_data );

		if ( is_wp_error( $result ) ) {
			$this->send_error( $result );
		}

		// Clear transients.
		$this->transient_manager->clear_all();

		wp_send_json_success(
			array(
				'message'  => __( 'Settings imported successfully.', 'unstoppable-white-label' ),
				'settings' => $this->settings->all(),
			)
		);
	}

	/**
	 * Handle settings reset.
	 *
	 * @return void
	 */
	public function handle_reset() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$result = $this->settings->reset();

		if ( ! $result ) {
			$this->send_error( __( 'Failed to reset settings.', 'unstoppable-white-label' ) );
		}

		// Clear transients.
		$this->transient_manager->clear_all();

		wp_send_json_success(
			array(
				'message'  => __( 'Settings reset to defaults.', 'unstoppable-white-label' ),
				'settings' => $this->settings->all(),
			)
		);
	}

	/**
	 * Handle get menus request.
	 *
	 * @return void
	 */
	public function handle_get_menus() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$visibility = Plugin::instance()->loader()->get_module( 'menu_visibility' );
		$menus      = $visibility ? $visibility->get_available() : array();

		wp_send_json_success( array( 'menus' => $menus ) );
	}

	/**
	 * Handle get admin bar items request.
	 *
	 * @return void
	 */
	public function handle_get_adminbar() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$visibility = Plugin::instance()->loader()->get_module( 'adminbar_visibility' );
		$items      = $visibility ? $visibility->get_available() : array();

		wp_send_json_success( array( 'items' => $items ) );
	}

	/**
	 * Handle get plugins request.
	 *
	 * @return void
	 */
	public function handle_get_plugins() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$visibility = Plugin::instance()->loader()->get_module( 'plugin_visibility' );
		$plugins    = $visibility ? $visibility->get_available() : array();

		wp_send_json_success( array( 'plugins' => $plugins ) );
	}

	/**
	 * Handle get widgets request.
	 *
	 * @return void
	 */
	public function handle_get_widgets() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		$visibility = Plugin::instance()->loader()->get_module( 'widget_visibility' );
		$widgets    = $visibility ? $visibility->get_available() : array();

		wp_send_json_success( array( 'widgets' => $widgets ) );
	}

	/**
	 * Sanitize import data array.
	 *
	 * Sanitizes both settings and _meta portions of import data.
	 *
	 * @param array $data Raw import data from JSON.
	 * @return array Sanitized import data.
	 */
	private function sanitize_import_data( $data ) {
		$sanitized = array();

		// Sanitize settings portion.
		if ( isset( $data['settings'] ) && is_array( $data['settings'] ) ) {
			$sanitized['settings'] = $this->sanitize_settings( $data['settings'] );
		}

		// Sanitize _meta portion.
		if ( isset( $data['_meta'] ) && is_array( $data['_meta'] ) ) {
			$sanitized['_meta'] = array();

			if ( isset( $data['_meta']['plugin_version'] ) ) {
				$sanitized['_meta']['plugin_version'] = sanitize_text_field( $data['_meta']['plugin_version'] );
			}

			if ( isset( $data['_meta']['export_date'] ) ) {
				$sanitized['_meta']['export_date'] = sanitize_text_field( $data['_meta']['export_date'] );
			}

			if ( isset( $data['_meta']['site_url'] ) ) {
				$sanitized['_meta']['site_url'] = esc_url_raw( $data['_meta']['site_url'] );
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize settings array.
	 *
	 * @param array $raw Raw settings from POST.
	 * @return array Sanitized settings.
	 */
	private function sanitize_settings( $raw ) {
		$current   = $this->settings->all();
		$sanitized = $current;

		// General settings.
		if ( isset( $raw['general'] ) ) {
			$sanitized['general']['enabled'] = ! empty( $raw['general']['enabled'] );
			$sanitized['general']['delete_on_uninstall'] = ! empty( $raw['general']['delete_on_uninstall'] );

			if ( isset( $raw['general']['admin_ids'] ) && is_array( $raw['general']['admin_ids'] ) ) {
				$sanitized['general']['admin_ids'] = array_map( 'absint', $raw['general']['admin_ids'] );
			}
		}

		// Login settings.
		if ( isset( $raw['login'] ) ) {
			$sanitized['login']['logo_id'] = isset( $raw['login']['logo_id'] ) ? absint( $raw['login']['logo_id'] ) : 0;
			$sanitized['login']['logo_url'] = isset( $raw['login']['logo_url'] ) ? esc_url_raw( $raw['login']['logo_url'] ) : '';
			$sanitized['login']['background_color'] = isset( $raw['login']['background_color'] ) ? sanitize_hex_color( $raw['login']['background_color'] ) : '#f0f0f1';
			$sanitized['login']['background_image_id'] = isset( $raw['login']['background_image_id'] ) ? absint( $raw['login']['background_image_id'] ) : 0;
			$sanitized['login']['background_image_url'] = isset( $raw['login']['background_image_url'] ) ? esc_url_raw( $raw['login']['background_image_url'] ) : '';
			$sanitized['login']['business_name'] = isset( $raw['login']['business_name'] ) ? sanitize_text_field( $raw['login']['business_name'] ) : '';
			$sanitized['login']['business_url'] = isset( $raw['login']['business_url'] ) ? esc_url_raw( $raw['login']['business_url'] ) : '';
		}

		// Admin settings.
		if ( isset( $raw['admin'] ) ) {
			$sanitized['admin']['footer_text'] = isset( $raw['admin']['footer_text'] ) ? sanitize_text_field( $raw['admin']['footer_text'] ) : '';
			$sanitized['admin']['howdy_text'] = isset( $raw['admin']['howdy_text'] ) ? sanitize_text_field( $raw['admin']['howdy_text'] ) : '';
			$sanitized['admin']['hide_wp_logo'] = ! empty( $raw['admin']['hide_wp_logo'] );
			$sanitized['admin']['hide_site_icon'] = ! empty( $raw['admin']['hide_site_icon'] );
			$sanitized['admin']['custom_logo_id'] = isset( $raw['admin']['custom_logo_id'] ) ? absint( $raw['admin']['custom_logo_id'] ) : 0;
			$sanitized['admin']['custom_logo_url'] = isset( $raw['admin']['custom_logo_url'] ) ? esc_url_raw( $raw['admin']['custom_logo_url'] ) : '';
		}

		// Visibility settings with limits.
		if ( isset( $raw['visibility'] ) ) {
			// Hidden menus.
			if ( isset( $raw['visibility']['hidden_menus'] ) && is_array( $raw['visibility']['hidden_menus'] ) ) {
				$max_menus = apply_filters( 'uswl_visibility_max_hidden_menus', 5 );
				$menus     = array_map( 'sanitize_text_field', $raw['visibility']['hidden_menus'] );
				if ( $max_menus > 0 ) {
					$menus = array_slice( $menus, 0, $max_menus );
				}
				$sanitized['visibility']['hidden_menus'] = $menus;
			}

			// Hidden admin bar items.
			if ( isset( $raw['visibility']['hidden_admin_bar'] ) && is_array( $raw['visibility']['hidden_admin_bar'] ) ) {
				$max_adminbar = apply_filters( 'uswl_visibility_max_hidden_adminbar', 5 );
				$items        = array_map( 'sanitize_text_field', $raw['visibility']['hidden_admin_bar'] );
				if ( $max_adminbar > 0 ) {
					$items = array_slice( $items, 0, $max_adminbar );
				}
				$sanitized['visibility']['hidden_admin_bar'] = $items;
			}

			// Hidden plugins.
			if ( isset( $raw['visibility']['hidden_plugins'] ) && is_array( $raw['visibility']['hidden_plugins'] ) ) {
				$max_plugins = apply_filters( 'uswl_visibility_max_hidden_plugins', 5 );
				$plugins     = array_map( 'sanitize_text_field', $raw['visibility']['hidden_plugins'] );
				if ( $max_plugins > 0 ) {
					$plugins = array_slice( $plugins, 0, $max_plugins );
				}
				$sanitized['visibility']['hidden_plugins'] = $plugins;
			}

			// Hidden widgets.
			if ( isset( $raw['visibility']['hidden_widgets'] ) && is_array( $raw['visibility']['hidden_widgets'] ) ) {
				$sanitized['visibility']['hidden_widgets'] = array_map( 'sanitize_text_field', $raw['visibility']['hidden_widgets'] );
			}
		}

		return $sanitized;
	}

	/**
	 * Handle preview mode toggle.
	 *
	 * @return void
	 */
	public function handle_preview_mode() {
		$verified = $this->verify_request();
		if ( is_wp_error( $verified ) ) {
			$this->send_error( $verified );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
		$action = isset( $_POST['preview_action'] ) ? sanitize_text_field( wp_unslash( $_POST['preview_action'] ) ) : 'enable';

		$capabilities = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'capabilities' );
		
		if ( ! $capabilities ) {
			$this->send_error( __( 'Could not access capabilities.', 'unstoppable-white-label' ) );
		}

		if ( 'enable' === $action ) {
			$capabilities->enable_preview_mode();
			wp_send_json_success( array(
				'message' => __( 'Preview mode enabled for 10 minutes.', 'unstoppable-white-label' ),
				'reload'  => true,
			) );
		} else {
			$capabilities->disable_preview_mode();
			wp_send_json_success( array(
				'message' => __( 'Preview mode disabled.', 'unstoppable-white-label' ),
				'reload'  => true,
			) );
		}
	}
}
