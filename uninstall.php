<?php
/**
 * Uninstall handler.
 *
 * Removes all plugin data when the plugin is uninstalled.
 *
 * @package Unstoppable_White_Label
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Clean up plugin data.
 *
 * Only deletes data if the user has opted in via settings.
 */
function uswl_uninstall_cleanup() {
	// Get settings to check if user wants data deleted.
	$settings = get_option( 'uswl_settings', array() );

	// Only proceed if user has enabled delete on uninstall.
	if ( empty( $settings['general']['delete_on_uninstall'] ) ) {
		return;
	}

	global $wpdb;

	// Delete main settings.
	delete_option( 'uswl_settings' );

	// Delete installation timestamp.
	delete_option( 'uswl_installed_at' );

	// Delete any backup settings.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		"DELETE FROM {$wpdb->options} 
		WHERE option_name LIKE 'uswl_settings_backup_%'"
	);

	// Delete all transients.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		"DELETE FROM {$wpdb->options} 
		WHERE option_name LIKE '_transient_uswl_%' 
		OR option_name LIKE '_transient_timeout_uswl_%'"
	);

	// Clear any cached data.
	wp_cache_flush();
}

uswl_uninstall_cleanup();
