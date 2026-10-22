<?php
/**
 * Admin Section Template
 *
 * @package Unstoppable_White_Label
 *
 * @var array $settings Current settings.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uswl_admin = $settings['admin'] ?? array();
?>

<!-- Admin Bar -->
<h3 style="margin-top: 0;"><?php esc_html_e( 'Admin Bar', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-howdy-text"><?php esc_html_e( 'Greeting Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-howdy-text" 
					   name="admin[howdy_text]" 
					   value="<?php echo esc_attr( $uswl_admin['howdy_text'] ?? '' ); ?>" 
					   class="regular-text"
					   placeholder="Howdy">
				<p class="description">
					<?php esc_html_e( 'Replaces "Howdy" in the admin bar greeting.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'WordPress Logo', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-hide-wp-logo" style="display: block; margin-bottom: 8px;">
					<input type="checkbox" 
						   id="uswl-hide-wp-logo" 
						   name="admin[hide_wp_logo]" 
						   value="1" 
						   <?php checked( ! empty( $uswl_admin['hide_wp_logo'] ) ); ?>>
					<?php esc_html_e( 'Remove the WordPress logo from the admin bar', 'unstoppable-white-label' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Site Icon', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-hide-site-icon" style="display: block; margin-bottom: 8px;">
					<input type="checkbox" 
						   id="uswl-hide-site-icon" 
						   name="admin[hide_site_icon]" 
						   value="1" 
						   <?php checked( ! empty( $uswl_admin['hide_site_icon'] ) ); ?>>
					<?php esc_html_e( 'Hide the site icon next to the site name', 'unstoppable-white-label' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Removes the small icon WordPress shows beside the site name in the admin bar. The site name and its menu stay visible.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-custom-logo"><?php esc_html_e( 'Replace Logo', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<div class="uswl-media-field">
					<input type="hidden" 
						   id="uswl-custom-logo-id" 
						   name="admin[custom_logo_id]" 
						   value="<?php echo esc_attr( $uswl_admin['custom_logo_id'] ?? 0 ); ?>">
					<input type="hidden" 
						   id="uswl-custom-logo-url" 
						   name="admin[custom_logo_url]" 
						   value="<?php echo esc_url( $uswl_admin['custom_logo_url'] ?? '' ); ?>">
					
					<div class="uswl-media-preview uswl-media-preview-small" id="uswl-custom-logo-preview">
						<?php if ( ! empty( $uswl_admin['custom_logo_url'] ) ) : ?>
							<img src="<?php echo esc_url( $uswl_admin['custom_logo_url'] ); ?>" alt="">
						<?php endif; ?>
					</div>
					
					<button type="button" class="button uswl-media-select" data-target="custom-logo">
						<?php echo empty( $uswl_admin['custom_logo_url'] ) ? esc_html__( 'Choose Logo', 'unstoppable-white-label' ) : esc_html__( 'Change Logo', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button uswl-media-remove" data-target="custom-logo" <?php echo empty( $uswl_admin['custom_logo_url'] ) ? 'style="display:none;"' : ''; ?>>
						<?php esc_html_e( 'Remove', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Replace the WordPress logo in the admin bar with your own. Recommended height: 20px.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Logo Link', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-remove-logo-link" style="display: block; margin-bottom: 8px;">
					<input type="checkbox" 
						   id="uswl-remove-logo-link" 
						   name="admin[remove_logo_link]" 
						   value="1" 
						   <?php checked( ! empty( $uswl_admin['remove_logo_link'] ) ); ?>>
					<?php esc_html_e( 'Remove the link from the admin bar logo', 'unstoppable-white-label' ); ?>
				</label>
				
				<div id="uswl-logo-link-wrapper" style="margin-top: 10px; <?php echo ! empty( $uswl_admin['remove_logo_link'] ) ? 'display:none;' : ''; ?>">
					<input type="url" 
						   id="uswl-logo-link" 
						   name="admin[logo_link]" 
						   value="<?php echo esc_url( $uswl_admin['logo_link'] ?? '' ); ?>" 
						   class="regular-text"
						   placeholder="https://example.com">
					<p class="description">
						<?php esc_html_e( 'Custom URL for the admin bar logo. Leave empty to link to dashboard.', 'unstoppable-white-label' ); ?>
					</p>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!-- Footer -->
<h3><?php esc_html_e( 'Footer', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-footer-text"><?php esc_html_e( 'Footer Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-footer-text" 
					   name="admin[footer_text]" 
					   value="<?php echo esc_attr( $uswl_admin['footer_text'] ?? '' ); ?>" 
					   class="large-text"
					   placeholder="<?php esc_attr_e( 'Powered by Your Company', 'unstoppable-white-label' ); ?>">
				<p class="description">
					<?php esc_html_e( 'Replaces "Thank you for creating with WordPress" in the admin footer.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'WordPress Version', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-hide-wp-version">
					<input type="checkbox" 
						   id="uswl-hide-wp-version" 
						   name="admin[hide_wp_version]" 
						   value="1" 
						   <?php checked( ! empty( $uswl_admin['hide_wp_version'] ) ); ?>>
					<?php esc_html_e( 'Remove the WordPress version from the admin footer', 'unstoppable-white-label' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>

<!-- Admin Color Scheme -->
<h3><?php esc_html_e( 'Color Scheme', 'unstoppable-white-label' ); ?></h3>
<p class="description" style="margin-bottom: 15px;">
	<?php esc_html_e( 'Customize the admin color scheme. Leave colors empty to use WordPress defaults.', 'unstoppable-white-label' ); ?>
</p>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Enable Color Scheme', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-enable-color-scheme">
					<input type="checkbox" 
						   id="uswl-enable-color-scheme" 
						   name="admin[enable_color_scheme]" 
						   value="1" 
						   <?php checked( ! empty( $uswl_admin['enable_color_scheme'] ) ); ?>>
					<?php esc_html_e( 'Apply custom color scheme to admin area', 'unstoppable-white-label' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-menu-bg"><?php esc_html_e( 'Menu Background', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-menu-bg" 
					   name="admin[menu_background]" 
					   value="<?php echo esc_attr( $uswl_admin['menu_background'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#1d2327">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-menu-text"><?php esc_html_e( 'Menu Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-menu-text" 
					   name="admin[menu_text]" 
					   value="<?php echo esc_attr( $uswl_admin['menu_text'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#f0f0f1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-menu-highlight"><?php esc_html_e( 'Menu Highlight', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-menu-highlight" 
					   name="admin[menu_highlight]" 
					   value="<?php echo esc_attr( $uswl_admin['menu_highlight'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-submenu-bg"><?php esc_html_e( 'Submenu Background', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-submenu-bg" 
					   name="admin[submenu_background]" 
					   value="<?php echo esc_attr( $uswl_admin['submenu_background'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2c3338">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-submenu-text"><?php esc_html_e( 'Submenu Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-submenu-text" 
					   name="admin[submenu_text]" 
					   value="<?php echo esc_attr( $uswl_admin['submenu_text'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#f0f0f1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-submenu-highlight"><?php esc_html_e( 'Submenu Highlight', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-submenu-highlight" 
					   name="admin[submenu_highlight]" 
					   value="<?php echo esc_attr( $uswl_admin['submenu_highlight'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-notification"><?php esc_html_e( 'Notifications', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-notification" 
					   name="admin[notification_color]" 
					   value="<?php echo esc_attr( $uswl_admin['notification_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#d63638">
				<p class="description"><?php esc_html_e( 'Color for update bubbles and notifications.', 'unstoppable-white-label' ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-admin-link"><?php esc_html_e( 'Links', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-admin-link" 
					   name="admin[link_color]" 
					   value="<?php echo esc_attr( $uswl_admin['link_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-admin-button"><?php esc_html_e( 'Buttons', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-admin-button" 
					   name="admin[button_color]" 
					   value="<?php echo esc_attr( $uswl_admin['button_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>
	</tbody>
</table>
