<?php
/**
 * Menu Branding Class.
 *
 * Handles sidebar menu renaming.
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
 * MenuBranding Class.
 *
 * @since 1.2.0
 */
class MenuBranding {

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
	 * Rename sidebar menus.
	 *
	 * @return void
	 */
	public function rename_menus() {
		global $menu, $submenu;

		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		$renamed_menus = $this->settings->get( 'menus.renamed', array() );

		if ( empty( $renamed_menus ) || ! is_array( $renamed_menus ) ) {
			return;
		}

		// Rename top-level menus.
		if ( ! empty( $menu ) && is_array( $menu ) ) {
			foreach ( $menu as $key => $menu_item ) {
				// Menu item structure: [0] = name, [1] = capability, [2] = slug, [3] = page title, [4] = classes, [5] = id, [6] = icon
				if ( ! isset( $menu_item[2] ) ) {
					continue;
				}

				$slug = $menu_item[2];

				if ( isset( $renamed_menus[ $slug ] ) && ! empty( $renamed_menus[ $slug ] ) ) {
					// Store original name for reference.
					$original_name = $menu[ $key ][0];
					
					// Replace the menu name, preserving any notification bubbles.
					$new_name = $renamed_menus[ $slug ];
					
					// Check if there's a notification bubble in the original name.
					if ( preg_match( '/<span class="[^"]*awaiting-mod[^"]*"/', $original_name, $matches ) ) {
						// Extract the bubble and append to new name.
						if ( preg_match( '/(<span class="[^"]*awaiting-mod[^"]*"[^>]*>.*?<\/span>)/s', $original_name, $bubble_match ) ) {
							$new_name .= ' ' . $bubble_match[1];
						}
					}
					
					$menu[ $key ][0] = $new_name;
				}
			}
		}
	}
}
