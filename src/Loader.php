<?php
/**
 * Hook Loader Class.
 *
 * Centralizes all hook registration. No hooks are registered in constructors.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loader Class.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Registered actions.
	 *
	 * @var array
	 */
	private $actions = array();

	/**
	 * Registered filters.
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Module instances.
	 *
	 * @var array
	 */
	private $modules = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Define all plugin hooks.
	 *
	 * @return void
	 */
	private function define_hooks() {
		// Migration manager - runs early on admin_init.
		$this->add_action( 'admin_init', array( $this, 'run_migrations' ), 5 );

		// Admin page registration.
		$this->add_action( 'admin_menu', array( $this, 'register_admin_page' ), 10 );

		// Admin assets.
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 10 );

		// Admin menu brand color (runs on all admin pages).
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'admin_menu_color' ), 10 );

		// Admin notices.
		$this->add_action( 'admin_notices', array( $this, 'display_admin_notices' ), 10 );
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_notice_scripts' ), 10 );
		$this->add_action( 'wp_ajax_uswl_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
		$this->add_action( 'admin_init', array( $this, 'handle_preview_exit_fallback' ), 1 );

		// AJAX handlers.
		$this->add_action( 'wp_ajax_uswl_save_settings', array( $this, 'ajax_save_settings' ) );
		$this->add_action( 'wp_ajax_uswl_export_settings', array( $this, 'ajax_export_settings' ) );
		$this->add_action( 'wp_ajax_uswl_import_settings', array( $this, 'ajax_import_settings' ) );
		$this->add_action( 'wp_ajax_uswl_reset_settings', array( $this, 'ajax_reset_settings' ) );
		$this->add_action( 'wp_ajax_uswl_get_menus', array( $this, 'ajax_get_menus' ) );
		$this->add_action( 'wp_ajax_uswl_get_adminbar', array( $this, 'ajax_get_adminbar' ) );
		$this->add_action( 'wp_ajax_uswl_get_plugins', array( $this, 'ajax_get_plugins' ) );
		$this->add_action( 'wp_ajax_uswl_get_widgets', array( $this, 'ajax_get_widgets' ) );
		$this->add_action( 'wp_ajax_uswl_preview_mode', array( $this, 'ajax_preview_mode' ) );

		// Login branding.
		$this->add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_styles' ), 10 );
		$this->add_filter( 'login_headerurl', array( $this, 'filter_login_header_url' ), 10 );
		$this->add_filter( 'login_headertext', array( $this, 'filter_login_header_text' ), 10 );

		// Admin branding.
		$this->add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer_text' ), 10 );
		$this->add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 999 );
		$this->add_filter( 'gettext', array( $this, 'filter_howdy_text' ), 20, 3 );
		$this->add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_logo' ), 0 ); // Priority 0 so the logo is the FIRST node registered (far left).
		$this->add_action( 'admin_bar_menu', array( $this, 'modify_admin_bar' ), 999 ); // High priority to ensure we run after WP logo is added
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_branding_styles' ), 10 );
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_logo_css' ), 10 );
		$this->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_logo_css' ), 10 );
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_scheme' ), 10 );

		// Menu visibility and renaming.
		$this->add_action( 'admin_menu', array( $this, 'hide_menus' ), 9999 );
		$this->add_action( 'admin_menu', array( $this, 'rename_menus' ), 9999 );

		// Admin bar visibility - cache first, then hide.
		$this->add_action( 'admin_bar_menu', array( $this, 'cache_adminbar_items' ), 9998 );
		$this->add_action( 'admin_bar_menu', array( $this, 'hide_adminbar_items' ), 9999 );

		// Plugin visibility.
		$this->add_filter( 'all_plugins', array( $this, 'filter_plugins' ), 10 );

		// Theme visibility.
		$this->add_filter( 'wp_prepare_themes_for_js', array( $this, 'filter_themes' ), 10 );

		// Widget visibility and custom widget.
		$this->add_action( 'wp_dashboard_setup', array( $this, 'hide_widgets' ), 9999 );
		$this->add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ), 9999 );
		$this->add_action( 'wp_dashboard_setup', array( $this, 'register_custom_widget' ), 10 );

		// Notification visibility.
		$this->add_action( 'admin_head', array( $this, 'remove_admin_notices' ), 1 );
		$this->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_notice_hide_css' ), 10 );
	}

	/**
	 * Add an action hook.
	 *
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback function.
	 * @param int      $priority      Priority. Default 10.
	 * @param int      $accepted_args Number of accepted arguments. Default 1.
	 * @return void
	 */
	public function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Add a filter hook.
	 *
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback function.
	 * @param int      $priority      Priority. Default 10.
	 * @param int      $accepted_args Number of accepted arguments. Default 1.
	 * @return void
	 */
	public function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Register all defined hooks with WordPress.
	 *
	 * @return void
	 */
	public function register() {
		// Register actions.
		foreach ( $this->actions as $action ) {
			add_action(
				$action['hook'],
				$action['callback'],
				$action['priority'],
				$action['accepted_args']
			);
		}

		// Register filters.
		foreach ( $this->filters as $filter ) {
			add_filter(
				$filter['hook'],
				$filter['callback'],
				$filter['priority'],
				$filter['accepted_args']
			);
		}
	}

	/**
	 * Initialize all modules.
	 *
	 * @return void
	 */
	public function init_modules() {
		$plugin = Plugin::instance();

		// Core services.
		$this->modules['settings']          = $plugin->settings();
		$this->modules['transient_manager'] = new Core\TransientManager();
		$this->modules['capabilities']      = new Core\Capabilities( $this->modules['settings'] );

		// Admin.
		$this->modules['admin_page']    = new Admin\AdminPage( $this->modules['settings'], $this->modules['capabilities'] );
		$this->modules['admin_assets']  = new Admin\AdminAssets( $this->modules['admin_page'] );
		$this->modules['admin_notices'] = new Admin\AdminNotices();
		$this->modules['ajax']          = new Admin\AjaxController(
			$this->modules['settings'],
			$this->modules['capabilities'],
			$this->modules['transient_manager']
		);

		// Branding.
		$this->modules['login_branding']     = new Branding\LoginBranding( $this->modules['settings'], $this->modules['capabilities'] );
		$this->modules['admin_branding']     = new Branding\AdminBranding( $this->modules['settings'], $this->modules['capabilities'] );
		$this->modules['menu_branding']      = new Branding\MenuBranding( $this->modules['settings'], $this->modules['capabilities'] );
		$this->modules['dashboard_branding'] = new Branding\DashboardBranding( $this->modules['settings'], $this->modules['capabilities'] );

		// Visibility.
		$this->modules['menu_visibility']     = new Visibility\MenuVisibility(
			$this->modules['settings'],
			$this->modules['capabilities'],
			$this->modules['transient_manager']
		);
		$this->modules['adminbar_visibility'] = new Visibility\AdminBarVisibility(
			$this->modules['settings'],
			$this->modules['capabilities'],
			$this->modules['transient_manager']
		);
		$this->modules['plugin_visibility']   = new Visibility\PluginVisibility(
			$this->modules['settings'],
			$this->modules['capabilities']
		);
		$this->modules['widget_visibility']   = new Visibility\WidgetVisibility(
			$this->modules['settings'],
			$this->modules['capabilities']
		);
		$this->modules['theme_visibility']    = new Visibility\ThemeVisibility(
			$this->modules['settings'],
			$this->modules['capabilities']
		);
		$this->modules['notification_visibility'] = new Visibility\NotificationVisibility(
			$this->modules['settings'],
			$this->modules['capabilities']
		);

		// Visibility manager facade.
		$this->modules['visibility_manager'] = new Visibility\VisibilityManager(
			$this->modules['menu_visibility'],
			$this->modules['adminbar_visibility'],
			$this->modules['plugin_visibility'],
			$this->modules['widget_visibility']
		);

		// Migration manager.
		$this->modules['migration_manager'] = new Migration\MigrationManager(
			$this->modules['settings'],
			$this->modules['transient_manager']
		);
	}

	/**
	 * Get a module instance.
	 *
	 * @param string $name Module name.
	 * @return object|null
	 */
	public function get_module( $name ) {
		return isset( $this->modules[ $name ] ) ? $this->modules[ $name ] : null;
	}

	// =========================================================================
	// HOOK CALLBACKS
	// =========================================================================

	/**
	 * Run migrations.
	 *
	 * @return void
	 */
	public function run_migrations() {
		$migration_manager = $this->get_module( 'migration_manager' );
		if ( $migration_manager ) {
			$migration_manager->maybe_run();
		}
	}

	/**
	 * Register admin page.
	 *
	 * @return void
	 */
	public function register_admin_page() {
		$admin_page = $this->get_module( 'admin_page' );
		if ( $admin_page ) {
			$admin_page->register();
		}
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		$admin_assets = $this->get_module( 'admin_assets' );
		if ( $admin_assets ) {
			$admin_assets->enqueue( $hook_suffix );
		}
	}

	/**
	 * Output admin menu brand color CSS.
	 *
	 * @return void
	 */
	public function admin_menu_color() {
		$admin_assets = $this->get_module( 'admin_assets' );
		if ( $admin_assets ) {
			$admin_assets->admin_menu_color();
		}
	}

	/**
	 * Display admin notices.
	 *
	 * @return void
	 */
	public function display_admin_notices() {
		$admin_notices = $this->get_module( 'admin_notices' );
		if ( $admin_notices ) {
			$admin_notices->display_notices();
		}
	}

	/**
	 * Enqueue notice scripts.
	 *
	 * @return void
	 */
	public function enqueue_notice_scripts() {
		$admin_notices = $this->get_module( 'admin_notices' );
		if ( $admin_notices ) {
			$admin_notices->enqueue_scripts();
		}
	}

	/**
	 * Handle AJAX dismiss notice.
	 *
	 * @return void
	 */
	public function ajax_dismiss_notice() {
		$admin_notices = $this->get_module( 'admin_notices' );
		if ( $admin_notices ) {
			$admin_notices->ajax_dismiss_notice();
		}
	}

	/**
	 * Handle AJAX save settings.
	 *
	 * @return void
	 */
	public function ajax_save_settings() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_save();
		}
	}

	/**
	 * Handle AJAX export settings.
	 *
	 * @return void
	 */
	public function ajax_export_settings() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_export();
		}
	}

	/**
	 * Handle AJAX import settings.
	 *
	 * @return void
	 */
	public function ajax_import_settings() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_import();
		}
	}

	/**
	 * Handle AJAX reset settings.
	 *
	 * @return void
	 */
	public function ajax_reset_settings() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_reset();
		}
	}

	/**
	 * Handle AJAX get menus.
	 *
	 * @return void
	 */
	public function ajax_get_menus() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_get_menus();
		}
	}

	/**
	 * Handle AJAX get admin bar items.
	 *
	 * @return void
	 */
	public function ajax_get_adminbar() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_get_adminbar();
		}
	}

	/**
	 * Handle AJAX get plugins.
	 *
	 * @return void
	 */
	public function ajax_get_plugins() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_get_plugins();
		}
	}

	/**
	 * Handle AJAX get widgets.
	 *
	 * @return void
	 */
	public function ajax_get_widgets() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_get_widgets();
		}
	}

	/**
	 * Handle the no-JavaScript preview exit fallback link.
	 *
	 * @return void
	 */
	public function handle_preview_exit_fallback() {
		$admin_notices = $this->get_module( 'admin_notices' );
		if ( $admin_notices ) {
			$admin_notices->maybe_handle_exit_fallback();
		}
	}

	/**
	 * Handle AJAX preview mode toggle.
	 *
	 * @return void
	 */
	public function ajax_preview_mode() {
		$ajax = $this->get_module( 'ajax' );
		if ( $ajax ) {
			$ajax->handle_preview_mode();
		}
	}

	/**
	 * Enqueue login styles.
	 *
	 * @return void
	 */
	public function login_enqueue_styles() {
		$login_branding = $this->get_module( 'login_branding' );
		if ( $login_branding ) {
			$login_branding->enqueue_styles();
		}
	}

	/**
	 * Filter login header URL.
	 *
	 * @param string $url Login header URL.
	 * @return string
	 */
	public function filter_login_header_url( $url ) {
		$login_branding = $this->get_module( 'login_branding' );
		if ( $login_branding ) {
			return $login_branding->filter_header_url( $url );
		}
		return $url;
	}

	/**
	 * Filter login header text.
	 *
	 * @param string $text Login header text.
	 * @return string
	 */
	public function filter_login_header_text( $text ) {
		$login_branding = $this->get_module( 'login_branding' );
		if ( $login_branding ) {
			return $login_branding->filter_header_text( $text );
		}
		return $text;
	}

	/**
	 * Filter admin footer text.
	 *
	 * @param string $text Footer text.
	 * @return string
	 */
	public function filter_admin_footer_text( $text ) {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding ) {
			return $admin_branding->filter_footer_text( $text );
		}
		return $text;
	}

	/**
	 * Filter update footer (WordPress version).
	 *
	 * @param string $content Original footer content.
	 * @return string
	 */
	public function filter_update_footer( $content ) {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding && method_exists( $admin_branding, 'filter_update_footer' ) ) {
			return $admin_branding->filter_update_footer( $content );
		}
		return $content;
	}

	/**
	 * Filter Howdy text.
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Original text.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function filter_howdy_text( $translation, $text, $domain ) {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding ) {
			return $admin_branding->filter_howdy_text( $translation, $text, $domain );
		}
		return $translation;
	}

	/**
	 * Modify admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function add_admin_bar_logo( $wp_admin_bar ) {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding ) {
			$admin_branding->add_admin_bar_logo( $wp_admin_bar );
		}
	}

	/**
	 * Modify the admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function modify_admin_bar( $wp_admin_bar ) {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding ) {
			$admin_branding->modify_admin_bar( $wp_admin_bar );
		}
	}

	/**
	 * Enqueue admin branding styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_branding_styles() {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding ) {
			$admin_branding->enqueue_styles();
		}
	}

	/**
	 * Enqueue custom logo CSS.
	 *
	 * @return void
	 */
	public function enqueue_custom_logo_css() {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding && method_exists( $admin_branding, 'enqueue_custom_logo_css' ) ) {
			$admin_branding->enqueue_custom_logo_css();
		}
	}

	/**
	 * Enqueue admin color scheme CSS.
	 *
	 * @return void
	 */
	public function enqueue_color_scheme() {
		$admin_branding = $this->get_module( 'admin_branding' );
		if ( $admin_branding && method_exists( $admin_branding, 'enqueue_color_scheme' ) ) {
			$admin_branding->enqueue_color_scheme();
		}
	}

	/**
	 * Hide sidebar menus.
	 *
	 * @return void
	 */
	public function hide_menus() {
		$menu_visibility = $this->get_module( 'menu_visibility' );
		if ( $menu_visibility ) {
			$menu_visibility->hide_menus();
		}
	}

	/**
	 * Cache admin bar items.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function cache_adminbar_items( $wp_admin_bar ) {
		$adminbar_visibility = $this->get_module( 'adminbar_visibility' );
		if ( $adminbar_visibility ) {
			$adminbar_visibility->cache_available( $wp_admin_bar );
		}
	}

	/**
	 * Hide admin bar items.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function hide_adminbar_items( $wp_admin_bar ) {
		$adminbar_visibility = $this->get_module( 'adminbar_visibility' );
		if ( $adminbar_visibility ) {
			$adminbar_visibility->hide_items( $wp_admin_bar );
		}
	}

	/**
	 * Filter plugins list.
	 *
	 * @param array $plugins All plugins.
	 * @return array
	 */
	public function filter_plugins( $plugins ) {
		$plugin_visibility = $this->get_module( 'plugin_visibility' );
		if ( $plugin_visibility ) {
			return $plugin_visibility->filter_plugins( $plugins );
		}
		return $plugins;
	}

	/**
	 * Hide dashboard widgets.
	 *
	 * @return void
	 */
	public function hide_widgets() {
		$widget_visibility = $this->get_module( 'widget_visibility' );
		if ( $widget_visibility ) {
			$widget_visibility->hide_widgets();
		}
	}

	/**
	 * Rename sidebar menus.
	 *
	 * @return void
	 */
	public function rename_menus() {
		$menu_branding = $this->get_module( 'menu_branding' );
		if ( $menu_branding && method_exists( $menu_branding, 'rename_menus' ) ) {
			$menu_branding->rename_menus();
		}
	}

	/**
	 * Remove dashboard widgets (via DashboardBranding).
	 *
	 * @return void
	 */
	public function remove_dashboard_widgets() {
		$dashboard_branding = $this->get_module( 'dashboard_branding' );
		if ( $dashboard_branding && method_exists( $dashboard_branding, 'remove_widgets' ) ) {
			$dashboard_branding->remove_widgets();
		}
	}

	/**
	 * Register custom dashboard widget.
	 *
	 * @return void
	 */
	public function register_custom_widget() {
		$dashboard_branding = $this->get_module( 'dashboard_branding' );
		if ( $dashboard_branding && method_exists( $dashboard_branding, 'register_custom_widget' ) ) {
			$dashboard_branding->register_custom_widget();
		}
	}

	/**
	 * Filter themes to hide selected ones.
	 *
	 * @param array $themes All themes.
	 * @return array
	 */
	public function filter_themes( $themes ) {
		$theme_visibility = $this->get_module( 'theme_visibility' );
		if ( $theme_visibility && method_exists( $theme_visibility, 'filter_themes' ) ) {
			return $theme_visibility->filter_themes( $themes );
		}
		return $themes;
	}

	/**
	 * Remove admin notices.
	 *
	 * @return void
	 */
	public function remove_admin_notices() {
		$notification_visibility = $this->get_module( 'notification_visibility' );
		if ( $notification_visibility && method_exists( $notification_visibility, 'remove_notices' ) ) {
			$notification_visibility->remove_notices();
		}
	}

	/**
	 * Enqueue CSS to hide admin notices.
	 *
	 * @return void
	 */
	public function enqueue_notice_hide_css() {
		$notification_visibility = $this->get_module( 'notification_visibility' );
		if ( $notification_visibility && method_exists( $notification_visibility, 'enqueue_hide_css' ) ) {
			$notification_visibility->enqueue_hide_css();
		}
	}
}
