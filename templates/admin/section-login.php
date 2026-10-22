<?php
/**
 * Login Section Template
 *
 * @package Unstoppable_White_Label
 *
 * @var array $settings Current settings.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uswl_login = $settings['login'] ?? array();
?>

<!-- Logo Settings -->
<h3 style="margin-top: 0;"><?php esc_html_e( 'Logo', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-login-logo"><?php esc_html_e( 'Login Logo', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<div class="uswl-media-field">
					<input type="hidden" 
						   id="uswl-login-logo-id" 
						   name="login[logo_id]" 
						   value="<?php echo esc_attr( $uswl_login['logo_id'] ?? 0 ); ?>">
					<input type="hidden" 
						   id="uswl-login-logo-url" 
						   name="login[logo_url]" 
						   value="<?php echo esc_url( $uswl_login['logo_url'] ?? '' ); ?>">
					
					<div class="uswl-media-preview" id="uswl-login-logo-preview">
						<?php if ( ! empty( $uswl_login['logo_url'] ) ) : ?>
							<img src="<?php echo esc_url( $uswl_login['logo_url'] ); ?>" alt="">
						<?php endif; ?>
					</div>
					
					<button type="button" class="button uswl-media-select" data-target="login-logo">
						<?php echo empty( $uswl_login['logo_url'] ) ? esc_html__( 'Select Image', 'unstoppable-white-label' ) : esc_html__( 'Change Image', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button uswl-media-remove" data-target="login-logo" <?php echo empty( $uswl_login['logo_url'] ) ? 'style="display:none;"' : ''; ?>>
						<?php esc_html_e( 'Remove', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Replaces the WordPress logo on the login page.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-logo-width"><?php esc_html_e( 'Logo Width', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="number" 
					   id="uswl-logo-width" 
					   name="login[logo_width]" 
					   value="<?php echo esc_attr( $uswl_login['logo_width'] ?? '' ); ?>" 
					   class="small-text"
					   min="0"
					   max="500"
					   placeholder="auto">
				<span>px</span>
				<p class="description">
					<?php esc_html_e( 'Leave empty for auto width.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-logo-height"><?php esc_html_e( 'Logo Height', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="number" 
					   id="uswl-logo-height" 
					   name="login[logo_height]" 
					   value="<?php echo esc_attr( $uswl_login['logo_height'] ?? '' ); ?>" 
					   class="small-text"
					   min="0"
					   max="500"
					   placeholder="auto">
				<span>px</span>
				<p class="description">
					<?php esc_html_e( 'Leave empty for auto height. Default WordPress logo is 84px.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-business-name"><?php esc_html_e( 'Logo Title', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-business-name" 
					   name="login[business_name]" 
					   value="<?php echo esc_attr( $uswl_login['business_name'] ?? '' ); ?>" 
					   class="regular-text"
					   placeholder="<?php esc_attr_e( 'Your Company Name', 'unstoppable-white-label' ); ?>">
				<p class="description">
					<?php esc_html_e( 'Used as the logo alt text and title attribute.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-business-url"><?php esc_html_e( 'Logo Link', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="url" 
					   id="uswl-business-url" 
					   name="login[business_url]" 
					   value="<?php echo esc_url( $uswl_login['business_url'] ?? '' ); ?>" 
					   class="regular-text"
					   placeholder="https://example.com">
				<p class="description">
					<?php esc_html_e( 'Logo click destination. Defaults to your site home if empty.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<!-- Layout Settings -->
<h3><?php esc_html_e( 'Layout', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-login-form-position"><?php esc_html_e( 'Form Position', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<select id="uswl-login-form-position" name="login[form_position]">
					<option value="center" <?php selected( $uswl_login['form_position'] ?? 'center', 'center' ); ?>>
						<?php esc_html_e( 'Center (Default)', 'unstoppable-white-label' ); ?>
					</option>
					<option value="left" <?php selected( $uswl_login['form_position'] ?? 'center', 'left' ); ?>>
						<?php esc_html_e( 'Left Side', 'unstoppable-white-label' ); ?>
					</option>
					<option value="right" <?php selected( $uswl_login['form_position'] ?? 'center', 'right' ); ?>>
						<?php esc_html_e( 'Right Side', 'unstoppable-white-label' ); ?>
					</option>
				</select>
				<p class="description">
					<?php esc_html_e( 'Position the login form on the page. Great for showing off background images.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-login-bg-image"><?php esc_html_e( 'Background Image', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<div class="uswl-media-field">
					<input type="hidden" 
						   id="uswl-login-bg-image-id" 
						   name="login[background_image_id]" 
						   value="<?php echo esc_attr( $uswl_login['background_image_id'] ?? 0 ); ?>">
					<input type="hidden" 
						   id="uswl-login-bg-image-url" 
						   name="login[background_image_url]" 
						   value="<?php echo esc_url( $uswl_login['background_image_url'] ?? '' ); ?>">
					
					<div class="uswl-media-preview uswl-media-preview-large" id="uswl-login-bg-image-preview">
						<?php if ( ! empty( $uswl_login['background_image_url'] ) ) : ?>
							<img src="<?php echo esc_url( $uswl_login['background_image_url'] ); ?>" alt="">
						<?php endif; ?>
					</div>
					
					<button type="button" class="button uswl-media-select" data-target="login-bg-image">
						<?php echo empty( $uswl_login['background_image_url'] ) ? esc_html__( 'Select Image', 'unstoppable-white-label' ) : esc_html__( 'Change Image', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button uswl-media-remove" data-target="login-bg-image" <?php echo empty( $uswl_login['background_image_url'] ) ? 'style="display:none;"' : ''; ?>>
						<?php esc_html_e( 'Remove', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Optional background image for the login page.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr id="uswl-bg-size-row" style="<?php echo empty( $uswl_login['background_image_url'] ) ? 'display:none;' : ''; ?>">
			<th scope="row">
				<label for="uswl-login-bg-size"><?php esc_html_e( 'Background Size', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<select id="uswl-login-bg-size" name="login[background_size]">
					<option value="cover" <?php selected( $uswl_login['background_size'] ?? 'cover', 'cover' ); ?>>
						<?php esc_html_e( 'Cover (fill screen, may crop)', 'unstoppable-white-label' ); ?>
					</option>
					<option value="contain" <?php selected( $uswl_login['background_size'] ?? 'cover', 'contain' ); ?>>
						<?php esc_html_e( 'Contain (fit entire image)', 'unstoppable-white-label' ); ?>
					</option>
					<option value="auto" <?php selected( $uswl_login['background_size'] ?? 'cover', 'auto' ); ?>>
						<?php esc_html_e( 'Auto (original size)', 'unstoppable-white-label' ); ?>
					</option>
					<option value="100% 100%" <?php selected( $uswl_login['background_size'] ?? 'cover', '100% 100%' ); ?>>
						<?php esc_html_e( 'Stretch (fill screen, may distort)', 'unstoppable-white-label' ); ?>
					</option>
				</select>
			</td>
		</tr>

		<tr id="uswl-bg-position-row" style="<?php echo empty( $uswl_login['background_image_url'] ) ? 'display:none;' : ''; ?>">
			<th scope="row">
				<label for="uswl-login-bg-position"><?php esc_html_e( 'Background Position', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<select id="uswl-login-bg-position" name="login[background_position]">
					<option value="center center" <?php selected( $uswl_login['background_position'] ?? 'center center', 'center center' ); ?>>
						<?php esc_html_e( 'Center', 'unstoppable-white-label' ); ?>
					</option>
					<option value="top center" <?php selected( $uswl_login['background_position'] ?? 'center center', 'top center' ); ?>>
						<?php esc_html_e( 'Top', 'unstoppable-white-label' ); ?>
					</option>
					<option value="bottom center" <?php selected( $uswl_login['background_position'] ?? 'center center', 'bottom center' ); ?>>
						<?php esc_html_e( 'Bottom', 'unstoppable-white-label' ); ?>
					</option>
					<option value="left center" <?php selected( $uswl_login['background_position'] ?? 'center center', 'left center' ); ?>>
						<?php esc_html_e( 'Left', 'unstoppable-white-label' ); ?>
					</option>
					<option value="right center" <?php selected( $uswl_login['background_position'] ?? 'center center', 'right center' ); ?>>
						<?php esc_html_e( 'Right', 'unstoppable-white-label' ); ?>
					</option>
				</select>
			</td>
		</tr>

		<tr id="uswl-bg-repeat-row" style="<?php echo empty( $uswl_login['background_image_url'] ) ? 'display:none;' : ''; ?>">
			<th scope="row">
				<label for="uswl-login-bg-repeat"><?php esc_html_e( 'Background Repeat', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<select id="uswl-login-bg-repeat" name="login[background_repeat]">
					<option value="no-repeat" <?php selected( $uswl_login['background_repeat'] ?? 'no-repeat', 'no-repeat' ); ?>>
						<?php esc_html_e( 'No Repeat', 'unstoppable-white-label' ); ?>
					</option>
					<option value="repeat" <?php selected( $uswl_login['background_repeat'] ?? 'no-repeat', 'repeat' ); ?>>
						<?php esc_html_e( 'Repeat (tile)', 'unstoppable-white-label' ); ?>
					</option>
					<option value="repeat-x" <?php selected( $uswl_login['background_repeat'] ?? 'no-repeat', 'repeat-x' ); ?>>
						<?php esc_html_e( 'Repeat Horizontally', 'unstoppable-white-label' ); ?>
					</option>
					<option value="repeat-y" <?php selected( $uswl_login['background_repeat'] ?? 'no-repeat', 'repeat-y' ); ?>>
						<?php esc_html_e( 'Repeat Vertically', 'unstoppable-white-label' ); ?>
					</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<!-- Hide Elements -->
<h3><?php esc_html_e( 'Hide Elements', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Hide on Login Page', 'unstoppable-white-label' ); ?></th>
			<td>
				<fieldset>
					<label style="display: block; margin-bottom: 8px;">
						<input type="checkbox" 
							   name="login[hide_remember_me]" 
							   value="1"
							   <?php checked( ! empty( $uswl_login['hide_remember_me'] ) ); ?>>
						<?php esc_html_e( 'Hide "Remember Me" checkbox', 'unstoppable-white-label' ); ?>
					</label>
					<label style="display: block; margin-bottom: 8px;">
						<input type="checkbox" 
							   name="login[hide_lost_password]" 
							   value="1"
							   <?php checked( ! empty( $uswl_login['hide_lost_password'] ) ); ?>>
						<?php esc_html_e( 'Hide "Lost your password?" link', 'unstoppable-white-label' ); ?>
					</label>
					<label style="display: block; margin-bottom: 8px;">
						<input type="checkbox" 
							   name="login[hide_back_to_site]" 
							   value="1"
							   <?php checked( ! empty( $uswl_login['hide_back_to_site'] ) ); ?>>
						<?php esc_html_e( 'Hide "← Go to Site" link', 'unstoppable-white-label' ); ?>
					</label>
					<label style="display: block; margin-bottom: 8px;">
						<input type="checkbox" 
							   name="login[hide_language_switcher]" 
							   value="1"
							   <?php checked( ! empty( $uswl_login['hide_language_switcher'] ) ); ?>>
						<?php esc_html_e( 'Hide Language Switcher', 'unstoppable-white-label' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>

<!-- Color Scheme -->
<h3><?php esc_html_e( 'Color Scheme', 'unstoppable-white-label' ); ?></h3>
<p class="description" style="margin-bottom: 15px;"><?php esc_html_e( 'Customize the login page colors to match your brand.', 'unstoppable-white-label' ); ?></p>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-login-bg-color"><?php esc_html_e( 'Page Background', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-login-bg-color" 
					   name="login[background_color]" 
					   value="<?php echo esc_attr( $uswl_login['background_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#f0f0f1">
				<p class="description"><?php esc_html_e( 'Used when no background image is set.', 'unstoppable-white-label' ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-form-bg-color"><?php esc_html_e( 'Form Background', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-form-bg-color" 
					   name="login[form_background_color]" 
					   value="<?php echo esc_attr( $uswl_login['form_background_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#ffffff">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-form-text-color"><?php esc_html_e( 'Form Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-form-text-color" 
					   name="login[form_text_color]" 
					   value="<?php echo esc_attr( $uswl_login['form_text_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#3c434a">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-link-color"><?php esc_html_e( 'Links', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-link-color" 
					   name="login[link_color]" 
					   value="<?php echo esc_attr( $uswl_login['link_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-button-bg-color"><?php esc_html_e( 'Button Background', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-button-bg-color" 
					   name="login[button_background_color]" 
					   value="<?php echo esc_attr( $uswl_login['button_background_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#2271b1">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-button-text-color"><?php esc_html_e( 'Button Text', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-button-text-color" 
					   name="login[button_text_color]" 
					   value="<?php echo esc_attr( $uswl_login['button_text_color'] ?? '' ); ?>" 
					   class="uswl-color-picker" 
					   data-default-color="#ffffff">
			</td>
		</tr>
	</tbody>
</table>

<!-- Preview -->
<h3><?php esc_html_e( 'Preview', 'unstoppable-white-label' ); ?></h3>
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Test Your Changes', 'unstoppable-white-label' ); ?></th>
			<td>
				<a href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" class="button">
					<?php esc_html_e( 'Preview Login Page', 'unstoppable-white-label' ); ?> →
				</a>
				<p class="description">
					<?php esc_html_e( 'Opens in a new tab. Save your settings first to see changes.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
