<?php
/**
 * Migration Manager Class.
 *
 * Handles database migrations between plugin versions.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Migration;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\TransientManager;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MigrationManager Class.
 *
 * @since 1.0.0
 */
class MigrationManager {

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * TransientManager instance.
	 *
	 * @var TransientManager
	 */
	private $transient_manager;

	/**
	 * Whether migrations have run this request.
	 *
	 * @var bool
	 */
	private static $has_run = false;

	/**
	 * Constructor.
	 *
	 * @param Settings         $settings          Settings instance.
	 * @param TransientManager $transient_manager TransientManager instance.
	 */
	public function __construct( Settings $settings, TransientManager $transient_manager ) {
		$this->settings          = $settings;
		$this->transient_manager = $transient_manager;
	}

	/**
	 * Run pending migrations if needed.
	 *
	 * @return void
	 */
	public function maybe_run() {
		// Only run once per request.
		if ( self::$has_run ) {
			return;
		}

		self::$has_run = true;

		// Get stored version.
		$stored_version = $this->settings->version();
		$current_version = \Unstoppable\WhiteLabel\Plugin::VERSION;

		// No migration needed if versions match.
		if ( version_compare( $stored_version, $current_version, '>=' ) ) {
			return;
		}

		// Get and run pending migrations.
		$migrations = $this->get_migrations();

		foreach ( $migrations as $version => $callback ) {
			if ( version_compare( $stored_version, $version, '<' ) ) {
				$this->run_migration( $version, $callback );
			}
		}

		// Update version.
		$this->update_version( $current_version );

		// Clear all transients after migration.
		$this->transient_manager->clear_all();

		/**
		 * Fires after all migrations are complete.
		 *
		 * @since 1.0.0
		 *
		 * @param string $stored_version  Previous version.
		 * @param string $current_version New version.
		 */
		do_action( 'uswl_migration_completed', $stored_version, $current_version );
	}

	/**
	 * Get all available migrations.
	 *
	 * @return array Array of version => callback pairs.
	 */
	private function get_migrations() {
		return array(
			// Future migrations will be added here.
			// '1.1.0' => array( $this, 'migrate_to_1_1_0' ),
			// '1.2.0' => array( $this, 'migrate_to_1_2_0' ),
		);
	}

	/**
	 * Run a single migration.
	 *
	 * @param string   $version  Version being migrated to.
	 * @param callable $callback Migration callback.
	 * @return bool True on success, false on failure.
	 */
	private function run_migration( $version, $callback ) {
		// Backup current settings before migration.
		$this->backup_settings( $version );

		try {
			// Run the migration callback.
			if ( is_callable( $callback ) ) {
				call_user_func( $callback );
			}

			return true;
		} catch ( \Exception $e ) {
			// Log the error.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					sprintf(
						'USWL Migration Error (v%s): %s',
						$version,
						$e->getMessage()
					)
				);
			}

			return false;
		}
	}

	/**
	 * Update the stored version.
	 *
	 * @param string $version New version.
	 * @return void
	 */
	private function update_version( $version ) {
		$this->settings->set( 'version', $version );
		$this->settings->save();
	}

	/**
	 * Backup settings before migration.
	 *
	 * @param string $version Version being migrated from.
	 * @return void
	 */
	private function backup_settings( $version ) {
		$backup_key = 'uswl_settings_backup_' . str_replace( '.', '_', $version );
		$settings   = $this->settings->all();

		update_option( $backup_key, $settings, 'no' );
	}

	/**
	 * Restore settings from backup.
	 *
	 * @param string $version Version to restore from.
	 * @return bool True if restored, false if backup not found.
	 */
	public function restore_backup( $version ) {
		$backup_key = 'uswl_settings_backup_' . str_replace( '.', '_', $version );
		$backup     = get_option( $backup_key );

		if ( false === $backup ) {
			return false;
		}

		return $this->settings->save( $backup );
	}

	// =========================================================================
	// MIGRATION METHODS
	// Add new migration methods here as needed.
	// =========================================================================

	/**
	 * Example migration method for version 1.1.0.
	 *
	 * @return void
	 */
	// private function migrate_to_1_1_0() {
	//     // Migration logic here.
	// }
}
