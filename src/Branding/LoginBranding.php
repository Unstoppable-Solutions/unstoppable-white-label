<?php
/**
 * Login Branding Class.
 *
 * Handles login page customization.
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
 * LoginBranding Class.
 *
 * @since 1.0.0
 */
class LoginBranding {

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
	 * Check if login branding should apply.
	 *
	 * @return bool
	 */
	private function should_apply() {
		// Must be enabled.
		if ( ! $this->settings->is_enabled() ) {
			return false;
		}

		// Login page always shows branding (user not logged in yet).
		return true;
	}

	/**
	 * Enqueue login page styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! $this->should_apply() ) {
			return;
		}

		/**
		 * Fires when login styles are being enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'uswl_login_enqueue_styles' );

		// Output inline styles using wp_add_inline_style for WP.org compliance.
		$this->enqueue_inline_styles();
	}

	/**
	 * Enqueue inline CSS styles using wp_add_inline_style.
	 *
	 * @return void
	 */
	private function enqueue_inline_styles() {
		$css = $this->get_login_css();

		if ( empty( trim( $css ) ) ) {
			return;
		}

		// Register an empty stylesheet as anchor for inline styles.
		wp_register_style( 'uswl-login-styles', false, array(), USWL_VERSION );
		wp_enqueue_style( 'uswl-login-styles' );
		wp_add_inline_style( 'uswl-login-styles', $css );
	}

	/**
	 * Get login page CSS rules.
	 *
	 * @return string
	 */
	private function get_login_css() {
		$css = '';

		// === PAGE BACKGROUND ===
		$bg_color      = $this->settings->get( 'login.background_color', '' );
		$bg_image      = $this->settings->get( 'login.background_image_url', '' );
		$form_position = $this->settings->get( 'login.form_position', 'center' );

		// Background color always applies to body
		if ( $bg_color ) {
			$css .= "/* Page Background Color */\n";
			$css .= "body.login { background-color: " . esc_attr( $bg_color ) . "; }\n";
		}

		// Background image handling depends on form position
		if ( $bg_image ) {
			$bg_size     = $this->settings->get( 'login.background_size', 'cover' );
			$bg_position = $this->settings->get( 'login.background_position', 'center center' );
			$bg_repeat   = $this->settings->get( 'login.background_repeat', 'no-repeat' );

			if ( 'center' === $form_position ) {
				// Center: background covers full page
				$css .= "/* Page Background Image - Full */\n";
				$css .= "body.login {\n";
				$css .= "  background-image: url('" . esc_url( $bg_image ) . "');\n";
				$css .= "  background-size: " . esc_attr( $bg_size ) . ";\n";
				$css .= "  background-position: " . esc_attr( $bg_position ) . ";\n";
				$css .= "  background-repeat: " . esc_attr( $bg_repeat ) . ";\n";
				$css .= "  background-attachment: fixed;\n";
				$css .= "}\n";
			} else {
				// Left/Right: background only on opposite side using pseudo-element
				$side = ( 'left' === $form_position ) ? 'left' : 'right';
				$opposite = ( 'left' === $form_position ) ? 'right' : 'left';
				
				$css .= "/* Page Background Image - Split Screen */\n";
				$css .= "body.login::after {\n";
				$css .= "  content: '';\n";
				$css .= "  position: fixed;\n";
				$css .= "  top: 0;\n";
				$css .= "  {$opposite}: 0;\n";
				$css .= "  width: calc(100% - 450px);\n";
				$css .= "  height: 100%;\n";
				$css .= "  background-image: url('" . esc_url( $bg_image ) . "');\n";
				$css .= "  background-size: " . esc_attr( $bg_size ) . ";\n";
				$css .= "  background-position: " . esc_attr( $bg_position ) . ";\n";
				$css .= "  background-repeat: " . esc_attr( $bg_repeat ) . ";\n";
				$css .= "  z-index: -1;\n";
				$css .= "}\n";
				$css .= "@media only screen and (max-width: 649px) {\n";
				$css .= "  body.login::after { display: none; }\n";
				$css .= "}\n";
			}
		}

		// === FORM POSITION ===
		$form_bg_color = $this->settings->get( 'login.form_background_color', '' ) ?: '#ffffff';

		if ( 'left' === $form_position ) {
			$escaped_bg = esc_attr( $form_bg_color );
			$css .= "/* Login Form Position: Left */\n";
			$css .= "div#login { background: {$escaped_bg}; height: 100%; padding-left: 10%; padding-right: 10%; }\n";
			$css .= ".interim-login #login { padding: 15px; }\n";
			$css .= "@media only screen and (min-width: 650px) {\n";
			$css .= "  body #login { background: {$escaped_bg}; padding: 8% 60px 10px 50px; float: left; height: 100%; position: fixed; box-shadow: 0px 0px 10px 10px rgba(0,0,0,0.35); }\n";
			$css .= "  .language-switcher { position: fixed; bottom: 0; left: 0; width: 430px; }\n";
			$css .= "  .language-switcher form#language-switcher { margin: auto; }\n";
			$css .= "}\n";
		} elseif ( 'right' === $form_position ) {
			$escaped_bg = esc_attr( $form_bg_color );
			$css .= "/* Login Form Position: Right */\n";
			$css .= "div#login { background: {$escaped_bg}; height: 100%; padding-left: 10%; padding-right: 10%; }\n";
			$css .= ".interim-login #login { padding: 15px; }\n";
			$css .= "@media only screen and (min-width: 650px) {\n";
			$css .= "  body #login { background: {$escaped_bg}; padding: 8% 60px 10px 50px; right: 0; height: 100%; position: fixed; box-shadow: 0px 0px 10px 10px rgba(0,0,0,0.35); }\n";
			$css .= "  .language-switcher { position: fixed; bottom: 0; right: 0; width: 430px; }\n";
			$css .= "  .language-switcher form#language-switcher { margin: auto; }\n";
			$css .= "}\n";
		}

		// === FORM BACKGROUND (for center position) ===
		$form_bg = $this->settings->get( 'login.form_background_color', '' );
		if ( $form_bg && 'center' === $form_position ) {
			$css .= "/* Form Background */\n";
			$css .= ".login form { background-color: " . esc_attr( $form_bg ) . "; }\n";
		}

		// === FORM TEXT COLOR ===
		$form_text_color = $this->settings->get( 'login.form_text_color', '' );
		if ( $form_text_color ) {
			$css .= "/* Form Text */\n";
			$css .= ".login form label { color: " . esc_attr( $form_text_color ) . "; }\n";
		}

		// === LINK COLORS ===
		$link_color = $this->settings->get( 'login.link_color', '' );
		if ( $link_color ) {
			$css .= "/* Links */\n";
			$css .= ".login #nav a, .login #backtoblog a, .login .privacy-policy-page-link a { color: " . esc_attr( $link_color ) . "; }\n";
			$css .= ".login #nav a:hover, .login #backtoblog a:hover, .login .privacy-policy-page-link a:hover { color: " . esc_attr( $link_color ) . "; opacity: 0.8; }\n";
		}

		// === BUTTON COLORS ===
		$button_bg = $this->settings->get( 'login.button_background_color', '' );
		$button_text = $this->settings->get( 'login.button_text_color', '' );
		if ( $button_bg || $button_text ) {
			$css .= "/* Button */\n";
			$css .= ".login .button-primary {\n";
			if ( $button_bg ) {
				$css .= "  background-color: " . esc_attr( $button_bg ) . " !important;\n";
				$css .= "  border-color: " . esc_attr( $button_bg ) . " !important;\n";
			}
			if ( $button_text ) {
				$css .= "  color: " . esc_attr( $button_text ) . " !important;\n";
			}
			$css .= "}\n";
			$css .= ".login .button-primary:hover, .login .button-primary:focus {\n";
			if ( $button_bg ) {
				$css .= "  background-color: " . esc_attr( $button_bg ) . " !important;\n";
				$css .= "  border-color: " . esc_attr( $button_bg ) . " !important;\n";
				$css .= "  opacity: 0.9;\n";
			}
			$css .= "}\n";
		}

		// === CUSTOM LOGO ===
		$logo_url = $this->settings->get( 'login.logo_url', '' );
		if ( $logo_url ) {
			$logo_width = $this->settings->get( 'login.logo_width', '' );
			$logo_height = $this->settings->get( 'login.logo_height', '' );

			$css .= "/* Custom Logo */\n";
			$css .= "#login h1 a, .login h1 a {\n";
			$css .= "  background-image: url('" . esc_url( $logo_url ) . "');\n";
			$css .= "  background-size: contain;\n";
			$css .= "  background-position: center center;\n";
			$css .= "  background-repeat: no-repeat;\n";
			$css .= "  width: " . ( $logo_width ? absint( $logo_width ) . 'px' : '320px' ) . ";\n";
			$css .= "  height: " . ( $logo_height ? absint( $logo_height ) . 'px' : '84px' ) . ";\n";
			$css .= "}\n";
		}

		// === HIDE ELEMENTS ===
		$hide_css = array();

		if ( $this->settings->get( 'login.hide_remember_me', false ) ) {
			$hide_css[] = '.login .forgetmenot';
		}
		if ( $this->settings->get( 'login.hide_lost_password', false ) ) {
			$hide_css[] = '.login #nav';
		}
		if ( $this->settings->get( 'login.hide_back_to_site', false ) ) {
			$hide_css[] = '.login #backtoblog';
		}
		if ( $this->settings->get( 'login.hide_language_switcher', false ) ) {
			$hide_css[] = '.login .language-switcher';
		}

		if ( ! empty( $hide_css ) ) {
			$css .= "/* Hidden Elements */\n";
			$css .= implode( ",\n", $hide_css ) . " { display: none !important; }\n";
		}

		// === BUSINESS NAME BELOW FORM ===
		$business_name = $this->settings->get( 'login.business_name', '' );
		if ( $business_name ) {
			$css .= "/* Business Name */\n";
			$css .= "#login::after {\n";
			$css .= "  content: '" . esc_attr( $business_name ) . "';\n";
			$css .= "  display: block;\n";
			$css .= "  text-align: center;\n";
			$css .= "  margin-top: 20px;\n";
			$css .= "  color: #50575e;\n";
			$css .= "  font-size: 13px;\n";
			$css .= "}\n";
		}

		return $css;
	}

	/**
	 * Get CSS variables for login page.
	 *
	 * @return array
	 */
	public function get_css_variables() {
		$variables = array();

		/**
		 * Filters the CSS variables for the login page.
		 *
		 * @since 1.0.0
		 *
		 * @param array $variables CSS variables array.
		 */
		return apply_filters( 'uswl_login_css_variables', $variables );
	}

	/**
	 * Filter the login header URL.
	 *
	 * @param string $url Original URL.
	 * @return string
	 */
	public function filter_header_url( $url ) {
		if ( ! $this->should_apply() ) {
			return $url;
		}

		$business_url = $this->settings->get( 'login.business_url', '' );

		if ( $business_url ) {
			$url = $business_url;
		} elseif ( $this->settings->get( 'login.logo_url', '' ) ) {
			// If custom logo but no URL, link to home.
			$url = home_url( '/' );
		}

		/**
		 * Filters the login logo URL.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url Logo URL.
		 */
		return apply_filters( 'uswl_login_logo_url', $url );
	}

	/**
	 * Filter the login header text.
	 *
	 * @param string $text Original text.
	 * @return string
	 */
	public function filter_header_text( $text ) {
		if ( ! $this->should_apply() ) {
			return $text;
		}

		$business_name = $this->settings->get( 'login.business_name', '' );

		if ( $business_name ) {
			$text = $business_name;
		} elseif ( $this->settings->get( 'login.logo_url', '' ) ) {
			// If custom logo but no name, use site name.
			$text = get_bloginfo( 'name' );
		}

		/**
		 * Filters the login logo title text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text Logo title text.
		 */
		return apply_filters( 'uswl_login_logo_title', $text );
	}
}
