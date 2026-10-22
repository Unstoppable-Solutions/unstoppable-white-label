<?php
/**
 * Dashboard Section Template
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

// Get saved values
$uswl_dashboard        = $settings['dashboard'] ?? array();
$uswl_hidden_widgets   = $uswl_dashboard['hidden_widgets'] ?? array();
$uswl_widget_enabled   = ! empty( $uswl_dashboard['custom_widget_enabled'] );
$uswl_widget_title     = $uswl_dashboard['custom_widget_title'] ?? '';
$uswl_widget_content   = $uswl_dashboard['custom_widget_content'] ?? '';

// Get available dashboard widgets from transient or use defaults
$uswl_available_widgets = get_transient( 'uswl_dashboard_widgets' );
if ( ! $uswl_available_widgets ) {
	// Default WordPress widgets
	$uswl_available_widgets = array(
		'dashboard_primary'          => __( 'WordPress Events and News', 'unstoppable-white-label' ),
		'dashboard_quick_press'      => __( 'Quick Draft Your Recent Drafts', 'unstoppable-white-label' ),
		'dashboard_right_now'        => __( 'At a Glance', 'unstoppable-white-label' ),
		'dashboard_activity'         => __( 'Activity', 'unstoppable-white-label' ),
		'dashboard_site_health'      => __( 'Site Health Status', 'unstoppable-white-label' ),
	);
}
?>

<!-- Remove Dashboard Widgets -->
<h3 style="margin-top: 0;"><?php esc_html_e( 'Remove Dashboard Widgets', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Remove widgets from the admin dashboard. These settings are ignored by White Label Administrators.', 'unstoppable-white-label' ); ?></p>

<table class="widefat uswl-widgets-table" style="margin-top: 15px; max-width: 600px;">
	<thead>
		<tr>
			<th style="width: 60px;"><?php esc_html_e( 'Remove', 'unstoppable-white-label' ); ?></th>
			<th><?php esc_html_e( 'Dashboard Widget', 'unstoppable-white-label' ); ?></th>
		</tr>
	</thead>
	<tbody id="uswl-widgets-list">
		<tr class="uswl-widget-group-header">
			<td colspan="2"><strong><?php esc_html_e( 'Default Dashboard Widgets', 'unstoppable-white-label' ); ?></strong></td>
		</tr>
		<?php 
		$uswl_core_widgets = array(
			'dashboard_right_now'     => __( 'At a Glance', 'unstoppable-white-label' ),
			'dashboard_activity'      => __( 'Activity', 'unstoppable-white-label' ),
			'dashboard_quick_press'   => __( 'Quick Draft Your Recent Drafts', 'unstoppable-white-label' ),
			'dashboard_primary'       => __( 'WordPress Events and News', 'unstoppable-white-label' ),
			'dashboard_site_health'   => __( 'Site Health Status', 'unstoppable-white-label' ),
		);
		foreach ( $uswl_core_widgets as $uswl_widget_id => $uswl_widget_name ) : ?>
			<tr>
				<td style="text-align: center;">
					<input type="checkbox" 
						   name="dashboard[hidden_widgets][]" 
						   value="<?php echo esc_attr( $uswl_widget_id ); ?>"
						   <?php checked( in_array( $uswl_widget_id, $uswl_hidden_widgets, true ) ); ?>>
				</td>
				<td><?php echo esc_html( $uswl_widget_name ); ?></td>
			</tr>
		<?php endforeach; ?>
		
		<tr class="uswl-widget-group-header">
			<td colspan="2"><strong><?php esc_html_e( 'Third-Party Dashboard Widgets from Plugins and Themes', 'unstoppable-white-label' ); ?></strong></td>
		</tr>
		<?php 
		// Common third-party widgets
		$uswl_third_party_widgets = array(
			'woocommerce_dashboard_status'    => __( 'WooCommerce Status', 'unstoppable-white-label' ),
			'woocommerce_dashboard_recent_reviews' => __( 'WooCommerce Recent Reviews', 'unstoppable-white-label' ),
			'jetpack_summary_widget'          => __( 'Jetpack Stats', 'unstoppable-white-label' ),
			'elementor_overview'              => __( 'Elementor Overview', 'unstoppable-white-label' ),
			'wordfence_activity_report'       => __( 'Wordfence activity in the past week', 'unstoppable-white-label' ),
			'google_sitekit_widget'           => __( 'Site Kit Summary – last 28 days', 'unstoppable-white-label' ),
			'yoast_db_widget'                 => __( 'Yoast SEO', 'unstoppable-white-label' ),
			'rank_math_dashboard_widget'      => __( 'Rank Math Overview', 'unstoppable-white-label' ),
			'wpforms_reports_widget'          => __( 'WPForms', 'unstoppable-white-label' ),
			'monsterinsights_reports_widget'  => __( 'MonsterInsights Reports', 'unstoppable-white-label' ),
			'fluent_smtp_widget'              => __( 'Fluent SMTP', 'unstoppable-white-label' ),
			'updraftplus_widget'              => __( 'UpdraftPlus', 'unstoppable-white-label' ),
			'ai_capabilities_widget'          => __( 'AI Capabilities', 'unstoppable-white-label' ),
			'ai_status_widget'                => __( 'AI Status', 'unstoppable-white-label' ),
		);
		foreach ( $uswl_third_party_widgets as $uswl_widget_id => $uswl_widget_name ) : ?>
			<tr>
				<td style="text-align: center;">
					<input type="checkbox" 
						   name="dashboard[hidden_widgets][]" 
						   value="<?php echo esc_attr( $uswl_widget_id ); ?>"
						   <?php checked( in_array( $uswl_widget_id, $uswl_hidden_widgets, true ) ); ?>>
				</td>
				<td><?php echo esc_html( $uswl_widget_name ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<p style="margin-top: 10px;">
	<button type="button" class="button button-small uswl-check-all" data-target="widgets">
		<?php esc_html_e( 'Check All', 'unstoppable-white-label' ); ?>
	</button>
	<button type="button" class="button button-small uswl-uncheck-all" data-target="widgets">
		<?php esc_html_e( 'Uncheck All', 'unstoppable-white-label' ); ?>
	</button>
</p>

<!-- Custom Dashboard Widget -->
<h3 style="margin-top: 30px;"><?php esc_html_e( 'Custom Widget', 'unstoppable-white-label' ); ?></h3>
<p class="description"><?php esc_html_e( 'Add a custom widget to the admin dashboard. Provide users with quick links or information.', 'unstoppable-white-label' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable Custom Widget', 'unstoppable-white-label' ); ?></th>
			<td>
				<label for="uswl-custom-widget-enabled">
					<input type="checkbox" 
						   id="uswl-custom-widget-enabled" 
						   name="dashboard[custom_widget_enabled]" 
						   value="1" 
						   <?php checked( $uswl_widget_enabled ); ?>>
					<?php esc_html_e( 'The custom widget will show up for all users in the admin dashboard.', 'unstoppable-white-label' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="uswl-custom-widget-title"><?php esc_html_e( 'Custom Widget Title', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   id="uswl-custom-widget-title" 
					   name="dashboard[custom_widget_title]" 
					   value="<?php echo esc_attr( $uswl_widget_title ); ?>" 
					   class="large-text"
					   placeholder="<?php esc_attr_e( 'Welcome to Your Dashboard', 'unstoppable-white-label' ); ?>">
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label><?php esc_html_e( 'Custom Widget Content', 'unstoppable-white-label' ); ?></label>
			</th>
			<td>
				<?php
				wp_editor(
					$uswl_widget_content,
					'uswl_custom_widget_content',
					array(
						'textarea_name' => 'dashboard[custom_widget_content]',
						'textarea_rows' => 10,
						'media_buttons' => true,
						'teeny'         => false,
						'quicktags'     => true,
						'tinymce'       => array(
							'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
							'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
						),
					)
				);
				?>
				<p class="description" style="margin-top: 10px;">
					<?php esc_html_e( 'Use the editor to create your custom dashboard widget content. You can add text, images, links, and more.', 'unstoppable-white-label' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
