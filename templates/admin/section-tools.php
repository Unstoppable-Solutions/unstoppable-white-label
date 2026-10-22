<?php
/**
 * Tools Section Template
 *
 * @package Unstoppable_White_Label
 *
 * @var array $settings Current settings.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Export Settings', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<button type="button" class="button" id="uswl-export-settings">
					<?php esc_html_e( 'Export Settings', 'unstoppable-white-label' ); ?>
				</button>
				<p class="description">
					<?php esc_html_e( 'Download your current settings as a JSON file.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-import-file"><?php esc_html_e( 'Import Settings', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="file" 
					   id="uswl-import-file" 
					   accept=".json,application/json"
					   class="uswl-file-input">
				<button type="button" class="button" id="uswl-import-settings" disabled>
					<?php esc_html_e( 'Import Settings', 'unstoppable-white-label' ); ?>
				</button>
				<p class="description">
					<?php esc_html_e( 'Import settings from a previously exported JSON file.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Reset Settings', 'unstoppable-white-label' ); ?>
			</th>
			<td>
				<button type="button" class="button button-link-delete" id="uswl-reset-settings">
					<?php esc_html_e( 'Reset All Settings', 'unstoppable-white-label' ); ?>
				</button>
				<p class="description uswl-warning">
					<?php esc_html_e( 'This will reset all settings to their default values. This cannot be undone.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<div class="uswl-support-info">
	<h3><?php esc_html_e( 'Need Help?', 'unstoppable-white-label' ); ?></h3>
	<p>
		<?php esc_html_e( 'Check out our documentation or reach out to support:', 'unstoppable-white-label' ); ?>
	</p>
	<ul>
		<li>
			<a href="https://unstoppablesolutions.app/docs/unstoppable-white-label/" target="_blank">
				<?php esc_html_e( 'Documentation', 'unstoppable-white-label' ); ?> →
			</a>
		</li>
		<li>
			<a href="https://wordpress.org/support/plugin/unstoppable-white-label/" target="_blank">
				<?php esc_html_e( 'Support Forum', 'unstoppable-white-label' ); ?> →
			</a>
		</li>
	</ul>
</div>
