<?php
/**
 * Settings Page Template
 *
 * @package Unstoppable_White_Label
 *
 * @var array $uswl_settings Current settings.
 * @var array $uswl_sections Available sections.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uswl_save_message = '';
$uswl_save_error = '';

// Handle form submission.
if ( isset( $_POST['uswl_save_settings'] ) ) {
	
	if ( ! isset( $_POST['uswl_nonce'] ) ) {
		$uswl_save_error = __( 'Security check failed. Please try again.', 'unstoppable-white-label' );
	} elseif ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['uswl_nonce'] ) ), 'uswl_settings_nonce' ) ) {
		$uswl_save_error = __( 'Security check failed. Please refresh the page and try again.', 'unstoppable-white-label' );
	} elseif ( ! current_user_can( 'manage_options' ) ) {
		$uswl_save_error = __( 'You do not have permission to save settings.', 'unstoppable-white-label' );
	} else {
		// Build settings array.
		$uswl_new_settings = array();
		
		// General.
		$uswl_new_settings['general'] = array(
			'enabled'             => isset( $_POST['general']['enabled'] ) ? 1 : 0,
			'admin_ids'           => isset( $_POST['general']['admin_ids'] ) && is_array( $_POST['general']['admin_ids'] ) 
				? array_map( 'absint', wp_unslash( $_POST['general']['admin_ids'] ) ) 
				: array(),
			'delete_on_uninstall' => isset( $_POST['general']['delete_on_uninstall'] ) ? 1 : 0,
		);
		
		// Login.
		$uswl_form_position = isset( $_POST['login']['form_position'] ) ? sanitize_key( wp_unslash( $_POST['login']['form_position'] ) ) : 'center';
		if ( ! in_array( $uswl_form_position, array( 'center', 'left', 'right' ), true ) ) {
			$uswl_form_position = 'center';
		}
		
		$uswl_new_settings['login'] = array(
			// Logo
			'logo_id'                 => isset( $_POST['login']['logo_id'] ) ? absint( $_POST['login']['logo_id'] ) : 0,
			'logo_url'                => isset( $_POST['login']['logo_url'] ) ? esc_url_raw( wp_unslash( $_POST['login']['logo_url'] ) ) : '',
			'logo_width'              => isset( $_POST['login']['logo_width'] ) && $_POST['login']['logo_width'] !== '' ? absint( $_POST['login']['logo_width'] ) : '',
			'logo_height'             => isset( $_POST['login']['logo_height'] ) && $_POST['login']['logo_height'] !== '' ? absint( $_POST['login']['logo_height'] ) : '',
			'business_name'           => isset( $_POST['login']['business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['login']['business_name'] ) ) : '',
			'business_url'            => isset( $_POST['login']['business_url'] ) ? esc_url_raw( wp_unslash( $_POST['login']['business_url'] ) ) : '',
			// Layout
			'form_position'           => $uswl_form_position,
			'background_image_id'     => isset( $_POST['login']['background_image_id'] ) ? absint( $_POST['login']['background_image_id'] ) : 0,
			'background_image_url'    => isset( $_POST['login']['background_image_url'] ) ? esc_url_raw( wp_unslash( $_POST['login']['background_image_url'] ) ) : '',
			// Hide elements
			'hide_remember_me'        => isset( $_POST['login']['hide_remember_me'] ) ? 1 : 0,
			'hide_lost_password'      => isset( $_POST['login']['hide_lost_password'] ) ? 1 : 0,
			'hide_back_to_site'       => isset( $_POST['login']['hide_back_to_site'] ) ? 1 : 0,
			'hide_language_switcher'  => isset( $_POST['login']['hide_language_switcher'] ) ? 1 : 0,
			// Color scheme
			'background_color'        => isset( $_POST['login']['background_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['background_color'] ) ) : '',
			'form_background_color'   => isset( $_POST['login']['form_background_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['form_background_color'] ) ) : '',
			'form_text_color'         => isset( $_POST['login']['form_text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['form_text_color'] ) ) : '',
			'link_color'              => isset( $_POST['login']['link_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['link_color'] ) ) : '',
			'button_background_color' => isset( $_POST['login']['button_background_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['button_background_color'] ) ) : '',
			'button_text_color'       => isset( $_POST['login']['button_text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['login']['button_text_color'] ) ) : '',
		);
		
		// Admin.
		$uswl_new_settings['admin'] = array(
			// Admin bar
			'howdy_text'          => isset( $_POST['admin']['howdy_text'] ) ? sanitize_text_field( wp_unslash( $_POST['admin']['howdy_text'] ) ) : '',
			'hide_wp_logo'        => isset( $_POST['admin']['hide_wp_logo'] ) ? 1 : 0,
			'hide_site_icon'      => isset( $_POST['admin']['hide_site_icon'] ) ? 1 : 0,
			'custom_logo_id'      => isset( $_POST['admin']['custom_logo_id'] ) ? absint( $_POST['admin']['custom_logo_id'] ) : 0,
			'custom_logo_url'     => isset( $_POST['admin']['custom_logo_url'] ) ? esc_url_raw( wp_unslash( $_POST['admin']['custom_logo_url'] ) ) : '',
			'remove_logo_link'    => isset( $_POST['admin']['remove_logo_link'] ) ? 1 : 0,
			'logo_link'           => isset( $_POST['admin']['logo_link'] ) ? esc_url_raw( wp_unslash( $_POST['admin']['logo_link'] ) ) : '',
			// Footer
			'footer_text'         => isset( $_POST['admin']['footer_text'] ) ? sanitize_text_field( wp_unslash( $_POST['admin']['footer_text'] ) ) : '',
			'hide_wp_version'     => isset( $_POST['admin']['hide_wp_version'] ) ? 1 : 0,
			// Color scheme
			'enable_color_scheme' => isset( $_POST['admin']['enable_color_scheme'] ) ? 1 : 0,
			'menu_background'     => isset( $_POST['admin']['menu_background'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['menu_background'] ) ) : '',
			'menu_text'           => isset( $_POST['admin']['menu_text'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['menu_text'] ) ) : '',
			'menu_highlight'      => isset( $_POST['admin']['menu_highlight'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['menu_highlight'] ) ) : '',
			'submenu_background'  => isset( $_POST['admin']['submenu_background'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['submenu_background'] ) ) : '',
			'submenu_text'        => isset( $_POST['admin']['submenu_text'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['submenu_text'] ) ) : '',
			'submenu_highlight'   => isset( $_POST['admin']['submenu_highlight'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['submenu_highlight'] ) ) : '',
			'notification_color'  => isset( $_POST['admin']['notification_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['notification_color'] ) ) : '',
			'link_color'          => isset( $_POST['admin']['link_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['link_color'] ) ) : '',
			'button_color'        => isset( $_POST['admin']['button_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['admin']['button_color'] ) ) : '',
		);
		
		// Visibility.
		$uswl_new_settings['visibility'] = array(
			'menus'        => isset( $_POST['visibility']['menus'] ) && is_array( $_POST['visibility']['menus'] ) 
				? array_map( 'sanitize_text_field', wp_unslash( $_POST['visibility']['menus'] ) ) 
				: array(),
			'adminbar'     => isset( $_POST['visibility']['adminbar'] ) && is_array( $_POST['visibility']['adminbar'] ) 
				? array_map( 'sanitize_text_field', wp_unslash( $_POST['visibility']['adminbar'] ) ) 
				: array(),
			'plugins'      => isset( $_POST['visibility']['plugins'] ) && is_array( $_POST['visibility']['plugins'] ) 
				? array_map( 'sanitize_text_field', wp_unslash( $_POST['visibility']['plugins'] ) ) 
				: array(),
			'themes'       => isset( $_POST['visibility']['themes'] ) && is_array( $_POST['visibility']['themes'] ) 
				? array_map( 'sanitize_text_field', wp_unslash( $_POST['visibility']['themes'] ) ) 
				: array(),
			'hide_notices' => isset( $_POST['visibility']['hide_notices'] ) ? 1 : 0,
		);
		
		// Menus (renamed).
		$uswl_renamed_menus = array();
		if ( isset( $_POST['menus']['renamed'] ) && is_array( $_POST['menus']['renamed'] ) ) {
			$uswl_posted_menus = map_deep( wp_unslash( $_POST['menus']['renamed'] ), 'sanitize_text_field' );
			foreach ( $uswl_posted_menus as $uswl_slug => $uswl_new_name ) {
				$uswl_slug = sanitize_key( $uswl_slug );
				if ( ! empty( $uswl_new_name ) ) {
					$uswl_renamed_menus[ $uswl_slug ] = $uswl_new_name;
				}
			}
		}
		$uswl_new_settings['menus'] = array(
			'renamed' => $uswl_renamed_menus,
		);
		
		// Dashboard.
		$uswl_new_settings['dashboard'] = array(
			'hidden_widgets'        => isset( $_POST['dashboard']['hidden_widgets'] ) && is_array( $_POST['dashboard']['hidden_widgets'] )
				? array_map( 'sanitize_key', wp_unslash( $_POST['dashboard']['hidden_widgets'] ) )
				: array(),
			'custom_widget_enabled' => isset( $_POST['dashboard']['custom_widget_enabled'] ) ? 1 : 0,
			'custom_widget_title'   => isset( $_POST['dashboard']['custom_widget_title'] ) 
				? sanitize_text_field( wp_unslash( $_POST['dashboard']['custom_widget_title'] ) ) 
				: '',
			'custom_widget_content' => isset( $_POST['dashboard']['custom_widget_content'] ) 
				? wp_kses_post( wp_unslash( $_POST['dashboard']['custom_widget_content'] ) ) 
				: '',
		);
		
		// Save to database.
		$uswl_result = update_option( 'uswl_settings', $uswl_new_settings );
		
		// Update local variable for display.
		$uswl_settings = $uswl_new_settings;
		$uswl_save_message = __( 'Settings saved successfully!', 'unstoppable-white-label' );
	}
}
?>
<div class="wrap uswl-settings-wrap">
	<h1><?php esc_html_e( 'Unstoppable White Label', 'unstoppable-white-label' ); ?></h1>
	
	<?php
	// Check if user is a WL admin (can use preview mode).
	$uswl_capabilities = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'capabilities' );
	$uswl_is_wl_admin = $uswl_capabilities && $uswl_capabilities->is_whitelabel_admin();
	$uswl_is_preview = $uswl_capabilities && $uswl_capabilities->is_preview_mode();
	
	if ( $uswl_is_wl_admin && ! $uswl_is_preview ) :
	?>
	<div class="uswl-preview-box" style="background: #f0f6fc; border: 1px solid #72aee6; border-left: 4px solid #72aee6; padding: 12px 15px; margin: 15px 0; display: flex; align-items: center; justify-content: space-between;">
		<div>
			<strong><?php esc_html_e( 'Preview Mode', 'unstoppable-white-label' ); ?></strong>
			<span style="color: #50575e; margin-left: 10px;"><?php esc_html_e( 'See what your clients will see (10 min session).', 'unstoppable-white-label' ); ?></span>
		</div>
		<button type="button" class="button button-primary" id="uswl-enable-preview">
			<?php esc_html_e( 'Enter Preview Mode', 'unstoppable-white-label' ); ?>
		</button>
	</div>
	<?php endif; ?>

	<?php if ( $uswl_save_message ) : ?>
		<div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( $uswl_save_message ); ?></strong></p></div>
	<?php endif; ?>
	
	<?php if ( $uswl_save_error ) : ?>
		<div class="notice notice-error is-dismissible"><p><strong><?php echo esc_html( $uswl_save_error ); ?></strong></p></div>
	<?php endif; ?>

	<?php
	// Ensure sections are available.
	if ( ! isset( $uswl_sections ) || empty( $uswl_sections ) ) {
		$uswl_admin_page_obj = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'admin_page' );
		$uswl_sections = $uswl_admin_page_obj ? $uswl_admin_page_obj->get_sections() : array();
	}
	if ( ! isset( $uswl_settings ) || empty( $uswl_settings ) ) {
		$uswl_settings = get_option( 'uswl_settings', array() );
	}
	?>

	<div class="uswl-settings-container">
		<nav class="uswl-settings-nav" role="navigation">
			<ul>
				<?php foreach ( $uswl_sections as $uswl_section_id => $uswl_section ) : ?>
					<li>
						<a href="#<?php echo esc_attr( $uswl_section_id ); ?>" class="uswl-nav-item" data-section="<?php echo esc_attr( $uswl_section_id ); ?>">
							<span class="dashicons <?php echo esc_attr( $uswl_section['icon'] ?? 'dashicons-admin-generic' ); ?>"></span>
							<span class="uswl-nav-label"><?php echo esc_html( $uswl_section['title'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<main class="uswl-settings-content">
			<form id="uswl-settings-form" method="post" action="">
				<?php wp_nonce_field( 'uswl_settings_nonce', 'uswl_nonce' ); ?>

				<?php foreach ( $uswl_sections as $uswl_section_id => $uswl_section ) : ?>
					<section id="<?php echo esc_attr( $uswl_section_id ); ?>" class="uswl-section">
						<?php
						$uswl_admin_page = \Unstoppable\WhiteLabel\Plugin::instance()->loader()->get_module( 'admin_page' );
						$uswl_admin_page->render_section( $uswl_section_id, $uswl_settings );
						?>
					</section>
				<?php endforeach; ?>

				<div class="uswl-form-actions">
					<button type="submit" name="uswl_save_settings" class="button button-primary button-hero">
						<?php esc_html_e( 'Save Settings', 'unstoppable-white-label' ); ?>
					</button>
				</div>
			</form>
		</main>
	</div>
</div>
