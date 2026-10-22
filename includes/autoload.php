<?php
/**
 * PSR-4 Autoloader for Unstoppable White Label.
 *
 * @package Unstoppable_White_Label
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	/**
	 * Autoload classes in the Unstoppable\WhiteLabel namespace.
	 *
	 * @param string $class_name The fully-qualified class name.
	 * @return void
	 */
	function ( $class_name ) {
		// Namespace prefix.
		$prefix = 'Unstoppable\\WhiteLabel\\';

		// Base directory for the namespace prefix.
		$base_dir = USWL_PLUGIN_DIR . 'src/';

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			// No, move to the next registered autoloader.
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class_name, $len );

		// Replace the namespace separator with directory separator.
		// Append .php extension.
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);
