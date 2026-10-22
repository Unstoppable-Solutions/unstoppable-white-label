<?php
/**
 * Visibility Section Template
 *
 * @package Unstoppable_White_Label
 *
 * @var array $settings Current settings.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Unstoppable\WhiteLabel\Plugin;

$uswl_visibility = Plugin::instance()->loader()->get_module( 'visibility_manager' );

$uswl_menus    = $uswl_visibility ? $uswl_visibility->menus()->get_available() : array();
$uswl_adminbar = $uswl_visibility ? $uswl_visibility->admin_bar()->get_available() : array();
$plugins  = $uswl_visibility ? $uswl_visibility->plugins()->get_available() : array();

// Get themes
$uswl_theme_visibility = Plugin::instance()->loader()->get_module( 'theme_visibility' );
$uswl_themes = $uswl_theme_visibility ? $uswl_theme_visibility->get_available() : array();

// Get saved values
$uswl_hidden_menus    = $settings['visibility']['menus'] ?? array();
$uswl_hidden_adminbar = $settings['visibility']['adminbar'] ?? array();
$uswl_hidden_plugins  = $settings['visibility']['plugins'] ?? array();
$uswl_hidden_themes   = $settings['visibility']['themes'] ?? array();
$uswl_hide_notices    = ! empty( $settings['visibility']['hide_notices'] );
$uswl_renamed_menus   = $settings['menus']['renamed'] ?? array();
?>

<!-- Sidebar Menus -->
<h3 style="margin-top: 0;"><?php esc_html_e( 'Sidebar Menus', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Hide or rename sidebar menu items. These settings are ignored by White Label Administrators.', 'unstoppable-white-label' ); ?></p>

<table class="widefat uswl-menus-table" style="margin-top: 15px;">
	<thead>
		<tr>
			<th style="width: 50px;"><?php esc_html_e( 'Hide', 'unstoppable-white-label' ); ?></th>
			<th><?php esc_html_e( 'Sidebar Menu', 'unstoppable-white-label' ); ?></th>
			<th style="width: 250px;"><?php esc_html_e( 'Rename To', 'unstoppable-white-label' ); ?></th>
		</tr>
	</thead>
	<tbody id="uswl-menus-list">
		<?php if ( empty( $uswl_menus ) ) : ?>
			<tr>
				<td colspan="3">
					<p class="description"><?php esc_html_e( 'No menus found. Visit other admin pages first to populate this list.', 'unstoppable-white-label' ); ?></p>
				</td>
			</tr>
		<?php else : ?>
			<?php foreach ( $uswl_menus as $uswl_slug => $uswl_label ) : 
				$uswl_is_hidden = in_array( $uswl_slug, $uswl_hidden_menus, true );
				$uswl_rename_value = $uswl_renamed_menus[ $uswl_slug ] ?? '';
			?>
				<tr>
					<td style="text-align: center;">
						<input type="checkbox" 
							   name="visibility[menus][]" 
							   value="<?php echo esc_attr( $uswl_slug ); ?>"
							   <?php checked( $uswl_is_hidden ); ?>>
					</td>
					<td>
						<strong><?php echo esc_html( $uswl_label ); ?></strong>
						<code style="font-size: 11px; color: #666; margin-left: 5px;"><?php echo esc_html( $uswl_slug ); ?></code>
					</td>
					<td>
						<input type="text" 
							   name="menus[renamed][<?php echo esc_attr( $uswl_slug ); ?>]" 
							   value="<?php echo esc_attr( $uswl_rename_value ); ?>" 
							   class="regular-text"
							   placeholder="<?php echo esc_attr( $uswl_label ); ?>"
							   style="width: 100%;">
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php if ( ! empty( $uswl_menus ) ) : ?>
<p style="margin-top: 10px;">
	<button type="button" class="button button-small uswl-check-all" data-target="menus">
		<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
	</button>
	<button type="button" class="button button-small uswl-uncheck-all" data-target="menus">
		<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
	</button>
</p>
<?php endif; ?>

<!-- Admin Bar Items -->
<h3><?php esc_html_e( 'Admin Bar Items', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Hide items from the admin bar. These settings are ignored by White Label Administrators.', 'unstoppable-white-label' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Hide Items', 'unstoppable-white-label' ); ?></th>
			<td>
				<?php if ( ! empty( $uswl_adminbar ) ) : ?>
				<div class="uswl-checkbox-controls" style="margin-bottom: 8px;">
					<button type="button" class="button button-small uswl-check-all" data-target="adminbar">
						<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button button-small uswl-uncheck-all" data-target="adminbar">
						<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<?php endif; ?>
				
				<div class="uswl-checkbox-list" id="uswl-adminbar-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
					<?php if ( empty( $uswl_adminbar ) ) : ?>
						<p class="description"><?php esc_html_e( 'No admin bar items found. This list populates after visiting other admin pages.', 'unstoppable-white-label' ); ?></p>
					<?php else : ?>
						<?php foreach ( $uswl_adminbar as $id => $uswl_label ) : ?>
							<label style="display: block; margin-bottom: 8px;" class="uswl-checkbox-label">
								<input type="checkbox" 
									   name="visibility[adminbar][]" 
									   value="<?php echo esc_attr( $id ); ?>"
									   <?php checked( in_array( $id, $uswl_hidden_adminbar, true ) ); ?>>
								<?php echo esc_html( $uswl_label ); ?>
							</label>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!-- Plugins -->
<h3><?php esc_html_e( 'Plugins List', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Hide plugins from the plugins list. They remain active but invisible to non-WL admin users.', 'unstoppable-white-label' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Hide Plugins', 'unstoppable-white-label' ); ?></th>
			<td>
				<?php if ( ! empty( $plugins ) ) : ?>
				<div class="uswl-checkbox-controls" style="margin-bottom: 8px;">
					<button type="button" class="button button-small uswl-check-all" data-target="plugins">
						<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button button-small uswl-uncheck-all" data-target="plugins">
						<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<?php endif; ?>
				
				<div class="uswl-checkbox-list" id="uswl-plugins-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
					<?php if ( empty( $plugins ) ) : ?>
						<p class="description"><?php esc_html_e( 'No plugins found.', 'unstoppable-white-label' ); ?></p>
					<?php else : ?>
						<?php foreach ( $plugins as $uswl_basename => $uswl_name ) : ?>
							<label style="display: block; margin-bottom: 8px;" class="uswl-checkbox-label">
								<input type="checkbox" 
									   name="visibility[plugins][]" 
									   value="<?php echo esc_attr( $uswl_basename ); ?>"
									   <?php checked( in_array( $uswl_basename, $uswl_hidden_plugins, true ) ); ?>>
								<?php echo esc_html( $uswl_name ); ?>
							</label>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!-- Themes -->
<h3><?php esc_html_e( 'Themes List', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Hide themes from the themes list. The active theme cannot be hidden.', 'unstoppable-white-label' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Hide Themes', 'unstoppable-white-label' ); ?></th>
			<td>
				<?php if ( ! empty( $uswl_themes ) ) : ?>
				<div class="uswl-checkbox-controls" style="margin-bottom: 8px;">
					<button type="button" class="button button-small uswl-check-all" data-target="themes">
						<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
					</button>
					<button type="button" class="button button-small uswl-uncheck-all" data-target="themes">
						<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
					</button>
				</div>
				<?php endif; ?>
				
				<div class="uswl-checkbox-list" id="uswl-themes-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
					<?php if ( empty( $uswl_themes ) ) : ?>
						<p class="description"><?php esc_html_e( 'No themes found.', 'unstoppable-white-label' ); ?></p>
					<?php else : ?>
						<?php 
						$uswl_active_theme = get_stylesheet();
						foreach ( $uswl_themes as $uswl_stylesheet => $uswl_name ) : 
							$uswl_is_active = ( $uswl_stylesheet === $uswl_active_theme );
						?>
							<label style="display: block; margin-bottom: 8px; <?php echo $uswl_is_active ? 'opacity: 0.5;' : ''; ?>" class="uswl-checkbox-label">
								<input type="checkbox" 
									   name="visibility[themes][]" 
									   value="<?php echo esc_attr( $uswl_stylesheet ); ?>"
									   <?php checked( in_array( $uswl_stylesheet, $uswl_hidden_themes, true ) ); ?>
									   <?php disabled( $uswl_is_active ); ?>>
								<?php echo esc_html( $uswl_name ); ?>
								<?php if ( $uswl_is_active ) : ?>
									<span style="color: #666; font-style: italic;"><?php esc_html_e( '(Active)', 'unstoppable-white-label' ); ?></span>
								<?php endif; ?>
							</label>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!-- Admin Notifications -->
<h3><?php esc_html_e( 'Admin Notifications', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Control the visibility of admin notices and notifications.', 'unstoppable-white-label' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Hide All Notices', 'unstoppable-white-label' ); ?></th>
			<td>
				<label for="uswl-hide-notices">
					<input type="checkbox" 
						   id="uswl-hide-notices" 
						   name="visibility[hide_notices]" 
						   value="1" 
						   <?php checked( $uswl_hide_notices ); ?>>
					<?php esc_html_e( 'Remove all admin notices and notifications for non-White Label Administrators', 'unstoppable-white-label' ); ?>
				</label>
				<p class="description" style="margin-top: 5px;">
					<?php esc_html_e( 'This will hide update notices, plugin notices, and all other admin notifications. White Label Administrators will still see all notices.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<!-- Feature Suggestions -->
<div class="uswl-feature-suggestions" style="margin-top: 30px; padding: 20px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px;">
	<h4 style="margin: 0 0 10px 0; color: #1d2327;">
		<span class="dashicons dashicons-lightbulb" style="color: #f0b849;"></span>
		<?php esc_html_e( 'Have a Feature Suggestion?', 'unstoppable-white-label' ); ?>
	</h4>
	<p style="margin: 0 0 15px 0; color: #50575e;">
		<?php esc_html_e( 'We\'d love to hear your ideas for improving Unstoppable White Label! Send us your feature requests and suggestions.', 'unstoppable-white-label' ); ?>
	</p>
	<a href="mailto:support@unstoppablesolutions.app?subject=Feature%20Suggestion%20-%20Unstoppable%20White%20Label" 
	   class="button button-secondary" 
	   style="display: inline-flex; align-items: center; gap: 5px;">
		<span class="dashicons dashicons-email" style="margin-top: 3px;"></span>
		<?php esc_html_e( 'Email Feature Suggestion', 'unstoppable-white-label' ); ?>
	</a>
	<span style="margin-left: 10px; color: #646970;">support@unstoppablesolutions.app</span>
</div>
