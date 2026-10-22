<?php
/**
 * Visibility Manager Class.
 *
 * Facade for accessing all visibility modules.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Visibility;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * VisibilityManager Class.
 *
 * @since 1.0.0
 */
class VisibilityManager {

	/**
	 * MenuVisibility instance.
	 *
	 * @var MenuVisibility
	 */
	private $menu_visibility;

	/**
	 * AdminBarVisibility instance.
	 *
	 * @var AdminBarVisibility
	 */
	private $adminbar_visibility;

	/**
	 * PluginVisibility instance.
	 *
	 * @var PluginVisibility
	 */
	private $plugin_visibility;

	/**
	 * WidgetVisibility instance.
	 *
	 * @var WidgetVisibility
	 */
	private $widget_visibility;

	/**
	 * Constructor.
	 *
	 * @param MenuVisibility     $menu_visibility     MenuVisibility instance.
	 * @param AdminBarVisibility $adminbar_visibility AdminBarVisibility instance.
	 * @param PluginVisibility   $plugin_visibility   PluginVisibility instance.
	 * @param WidgetVisibility   $widget_visibility   WidgetVisibility instance.
	 */
	public function __construct(
		MenuVisibility $menu_visibility,
		AdminBarVisibility $adminbar_visibility,
		PluginVisibility $plugin_visibility,
		WidgetVisibility $widget_visibility
	) {
		$this->menu_visibility     = $menu_visibility;
		$this->adminbar_visibility = $adminbar_visibility;
		$this->plugin_visibility   = $plugin_visibility;
		$this->widget_visibility   = $widget_visibility;
	}

	/**
	 * Get the MenuVisibility instance.
	 *
	 * @return MenuVisibility
	 */
	public function menus() {
		return $this->menu_visibility;
	}

	/**
	 * Get the AdminBarVisibility instance.
	 *
	 * @return AdminBarVisibility
	 */
	public function admin_bar() {
		return $this->adminbar_visibility;
	}

	/**
	 * Get the PluginVisibility instance.
	 *
	 * @return PluginVisibility
	 */
	public function plugins() {
		return $this->plugin_visibility;
	}

	/**
	 * Get the WidgetVisibility instance.
	 *
	 * @return WidgetVisibility
	 */
	public function widgets() {
		return $this->widget_visibility;
	}
}
