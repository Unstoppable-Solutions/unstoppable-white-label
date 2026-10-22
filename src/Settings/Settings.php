<?php
/**
 * Settings Class.
 *
 * Handles loading, saving, and managing plugin settings.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Settings;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Class.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Option name in the database.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'uswl_settings';

	/**
	 * Cached settings array.
	 *
	 * @var array|null
	 */
	private static $cache = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Settings are lazy-loaded on first access.
	}

	/**
	 * Check if settings exist in the database.
	 *
	 * @return bool
	 */
	public function exists() {
		return false !== get_option( self::OPTION_NAME );
	}

	/**
	 * Create default settings in the database.
	 *
	 * @return bool
	 */
	public function create() {
		$defaults = Defaults::get();
		return add_option( self::OPTION_NAME, $defaults, '', 'yes' );
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public function all() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$settings = get_option( self::OPTION_NAME, array() );
		$defaults = Defaults::get();

		// Merge with defaults to ensure all keys exist.
		$settings = $this->merge_recursive( $defaults, $settings );

		/**
		 * Filters the settings after loading from the database.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Settings array.
		 */
		self::$cache = apply_filters( 'uswl_settings_after_load', $settings );

		return self::$cache;
	}

	/**
	 * Get a single setting value using dot notation.
	 *
	 * @param string $key     Setting key in dot notation (e.g., 'login.logo_id').
	 * @param mixed  $default Default value if key not found.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$settings = $this->all();
		$keys     = explode( '.', $key );
		$value    = $settings;

		foreach ( $keys as $k ) {
			if ( ! isset( $value[ $k ] ) ) {
				return $default;
			}
			$value = $value[ $k ];
		}

		return $value;
	}

	/**
	 * Set a single setting value using dot notation.
	 *
	 * @param string $key   Setting key in dot notation.
	 * @param mixed  $value Value to set.
	 * @return void
	 */
	public function set( $key, $value ) {
		$settings = $this->all();
		$keys     = explode( '.', $key );
		$current  = &$settings;

		foreach ( $keys as $i => $k ) {
			if ( $i === count( $keys ) - 1 ) {
				$current[ $k ] = $value;
			} else {
				if ( ! isset( $current[ $k ] ) || ! is_array( $current[ $k ] ) ) {
					$current[ $k ] = array();
				}
				$current = &$current[ $k ];
			}
		}

		self::$cache = $settings;
	}

	/**
	 * Save settings to the database.
	 *
	 * @param array|null $settings Optional. Settings to save. Uses cached settings if null.
	 * @return bool True on success (always succeeds for valid array input).
	 */
	public function save( $settings = null ) {
		if ( null === $settings ) {
			$settings = $this->all();
		}

		$old_settings = get_option( self::OPTION_NAME, array() );

		/**
		 * Filters settings before saving to the database.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings     Settings to save.
		 * @param array $old_settings Previous settings.
		 */
		$settings = apply_filters( 'uswl_settings_before_save', $settings, $old_settings );

		// update_option returns false if value unchanged, but that's not an error.
		update_option( self::OPTION_NAME, $settings );

		// Clear cache.
		self::$cache = null;

		/**
		 * Fires after settings are saved.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings     New settings.
		 * @param array $old_settings Previous settings.
		 */
		do_action( 'uswl_settings_saved', $settings, $old_settings );

		return true;
	}

	/**
	 * Reset settings to defaults.
	 *
	 * @return bool
	 */
	public function reset() {
		$old_settings = $this->all();
		$defaults     = Defaults::get();

		self::$cache = null;
		$result      = update_option( self::OPTION_NAME, $defaults );

		if ( $result ) {
			/**
			 * Fires after settings are reset to defaults.
			 *
			 * @since 1.0.0
			 *
			 * @param array $old_settings Settings before reset.
			 */
			do_action( 'uswl_settings_reset', $old_settings );
		}

		return $result;
	}

	/**
	 * Check if white labeling is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return (bool) $this->get( 'general.enabled', false );
	}

	/**
	 * Get the settings version.
	 *
	 * @return string
	 */
	public function version() {
		return $this->get( 'version', '1.0.0' );
	}

	/**
	 * Export settings as an array.
	 *
	 * @return array
	 */
	public function export() {
		$settings = $this->all();

		// Add export metadata.
		$data = array(
			'_meta'    => array(
				'plugin_version' => \Unstoppable\WhiteLabel\Plugin::VERSION,
				'exported_at'    => gmdate( 'Y-m-d\TH:i:s\Z' ),
				'site_url'       => home_url(),
			),
			'settings' => $settings,
		);

		// Remove internal version from export.
		unset( $data['settings']['version'] );

		/**
		 * Filters the settings export data.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data Export data array.
		 */
		return apply_filters( 'uswl_settings_export_data', $data );
	}

	/**
	 * Import settings from an array.
	 *
	 * @param array $data Import data array.
	 * @return bool|\WP_Error True on success, WP_Error on failure.
	 */
	public function import( $data ) {
		// Validate structure.
		if ( ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
			return new \WP_Error(
				'invalid_import_data',
				__( 'Invalid import data structure.', 'unstoppable-white-label' )
			);
		}

		$source_version = isset( $data['_meta']['plugin_version'] ) ? $data['_meta']['plugin_version'] : '1.0.0';

		/**
		 * Filters the settings import data.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $data           Import data.
		 * @param string $source_version Version that created the export.
		 */
		$data = apply_filters( 'uswl_settings_import_data', $data, $source_version );

		// Merge with defaults and current settings.
		$defaults     = Defaults::get();
		$new_settings = $this->merge_recursive( $defaults, $data['settings'] );

		// Preserve current version.
		$new_settings['version'] = \Unstoppable\WhiteLabel\Plugin::VERSION;

		$result = $this->save( $new_settings );

		if ( $result ) {
			/**
			 * Fires after settings are imported.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $new_settings   Imported settings.
			 * @param string $source_version Version that created the export.
			 */
			do_action( 'uswl_settings_imported', $new_settings, $source_version );
		}

		return $result;
	}

	/**
	 * Recursively merge arrays.
	 *
	 * @param array $defaults Default values.
	 * @param array $settings User settings.
	 * @return array
	 */
	private function merge_recursive( $defaults, $settings ) {
		$merged = $defaults;

		foreach ( $settings as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				// Check if it's a sequential array (list) or associative.
				if ( array_keys( $merged[ $key ] ) === range( 0, count( $merged[ $key ] ) - 1 ) ) {
					// Sequential array - replace entirely.
					$merged[ $key ] = $value;
				} else {
					// Associative array - merge recursively.
					$merged[ $key ] = $this->merge_recursive( $merged[ $key ], $value );
				}
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Clear the settings cache.
	 *
	 * @return void
	 */
	public function clear_cache() {
		self::$cache = null;
	}
}
