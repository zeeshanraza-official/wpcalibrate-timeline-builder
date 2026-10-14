<?php
/**
 * Global Settings Page.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Core\Capabilities;
use WPCalibrate\TimelineBuilder\Utilities\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SettingsPage
 */
class SettingsPage {

	public const OPTION_NAME     = 'wpcal_timeline_settings';
	public const UNINSTALL_OPTION = 'wpcal_timeline_delete_data_on_uninstall';

	/**
	 * Render settings page.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}

		$updated = false;

		// Process settings form submission.
		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['wpcal_settings_nonce'] ) ) {
			check_admin_referer( 'wpcal_save_global_settings', 'wpcal_settings_nonce' );

			$new_settings = [
				'default_layout'       => in_array( $_POST['default_layout'] ?? '', [ 'vertical', 'horizontal' ], true ) ? sanitize_key( $_POST['default_layout'] ) : 'vertical',
				'default_preset'       => in_array( $_POST['default_preset'] ?? '', [ 'clean', 'cards', 'minimal', 'modern', 'compact' ], true ) ? sanitize_key( $_POST['default_preset'] ) : 'cards',
				'default_accent_color' => Sanitizer::sanitize_hex_color( $_POST['default_accent_color'] ?? '#2563eb', '#2563eb' ),
				'default_animation'    => ( 'yes' === ( $_POST['default_animation'] ?? '' ) ) ? 'yes' : 'no',
				'asset_loading'        => ( 'always' === ( $_POST['asset_loading'] ?? '' ) ) ? 'always' : 'conditional',
			];

			update_option( self::OPTION_NAME, $new_settings );

			// Uninstall data retention preference.
			$delete_on_uninstall = isset( $_POST['delete_data_on_uninstall'] ) && '1' === $_POST['delete_data_on_uninstall'] ? 'yes' : 'no';
			update_option( self::UNINSTALL_OPTION, $delete_on_uninstall );

			$updated = true;
		}

		$settings            = get_option( self::OPTION_NAME, [] );
		$delete_on_uninstall = get_option( self::UNINSTALL_OPTION, 'no' );

		$default_layout       = $settings['default_layout'] ?? 'vertical';
		$default_preset       = $settings['default_preset'] ?? 'cards';
		$default_accent_color = $settings['default_accent_color'] ?? '#2563eb';
		$default_animation    = $settings['default_animation'] ?? 'yes';
		$asset_loading        = $settings['asset_loading'] ?? 'conditional';
		?>
		<div class="wrap wpcal-admin-wrap">
			<?php MenuManager::render_header_tabs( 'settings' ); ?>

			<div class="wpcal-section-header">
				<div>
					<h2><?php esc_html_e( 'Global Settings', 'wpcalibrate-timeline-builder' ); ?></h2>
					<p class="wpcal-section-desc"><?php esc_html_e( 'Configure sitewide defaults, asset loading optimization, and data retention preferences.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
			</div>

			<?php if ( $updated ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings updated successfully.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" action="">
				<?php wp_nonce_field( 'wpcal_save_global_settings', 'wpcal_settings_nonce' ); ?>

				<div class="wpcal-settings-container">
					<div class="wpcal-panel-card">
						<h3 class="wpcal-card-heading"><?php esc_html_e( 'New Timeline Defaults', 'wpcalibrate-timeline-builder' ); ?></h3>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><label for="default_layout"><?php esc_html_e( 'Default Layout', 'wpcalibrate-timeline-builder' ); ?></label></th>
								<td>
									<select name="default_layout" id="default_layout">
										<option value="vertical" <?php selected( $default_layout, 'vertical' ); ?>><?php esc_html_e( 'Vertical', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="horizontal" <?php selected( $default_layout, 'horizontal' ); ?>><?php esc_html_e( 'Horizontal', 'wpcalibrate-timeline-builder' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Initial layout selected when creating a new timeline.', 'wpcalibrate-timeline-builder' ); ?></p>
								</td>
							</tr>

							<tr>
								<th scope="row"><label for="default_preset"><?php esc_html_e( 'Default Preset', 'wpcalibrate-timeline-builder' ); ?></label></th>
								<td>
									<select name="default_preset" id="default_preset">
										<option value="clean" <?php selected( $default_preset, 'clean' ); ?>><?php esc_html_e( 'Clean', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="cards" <?php selected( $default_preset, 'cards' ); ?>><?php esc_html_e( 'Cards', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="minimal" <?php selected( $default_preset, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="modern" <?php selected( $default_preset, 'modern' ); ?>><?php esc_html_e( 'Modern', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="compact" <?php selected( $default_preset, 'compact' ); ?>><?php esc_html_e( 'Compact', 'wpcalibrate-timeline-builder' ); ?></option>
									</select>
								</td>
							</tr>

							<tr>
								<th scope="row"><label for="default_accent_color"><?php esc_html_e( 'Default Accent Color', 'wpcalibrate-timeline-builder' ); ?></label></th>
								<td>
									<input type="text" name="default_accent_color" id="default_accent_color" value="<?php echo esc_attr( $default_accent_color ); ?>" class="wpcal-color-picker" />
								</td>
							</tr>

							<tr>
								<th scope="row"><?php esc_html_e( 'Scroll Reveal Animation', 'wpcalibrate-timeline-builder' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="default_animation" value="yes" <?php checked( 'yes', $default_animation ); ?> />
										<?php esc_html_e( 'Enable animation by default on new timelines', 'wpcalibrate-timeline-builder' ); ?>
									</label>
								</td>
							</tr>
						</table>
					</div>

					<div class="wpcal-panel-card">
						<h3 class="wpcal-card-heading"><?php esc_html_e( 'Performance & Assets', 'wpcalibrate-timeline-builder' ); ?></h3>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Asset Loading Behavior', 'wpcalibrate-timeline-builder' ); ?></th>
								<td>
									<fieldset>
										<label>
											<input type="radio" name="asset_loading" value="conditional" <?php checked( 'conditional', $asset_loading ); ?> />
											<strong><?php esc_html_e( 'Conditional (Recommended)', 'wpcalibrate-timeline-builder' ); ?></strong> &mdash; <?php esc_html_e( 'Load frontend CSS and JavaScript only when a timeline is actually rendered on the page.', 'wpcalibrate-timeline-builder' ); ?>
										</label>
										<br /><br />
										<label>
											<input type="radio" name="asset_loading" value="always" <?php checked( 'always', $asset_loading ); ?> />
											<strong><?php esc_html_e( 'Always Load', 'wpcalibrate-timeline-builder' ); ?></strong> &mdash; <?php esc_html_e( 'Load on every frontend page (only select if using third-party AJAX page loaders).', 'wpcalibrate-timeline-builder' ); ?>
										</label>
									</fieldset>
								</td>
							</tr>
						</table>
					</div>

					<div class="wpcal-panel-card wpcal-danger-card">
						<h3 class="wpcal-card-heading text-danger"><?php esc_html_e( 'Uninstall & Data Retention', 'wpcalibrate-timeline-builder' ); ?></h3>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Delete Plugin Data on Uninstall', 'wpcalibrate-timeline-builder' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="delete_data_on_uninstall" value="1" <?php checked( 'yes', $delete_on_uninstall ); ?> />
										<strong><?php esc_html_e( 'Delete all timelines, items, categories, and settings when this plugin is deleted from WordPress.', 'wpcalibrate-timeline-builder' ); ?></strong>
									</label>
									<p class="description"><?php esc_html_e( 'Default is OFF. When unchecked, your timelines and settings are completely preserved if the plugin is uninstalled or deactivated.', 'wpcalibrate-timeline-builder' ); ?></p>
								</td>
							</tr>
						</table>
					</div>

					<div class="wpcal-form-submit">
						<button type="submit" class="button button-primary button-hero">
							<?php esc_html_e( 'Save Settings', 'wpcalibrate-timeline-builder' ); ?>
						</button>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}
