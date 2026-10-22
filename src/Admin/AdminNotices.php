<?php
/**
 * Admin Notices Class.
 *
 * Handles all admin dashboard notices for the plugin.
 *
 * @package Unstoppable_White_Label
 */

namespace Unstoppable\WhiteLabel\Admin;

use Unstoppable\WhiteLabel\Plugin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AdminNotices Class.
 *
 * @since 1.0.0
 */
class AdminNotices {

	/**
	 * Plugin prefix.
	 *
	 * @var string
	 */
	private $prefix = 'uswl';

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private $plugin_name = 'Unstoppable White Label';

	/**
	 * Display all active notices.
	 *
	 * @return void
	 */
	public function display_notices() {
		// Only show to admins.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Preview mode bar (always show when active).
		$this->maybe_show_preview_bar();

		// Welcome/Setup notice (shows once after activation).
		$this->maybe_show_welcome_notice();

		// Review request (shows after 7 days of use).
		$this->maybe_show_review_notice();
	}

	/**
	 * Show preview mode exit bar when preview mode is active.
	 *
	 * @return void
	 */
	private function maybe_show_preview_bar() {
		$capabilities = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'capabilities' );
		
		if ( ! $capabilities || ! $capabilities->is_preview_mode() ) {
			return;
		}

		?>
		<div id="uswl-preview-bar" class="uswl-preview-bar">
			<span class="uswl-preview-bar__text">
				<span class="dashicons dashicons-visibility"></span>
				<strong><?php esc_html_e( 'Preview Mode Active', 'unstoppable-white-label' ); ?></strong>
				<span class="uswl-preview-bar__sub">— <?php esc_html_e( 'You are viewing the site as your clients see it.', 'unstoppable-white-label' ); ?></span>
			</span>
			<button type="button" id="uswl-exit-preview" class="button uswl-preview-bar__button">
				<?php esc_html_e( 'Exit Preview Mode', 'unstoppable-white-label' ); ?>
			</button>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=unstoppable-white-label&uswl_exit_preview=1' ), 'uswl_exit_preview' ) ); ?>" class="uswl-preview-bar__fallback">
				<?php esc_html_e( 'Exit without JavaScript', 'unstoppable-white-label' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Get the standalone "exit preview" JavaScript.
	 *
	 * Self-contained so it works on admin screens where uswl-admin.js is not
	 * enqueued. Attached to jQuery via wp_add_inline_script().
	 *
	 * @return string
	 */
	private function get_preview_exit_script() {
		return "
			jQuery(document).ready(function($) {
				$(document).on('click', '#uswl-exit-preview', function(e) {
					e.preventDefault();
					var \$button = $(this);
					if (\$button.prop('disabled')) { return; }
					\$button.prop('disabled', true).text('Please wait...');
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'uswl_preview_mode',
							preview_action: 'disable',
							nonce: uswlNotices.nonce
						}
					}).done(function(response) {
						if (response && response.success) {
							window.onbeforeunload = null;
							$(window).off('beforeunload');
							window.location.reload();
							return;
						}
						\$button.prop('disabled', false).text('Exit Preview Mode');
						window.alert((response && response.data && response.data.message) || uswlNotices.exitFailed);
					}).fail(function() {
						\$button.prop('disabled', false).text('Exit Preview Mode');
						window.alert(uswlNotices.exitFailed);
					});
				});
			});
		";
	}

	/**
	 * Handle the no-JavaScript "exit preview" fallback link.
	 *
	 * Hooked to admin_init so it can redirect before output starts.
	 *
	 * @return void
	 */
	public function maybe_handle_exit_fallback() {
		if ( ! isset( $_GET['uswl_exit_preview'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$uswl_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $uswl_nonce, 'uswl_exit_preview' ) ) {
			return;
		}

		$capabilities = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'capabilities' );

		if ( $capabilities ) {
			$capabilities->disable_preview_mode();
		}

		wp_safe_redirect( admin_url( 'admin.php?page=unstoppable-white-label' ) );
		exit;
	}

	/**
	 * Get the preview bar CSS.
	 *
	 * Returned as a string so it can be passed to wp_add_inline_style()
	 * (inline <style> blocks are rejected by WordPress.org Plugin Check).
	 *
	 * @return string
	 */
	private function get_preview_bar_css() {
		return '
			.uswl-preview-bar {
				position: fixed;
				bottom: 0;
				left: 0;
				right: 0;
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: #fff;
				padding: 12px 20px;
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 20px;
				z-index: 99999;
				box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
			}
			.uswl-preview-bar__text {
				display: flex;
				align-items: center;
				gap: 8px;
			}
			.uswl-preview-bar .dashicons-visibility {
				font-size: 20px;
			}
			.uswl-preview-bar__sub {
				opacity: 0.8;
			}
			.uswl-preview-bar__button {
				background: #fff !important;
				color: #764ba2 !important;
				border: none !important;
				font-weight: 600;
			}
			.uswl-preview-bar__fallback {
				color: #fff;
				opacity: 0.75;
				font-size: 12px;
				text-decoration: underline;
			}
			body.wp-admin { padding-bottom: 60px; }
		';
	}

	/**
	 * Welcome/Setup notice after plugin activation.
	 *
	 * @return void
	 */
	private function maybe_show_welcome_notice() {
		// Check if already dismissed.
		if ( get_option( $this->prefix . '_welcome_notice_dismissed' ) ) {
			return;
		}

		// Auto-dismiss if user is on the plugin settings page (they found it!).
		$screen = get_current_screen();
		if ( $screen && false !== strpos( $screen->id, 'unstoppable-white-label' ) ) {
			update_option( $this->prefix . '_welcome_notice_dismissed', true );
			return;
		}

		// Only show on dashboard.
		if ( ! $screen || 'dashboard' !== $screen->id ) {
			return;
		}

		?>
		<div class="notice notice-success is-dismissible <?php echo esc_attr( $this->prefix ); ?>-notice" data-notice="welcome">
			<p>
				<strong><?php echo esc_html( $this->plugin_name ); ?></strong> —
				<?php esc_html_e( 'Thanks for installing! Get started by configuring your settings.', 'unstoppable-white-label' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=unstoppable-white-label' ) ); ?>" class="button button-primary" style="margin-left: 10px;">
					<?php esc_html_e( 'Go to Settings', 'unstoppable-white-label' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Review request notice after 7 days.
	 *
	 * @return void
	 */
	private function maybe_show_review_notice() {
		// Check if already dismissed.
		if ( get_option( $this->prefix . '_review_notice_dismissed' ) ) {
			return;
		}

		// Check install date.
		$install_date = get_option( $this->prefix . '_installed_at' );
		if ( ! $install_date ) {
			return;
		}

		// Show after 7 days.
		if ( time() < ( $install_date + ( 7 * DAY_IN_SECONDS ) ) ) {
			return;
		}

		// Only show on plugin pages.
		$screen = get_current_screen();
		if ( ! $screen || ( false === strpos( $screen->id, $this->prefix ) && false === strpos( $screen->id, 'unstoppable-white-label' ) ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible <?php echo esc_attr( $this->prefix ); ?>-notice" data-notice="review">
			<p>
				<?php
				printf(
					/* translators: %s: Plugin name */
					esc_html__( 'Enjoying %s? Please leave a review to help others discover the plugin!', 'unstoppable-white-label' ),
					'<strong>' . esc_html( $this->plugin_name ) . '</strong>'
				);
				?>
				<a href="https://wordpress.org/support/plugin/unstoppable-white-label/reviews/#new-post" target="_blank" class="button button-primary" style="margin-left: 10px;">
					<?php esc_html_e( 'Leave a Review', 'unstoppable-white-label' ); ?>
				</a>
				<button type="button" class="button <?php echo esc_attr( $this->prefix ); ?>-dismiss-notice" data-notice="review" style="margin-left: 5px;">
					<?php esc_html_e( 'Maybe Later', 'unstoppable-white-label' ); ?>
				</button>
			</p>
		</div>
		<?php
	}

	/**
	 * AJAX handler to dismiss notices.
	 *
	 * @return void
	 */
	public function ajax_dismiss_notice() {
		// Verify nonce.
		check_ajax_referer( 'uswl_admin_nonce', 'nonce' );

		// Verify permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$notice = isset( $_POST['notice'] ) ? sanitize_key( $_POST['notice'] ) : '';

		if ( empty( $notice ) ) {
			wp_send_json_error( 'Invalid notice.' );
		}

		// Save dismissal.
		update_option( $this->prefix . '_' . $notice . '_notice_dismissed', true );

		wp_send_json_success();
	}

	/**
	 * Enqueue notice scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Only load on admin pages where notices might show.
		if ( ! is_admin() ) {
			return;
		}

		// Add a nonce for notice dismissal and preview mode.
		wp_localize_script( 'jquery', 'uswlNotices', array(
			'nonce'      => wp_create_nonce( 'uswl_admin_nonce' ),
			'exitFailed' => __( 'Could not exit preview mode. Preview expires automatically after 10 minutes.', 'unstoppable-white-label' ),
		) );

		// Preview bar styles + exit handler must load on EVERY admin page, not
		// just the plugin settings page — the bar renders site-wide, but
		// uswl-admin.js is only enqueued on the settings screen.
		$capabilities = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'capabilities' );

		if ( $capabilities && $capabilities->is_preview_mode() ) {
			wp_register_style( 'uswl-preview-bar', false, array(), USWL_VERSION );
			wp_enqueue_style( 'uswl-preview-bar' );
			wp_add_inline_style( 'uswl-preview-bar', $this->get_preview_bar_css() );

			wp_add_inline_script( 'jquery', $this->get_preview_exit_script() );
		}

		// Inline script for notice dismissal.
		$script = "
			jQuery(document).ready(function($) {
				// Handle WordPress default dismiss button.
				$(document).on('click', '.uswl-notice .notice-dismiss', function() {
					var notice = $(this).closest('.uswl-notice').data('notice');
					if (notice) {
						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'uswl_dismiss_notice',
								notice: notice,
								nonce: uswlNotices.nonce
							}
						});
					}
				});

				// Handle custom dismiss buttons.
				$(document).on('click', '.uswl-dismiss-notice', function(e) {
					e.preventDefault();
					var notice = $(this).data('notice');
					$(this).closest('.uswl-notice').fadeOut();
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'uswl_dismiss_notice',
							notice: notice,
							nonce: uswlNotices.nonce
						}
					});
				});
			});
		";

		wp_add_inline_script( 'jquery', $script );
	}
}
