<?php
/**
 * Admin Branding Class.
 *
 * Handles admin area branding customization.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Branding;

use Unstoppable\WhiteLabel\Settings\Settings;
use Unstoppable\WhiteLabel\Core\Capabilities;
use Unstoppable\WhiteLabel\Plugin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AdminBranding Class.
 *
 * @since 1.0.0
 */
class AdminBranding {

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
	 * Filter the admin footer text.
	 *
	 * @param string $text Original footer text.
	 * @return string
	 */
	public function filter_footer_text( $text ) {
		if ( ! $this->capabilities->should_apply() ) {
			return $text;
		}

		$custom_text = $this->settings->get( 'admin.footer_text', '' );

		if ( ! $custom_text ) {
			return $text;
		}

		$original = $text;

		/**
		 * Filters the admin footer text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $custom_text Custom footer text.
		 * @param string $original    Original WordPress footer text.
		 */
		return apply_filters( 'uswl_admin_footer_text', $custom_text, $original );
	}

	/**
	 * Filter the Howdy greeting text.
	 *
	 * Only replaces "Howdy" in the default domain to avoid breaking translations.
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Original text.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function filter_howdy_text( $translation, $text, $domain ) {
		// Only filter default domain.
		if ( 'default' !== $domain ) {
			return $translation;
		}

		// Only filter strings starting with "Howdy".
		if ( strpos( $text, 'Howdy' ) !== 0 ) {
			return $translation;
		}

		if ( ! $this->capabilities->should_apply() ) {
			return $translation;
		}

		$replacement = $this->settings->get( 'admin.howdy_text', '' );

		if ( ! $replacement ) {
			return $translation;
		}

		// Get current user display name for the filter.
		$user = wp_get_current_user();
		$display_name = $user->exists() ? $user->display_name : '';

		/**
		 * Filters the Howdy replacement text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $replacement      Replacement text.
		 * @param string $display_name     Current user's display name.
		 */
		$replacement = apply_filters( 'uswl_admin_howdy_replacement', $replacement, $display_name );

		// Replace "Howdy" with the custom text.
		return str_replace( 'Howdy', $replacement, $translation );
	}

	/**
	 * Modify the admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function modify_admin_bar( $wp_admin_bar ) {
		// These apply to EVERYONE (including White Label Administrators).
		// Remove WordPress logo if enabled.
		if ( $this->settings->get( 'admin.hide_wp_logo', false ) ) {
			$wp_admin_bar->remove_node( 'wp-logo' );
			$wp_admin_bar->remove_node( 'about' );
			$wp_admin_bar->remove_node( 'wporg' );
			$wp_admin_bar->remove_node( 'documentation' );
			$wp_admin_bar->remove_node( 'support-forums' );
			$wp_admin_bar->remove_node( 'feedback' );
		}

		// NOTE: The custom logo is NOT added here. modify_admin_bar() runs at
		// priority 999, by which point every other node is already registered,
		// so add_node() would append the logo to the END of the left group.
		// It is registered separately in add_admin_bar_logo() at priority 0.

		// The rest only applies to non-White Label Administrators.
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		/**
		 * Fires before admin branding is applied.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_before_apply_branding' );

		/**
		 * Filters admin bar nodes to modify.
		 *
		 * @since 1.0.0
		 *
		 * @param array $nodes Array of node modifications.
		 */
		$nodes = apply_filters( 'uswl_admin_bar_nodes_to_modify', array() );

		// Process custom node modifications from Pro.
		foreach ( $nodes as $node_id => $modification ) {
			if ( ! isset( $modification['action'] ) ) {
				continue;
			}

			switch ( $modification['action'] ) {
				case 'remove':
					$wp_admin_bar->remove_node( $node_id );
					break;

				case 'add':
					if ( isset( $modification['args'] ) ) {
						$wp_admin_bar->add_node( $modification['args'] );
					}
					break;

				case 'modify':
					$node = $wp_admin_bar->get_node( $node_id );
					if ( $node && isset( $modification['args'] ) ) {
						$args = array_merge( (array) $node, $modification['args'] );
						$wp_admin_bar->add_node( $args );
					}
					break;
			}
		}

		/**
		 * Fires after admin branding is applied.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_after_apply_branding' );
	}

	/**
	 * Register the custom logo node in the admin bar.
	 *
	 * Hooked to admin_bar_menu at priority 0 so the node is registered BEFORE
	 * core and other plugins add theirs. WP_Admin_Bar renders nodes in the
	 * order they were added within a group, so registering late pushes the
	 * logo to the far right of the left-hand cluster.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function add_admin_bar_logo( $wp_admin_bar ) {
		// Applies to EVERYONE, including White Label Administrators.
		$custom_logo_url = $this->settings->get( 'admin.custom_logo_url', '' );

		if ( ! $custom_logo_url ) {
			return;
		}

		$this->add_custom_logo( $wp_admin_bar, $custom_logo_url );
	}

	/**
	 * Add custom logo to admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @param string        $logo_url     Logo URL.
	 * @return void
	 */
	private function add_custom_logo( $wp_admin_bar, $logo_url ) {
		// Determine logo link URL.
		$remove_link = $this->settings->get( 'admin.remove_logo_link', false );
		$custom_link = $this->settings->get( 'admin.logo_link', '' );

		if ( $remove_link ) {
			$href = false; // No link.
		} elseif ( $custom_link ) {
			$href = $custom_link;
		} else {
			$href = admin_url(); // Default to dashboard.
		}

		/**
		 * Filters where the custom logo sits in the admin bar.
		 *
		 * @since 1.2.26
		 *
		 * @param string $position Either 'left' (default) or 'right'.
		 */
		$uswl_position = apply_filters( 'uswl_admin_bar_logo_position', 'left' );

		$uswl_args = array(
			'id'    => 'uswl-custom-logo',
			'title' => '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="uswl-logo-img" />',
			'href'  => $href,
			'meta'  => array(
				'class' => 'uswl-custom-logo-node',
			),
		);

		if ( 'right' === $uswl_position ) {
			$uswl_args['parent'] = 'top-secondary';
		}

		$wp_admin_bar->add_node( $uswl_args );
	}

	/**
	 * Enqueue custom logo CSS.
	 *
	 * Uses wp_add_inline_style() for WordPress.org compliance.
	 *
	 * @return void
	 */
	public function enqueue_custom_logo_css() {
		// NOTE: Do NOT gate this on should_apply(). modify_admin_bar() adds the
		// custom logo node for EVERYONE (including White Label Administrators),
		// so the sizing CSS must load for everyone too. Otherwise the raw <img>
		// renders at its natural size and breaks out of the admin bar.
		// The nodes only exist when the admin bar is showing.
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		$uswl_logo_css = '';

		// Hide the core site icon (.blavatar) beside the site name. Applies to
		// everyone, same as the "remove WordPress logo" option.
		if ( $this->settings->get( 'admin.hide_site_icon', false ) ) {
			$uswl_logo_css .= '
				#wpadminbar #wp-admin-bar-site-name .blavatar,
				#wpadminbar #wp-admin-bar-site-name img.blavatar,
				#wpadminbar #wp-admin-bar-site-name div.blavatar {
					display: none !important;
				}
				#wpadminbar #wp-admin-bar-site-name > .ab-item:before {
					content: none !important;
				}
			';
		}

		$custom_logo_url = $this->settings->get( 'admin.custom_logo_url', '' );

		if ( $custom_logo_url ) {
			$uswl_logo_css .= '
			/* Custom logo in admin bar - remove all backgrounds */
			#wpadminbar #wp-admin-bar-uswl-custom-logo,
			#wpadminbar #wp-admin-bar-uswl-custom-logo > .ab-item,
			#wpadminbar #wp-admin-bar-uswl-custom-logo > .ab-item > img,
			#wpadminbar #wp-admin-bar-uswl-custom-logo .ab-item,
			#wpadminbar #wp-admin-bar-uswl-custom-logo .uswl-logo-img,
			#wpadminbar .uswl-custom-logo-node,
			#wpadminbar .uswl-custom-logo-node > .ab-item {
				background: none !important;
				background-color: transparent !important;
				background-image: none !important;
			}
			#wpadminbar #wp-admin-bar-uswl-custom-logo {
				display: block !important;
				float: left !important;
				height: 32px !important;
				overflow: hidden !important;
			}
			#wpadminbar #wp-admin-bar-uswl-custom-logo > .ab-item {
				height: 32px !important;
				line-height: 32px !important;
				padding: 0 10px !important;
				display: flex !important;
				align-items: center;
				overflow: hidden !important;
			}
			#wpadminbar #wp-admin-bar-uswl-custom-logo .uswl-logo-img,
			#wpadminbar #wp-admin-bar-uswl-custom-logo > .ab-item > img {
				height: 20px !important;
				max-height: 20px !important;
				width: auto !important;
				max-width: 120px !important;
				min-width: 0 !important;
				display: block !important;
				position: static !important;
				margin: 0 !important;
				padding: 0 !important;
				border: 0 !important;
				border-radius: 0 !important;
				box-shadow: none !important;
				object-fit: contain;
				vertical-align: middle;
			}
			/* Mobile: WP collapses the bar to 46px */
			@media screen and (max-width: 782px) {
				#wpadminbar #wp-admin-bar-uswl-custom-logo,
				#wpadminbar #wp-admin-bar-uswl-custom-logo > .ab-item {
					height: 46px !important;
					line-height: 46px !important;
				}
			}
			/* Hover state */
			#wpadminbar #wp-admin-bar-uswl-custom-logo:hover > .ab-item {
				background: rgba(255, 255, 255, 0.1) !important;
			}
			';
		}

		// Nothing to output.
		if ( '' === $uswl_logo_css ) {
			return;
		}

		// Register an empty stylesheet as anchor for inline styles.
		wp_register_style( 'uswl-custom-logo', false, array(), USWL_VERSION );
		wp_enqueue_style( 'uswl-custom-logo' );

		wp_add_inline_style( 'uswl-custom-logo', $uswl_logo_css );
	}

	/**
	 * Enqueue admin branding styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		/**
		 * Fires when admin branding styles are being enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_admin_enqueue_styles' );

		$css_vars = $this->get_css_variables();

		if ( empty( $css_vars ) ) {
			return;
		}

		$css = ':root {' . "\n";
		foreach ( $css_vars as $name => $value ) {
			$css .= sprintf( '  %s: %s;', esc_attr( $name ), esc_attr( $value ) ) . "\n";
		}
		$css .= '}' . "\n";

		wp_add_inline_style( 'admin-bar', $css );
	}

	/**
	 * Get CSS variables for admin area.
	 *
	 * @return array
	 */
	public function get_css_variables() {
		$variables = array();

		/**
		 * Filters the CSS variables for the admin area.
		 *
		 * @since 1.0.0
		 *
		 * @param array $variables CSS variables array.
		 */
		return apply_filters( 'uswl_admin_css_variables', $variables );
	}

	/**
	 * Filter the update footer (WordPress version).
	 *
	 * @param string $content Original footer content.
	 * @return string
	 */
	public function filter_update_footer( $content ) {
		if ( ! $this->capabilities->should_apply() ) {
			return $content;
		}

		$hide_version = $this->settings->get( 'admin.hide_wp_version', false );

		if ( ! $hide_version ) {
			return $content;
		}

		return '';
	}

	/**
	 * Enqueue admin color scheme CSS.
	 *
	 * @return void
	 */
	public function enqueue_color_scheme() {
		if ( ! $this->capabilities->should_apply() ) {
			return;
		}

		// Check if color scheme is enabled.
		if ( ! $this->settings->get( 'admin.enable_color_scheme', false ) ) {
			return;
		}

		$css = $this->get_color_scheme_css();

		if ( empty( trim( $css ) ) ) {
			return;
		}

		// Register an empty stylesheet as anchor for inline styles.
		wp_register_style( 'uswl-color-scheme', false, array(), USWL_VERSION );
		wp_enqueue_style( 'uswl-color-scheme' );
		wp_add_inline_style( 'uswl-color-scheme', $css );
	}

	/**
	 * Generate color scheme CSS.
	 *
	 * @return string
	 */
	private function get_color_scheme_css() {
		$css = '';

		// Menu background.
		$menu_bg = $this->settings->get( 'admin.menu_background', '' );
		if ( $menu_bg ) {
			$menu_bg = esc_attr( $menu_bg );
			$css .= "#adminmenu, #adminmenuback, #adminmenuwrap { background: {$menu_bg}; }\n";
		}

		// Menu text.
		$menu_text = $this->settings->get( 'admin.menu_text', '' );
		if ( $menu_text ) {
			$menu_text = esc_attr( $menu_text );
			$css .= "#adminmenu a, #adminmenu .wp-submenu a, #adminmenu .wp-menu-name { color: {$menu_text}; }\n";
			$css .= "#adminmenu .wp-menu-image:before { color: {$menu_text}; }\n";
		}

		// Menu highlight.
		$menu_highlight = $this->settings->get( 'admin.menu_highlight', '' );
		if ( $menu_highlight ) {
			$menu_highlight = esc_attr( $menu_highlight );
			$css .= "#adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus { background: {$menu_highlight}; }\n";
			$css .= "#adminmenu .current a.menu-top, #adminmenu .wp-has-current-submenu a.wp-has-current-submenu { background: {$menu_highlight}; }\n";
			$css .= "#adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu .wp-has-current-submenu.opensub .wp-submenu { border-left-color: {$menu_highlight}; }\n";
		}

		// Submenu background.
		$submenu_bg = $this->settings->get( 'admin.submenu_background', '' );
		if ( $submenu_bg ) {
			$submenu_bg = esc_attr( $submenu_bg );
			$css .= "#adminmenu .wp-submenu, #adminmenu .wp-has-current-submenu .wp-submenu { background: {$submenu_bg}; }\n";
		}

		// Submenu text.
		$submenu_text = $this->settings->get( 'admin.submenu_text', '' );
		if ( $submenu_text ) {
			$submenu_text = esc_attr( $submenu_text );
			$css .= "#adminmenu .wp-submenu a { color: {$submenu_text}; }\n";
		}

		// Submenu highlight.
		$submenu_highlight = $this->settings->get( 'admin.submenu_highlight', '' );
		if ( $submenu_highlight ) {
			$submenu_highlight = esc_attr( $submenu_highlight );
			$css .= "#adminmenu .wp-submenu a:hover, #adminmenu .wp-submenu a:focus { color: {$submenu_highlight}; }\n";
			$css .= "#adminmenu .wp-submenu li.current a { color: {$submenu_highlight}; }\n";
		}

		// Notification color.
		$notification = $this->settings->get( 'admin.notification_color', '' );
		if ( $notification ) {
			$notification = esc_attr( $notification );
			$css .= "#adminmenu .awaiting-mod, #adminmenu .update-plugins { background: {$notification}; }\n";
			$css .= "#wpadminbar .ab-top-menu .awaiting-mod, #wpadminbar .ab-top-menu .update-plugins { background: {$notification}; }\n";
		}

		// Link color.
		$link_color = $this->settings->get( 'admin.link_color', '' );
		if ( $link_color ) {
			$link_color = esc_attr( $link_color );
			$css .= "#wpbody a { color: {$link_color}; }\n";
			$css .= "#wpbody a:hover, #wpbody a:focus { color: {$link_color}; filter: brightness(1.2); }\n";
		}

		// Button color.
		$button_color = $this->settings->get( 'admin.button_color', '' );
		if ( $button_color ) {
			$button_color = esc_attr( $button_color );
			$css .= ".wp-core-ui .button-primary { background: {$button_color}; border-color: {$button_color}; }\n";
			$css .= ".wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus { background: {$button_color}; filter: brightness(1.1); border-color: {$button_color}; }\n";
		}

		return $css;
	}
}
