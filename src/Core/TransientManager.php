<?php
/**
 * Transient Manager Class.
 *
 * Handles transient caching with proper prefixing.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Core;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TransientManager Class.
 *
 * @since 1.0.0
 */
class TransientManager {

	/**
	 * Transient prefix.
	 *
	 * @var string
	 */
	const PREFIX = 'uswl_';

	/**
	 * Default TTL (1 hour).
	 *
	 * @var int
	 */
	const DEFAULT_TTL = 3600;

	/**
	 * Get a transient value.
	 *
	 * @param string $key Transient key (without prefix).
	 * @return mixed|false Transient value or false if not found.
	 */
	public function get( $key ) {
		return get_transient( self::PREFIX . $key );
	}

	/**
	 * Set a transient value.
	 *
	 * @param string $key   Transient key (without prefix).
	 * @param mixed  $value Value to store.
	 * @param int    $ttl   Time to live in seconds. Default 1 hour.
	 * @return bool True if set, false otherwise.
	 */
	public function set( $key, $value, $ttl = self::DEFAULT_TTL ) {
		return set_transient( self::PREFIX . $key, $value, $ttl );
	}

	/**
	 * Delete a transient.
	 *
	 * @param string $key Transient key (without prefix).
	 * @return bool True if deleted, false otherwise.
	 */
	public function delete( $key ) {
		return delete_transient( self::PREFIX . $key );
	}

	/**
	 * Clear all plugin transients.
	 *
	 * Only deletes transients with our prefix.
	 *
	 * @return int Number of transients deleted.
	 */
	public function clear_all() {
		global $wpdb;

		$count = 0;

		// Get all transients with our prefix.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				'_transient_' . self::PREFIX . '%'
			)
		);

		foreach ( $transients as $transient ) {
			// Remove the '_transient_' prefix to get the actual key.
			$key = str_replace( '_transient_', '', $transient );

			if ( delete_transient( $key ) ) {
				$count++;
			}
		}

		// Also clear timeout entries.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'_transient_timeout_' . self::PREFIX . '%'
			)
		);

		return $count;
	}

	/**
	 * Get multiple transients at once.
	 *
	 * @param array $keys Array of transient keys (without prefix).
	 * @return array Associative array of key => value pairs.
	 */
	public function get_multiple( $keys ) {
		$values = array();

		foreach ( $keys as $key ) {
			$value = $this->get( $key );
			if ( false !== $value ) {
				$values[ $key ] = $value;
			}
		}

		return $values;
	}

	/**
	 * Delete multiple transients at once.
	 *
	 * @param array $keys Array of transient keys (without prefix).
	 * @return int Number of transients deleted.
	 */
	public function delete_multiple( $keys ) {
		$count = 0;

		foreach ( $keys as $key ) {
			if ( $this->delete( $key ) ) {
				$count++;
			}
		}

		return $count;
	}
}
