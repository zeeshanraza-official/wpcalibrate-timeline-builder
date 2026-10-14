<?php
/**
 * Admin License Management Page.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Plugin;
use WPCalibrate\TimelineBuilder\Core\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicensePage
 */
class LicensePage {

	/**
	 * Render license settings screen.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}

		$license_manager = Plugin::get_instance()->get_license_manager();
		$message         = '';
		$notice_type     = '';

		// Handle saving or actions.
		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['wpcal_license_nonce'] ) ) {
			check_admin_referer( 'wpcal_manage_license', 'wpcal_license_nonce' );

			$action = sanitize_key( $_POST['license_action'] ?? 'save' );

			if ( 'save' === $action && isset( $_POST['license_key'] ) ) {
				$key = sanitize_text_field( wp_unslash( $_POST['license_key'] ) );
				$license_manager->save_key( $key );
				$message     = __( 'License key saved securely.', 'wpcalibrate-timeline-builder' );
				$notice_type = 'success';
			} elseif ( 'activate' === $action ) {
				$res         = $license_manager->activate();
				$message     = $res['message'];
				$notice_type = $res['success'] ? 'success' : 'error';
			} elseif ( 'deactivate' === $action ) {
				$res         = $license_manager->deactivate();
				$message     = $res['message'];
				$notice_type = $res['success'] ? 'success' : 'error';
			}
		}

		$is_configured = $license_manager->is_configured();
		$masked_key    = $license_manager->get_masked_key();
		$status        = $license_manager->get_status();
		$expiry        = $license_manager->get_expiry_date();
		$renewal_url   = $license_manager->get_renewal_url();
		?>
		<div class="wrap wpcal-admin-wrap">
			<?php MenuManager::render_header_tabs( 'license' ); ?>

			<div class="wpcal-section-header">
				<div>
					<h2><?php esc_html_e( 'License Management', 'wpcalibrate-timeline-builder' ); ?></h2>
					<p class="wpcal-section-desc"><?php esc_html_e( 'Manage your WPCalibrate Timeline Builder license and updates.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
			</div>

			<?php if ( ! empty( $message ) ) : ?>
				<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible">
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php endif; ?>

			<!-- Notice indicating licensing provider endpoint status -->
			<div class="notice notice-info wpcal-admin-notice">
				<p>
					<strong><?php esc_html_e( 'Licensing Service Status:', 'wpcalibrate-timeline-builder' ); ?></strong>
					<?php esc_html_e( 'The remote licensing provider endpoint has not been configured in this build. Full functionality remains active without artificial restrictions.', 'wpcalibrate-timeline-builder' ); ?>
				</p>
			</div>

			<div class="wpcal-panel-card" style="max-width: 700px;">
				<h3 class="wpcal-card-heading"><?php esc_html_e( 'License Configuration', 'wpcalibrate-timeline-builder' ); ?></h3>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Current Status', 'wpcalibrate-timeline-builder' ); ?></th>
						<td>
							<span class="wpcal-badge wpcal-badge-secondary">
								<?php esc_html_e( 'Not Configured (Standalone Mode)', 'wpcalibrate-timeline-builder' ); ?>
							</span>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'License Key', 'wpcalibrate-timeline-builder' ); ?></th>
						<td>
							<form method="post" action="" style="display: flex; gap: 8px; align-items: center; max-width: 450px;">
								<?php wp_nonce_field( 'wpcal_manage_license', 'wpcal_license_nonce' ); ?>
								<input type="hidden" name="license_action" value="save" />
								<input type="password" name="license_key"
									value="<?php echo esc_attr( $masked_key ); ?>"
									placeholder="<?php esc_attr_e( 'Enter your license key...', 'wpcalibrate-timeline-builder' ); ?>"
									class="regular-text" autocomplete="off" />
								<button type="submit" class="button">
									<?php esc_html_e( 'Save Key', 'wpcalibrate-timeline-builder' ); ?>
								</button>
							</form>
							<p class="description"><?php esc_html_e( 'Your key is masked and stored securely. Stored keys are never displayed in full.', 'wpcalibrate-timeline-builder' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Remote Activation', 'wpcalibrate-timeline-builder' ); ?></th>
						<td>
							<button type="button" class="button button-primary" disabled="disabled">
								<?php esc_html_e( 'Activate License', 'wpcalibrate-timeline-builder' ); ?>
							</button>
							<button type="button" class="button" disabled="disabled">
								<?php esc_html_e( 'Deactivate License', 'wpcalibrate-timeline-builder' ); ?>
							</button>
							<p class="description"><?php esc_html_e( 'Remote activation actions are disabled until a remote licensing API provider is designated.', 'wpcalibrate-timeline-builder' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Expiration Date', 'wpcalibrate-timeline-builder' ); ?></th>
						<td>
							<code><?php echo esc_html( $expiry ); ?></code>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Renewal', 'wpcalibrate-timeline-builder' ); ?></th>
						<td>
							<a href="<?php echo esc_url( $renewal_url ); ?>" target="_blank" rel="noopener noreferrer" class="button">
								<?php esc_html_e( 'Visit WPCalibrate Marketplace', 'wpcalibrate-timeline-builder' ); ?> &rarr;
							</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php
	}
}
