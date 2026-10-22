<?php
/**
 * General Section Template
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

$uswl_admin_page = Plugin::instance()->loader()->get_module( 'admin_page' );
$uswl_admins     = $uswl_admin_page ? $uswl_admin_page->get_available_admins() : array();
$uswl_admin_ids  = $settings['general']['admin_ids'] ?? array();
?>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<label for="uswl-enabled"><?php esc_html_e( 'Enable White Labeling', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<label class="uswl-toggle">
					<input type="checkbox" 
						   id="uswl-enabled" 
						   name="general[enabled]" 
						   value="1" 
						   <?php checked( $settings['general']['enabled'] ?? false ); ?>
						   role="switch"
						   aria-checked="<?php echo ( $settings['general']['enabled'] ?? false ) ? 'true' : 'false'; ?>">
					<span class="uswl-toggle-slider"></span>
				</label>
				<p class="description">
					<?php esc_html_e( 'Turn on to apply white labeling to your site.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'White Label Administrators', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'White Label Administrators', 'unstoppable-white-label' ); ?></legend>
					<div class="uswl-checkbox-list" id="uswl-admins-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
						<?php if ( empty( $uswl_admins ) ) : ?>
							<p class="description"><?php esc_html_e( 'No administrators found.', 'unstoppable-white-label' ); ?></p>
						<?php else : ?>
							<?php foreach ( $uswl_admins as $uswl_admin ) : ?>
								<label style="display: block; margin-bottom: 8px;">
									<input type="checkbox" 
										   name="general[admin_ids][]" 
										   value="<?php echo esc_attr( $uswl_admin['id'] ); ?>"
										   <?php checked( in_array( $uswl_admin['id'], $uswl_admin_ids, true ) ); ?>>
									<?php echo esc_html( $uswl_admin['name'] ); ?> 
									<span style="color: #666;">(<?php echo esc_html( $uswl_admin['email'] ); ?>)</span>
								</label>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $uswl_admins ) ) : ?>
					<p style="margin-top: 10px;">
						<button type="button" class="button button-small uswl-check-all" data-target="admins">
							<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
						</button>
						<button type="button" class="button button-small uswl-uncheck-all" data-target="admins">
							<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
						</button>
					</p>
					<?php endif; ?>
				</fieldset>
				<p class="description" id="uswl-admin-ids-description">
					<?php esc_html_e( 'These users will see the real WordPress interface (not white-labeled). Leave all unchecked to apply white labeling to everyone.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Data Cleanup', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<label for="uswl-delete-on-uninstall">
					<input type="checkbox" 
						   id="uswl-delete-on-uninstall" 
						   name="general[delete_on_uninstall]" 
						   value="1" 
						   <?php checked( $settings['general']['delete_on_uninstall'] ?? false ); ?>>
					<?php esc_html_e( 'Delete all plugin data when uninstalling', 'unstoppable-white-label' ); ?>
				</label>
				<p class="description uswl-warning">
					<?php esc_html_e( 'Warning: This will permanently delete all settings when you uninstall the plugin.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
