<?php
/**
 * Admin Support Page.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Core\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SupportPage
 */
class SupportPage {

	/**
	 * Render support information page.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}
		?>
		<div class="wrap wpcal-admin-wrap">
			<?php MenuManager::render_header_tabs( 'support' ); ?>

			<div class="wpcal-section-header">
				<div>
					<h2><?php esc_html_e( 'Help & Support', 'wpcalibrate-timeline-builder' ); ?></h2>
					<p class="wpcal-section-desc"><?php esc_html_e( 'Reach out to the WPCalibrate engineering team for assistance, feature suggestions, or custom development.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
			</div>

			<div class="wpcal-support-grid">
				<!-- Email Card -->
				<div class="wpcal-support-card">
					<div class="wpcal-support-icon">✉️</div>
					<h3><?php esc_html_e( 'Email Support', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Have questions, bug reports, or need technical help? Write directly to our support desk.', 'wpcalibrate-timeline-builder' ); ?></p>
					<a href="mailto:support@wpcalibrate.com" class="button button-primary">
						support@wpcalibrate.com
					</a>
				</div>

				<!-- WhatsApp / Phone Card -->
				<div class="wpcal-support-card">
					<div class="wpcal-support-icon">💬</div>
					<h3><?php esc_html_e( 'Direct / WhatsApp', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Get quick answers via phone or direct WhatsApp messaging with our team.', 'wpcalibrate-timeline-builder' ); ?></p>
					<a href="https://wa.me/447474795976" target="_blank" rel="noopener noreferrer" class="button">
						+447474795976
					</a>
				</div>

				<!-- Official Website Card -->
				<div class="wpcal-support-card">
					<div class="wpcal-support-icon">🌐</div>
					<h3><?php esc_html_e( 'Official Website', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Explore our company profile, services, documentation, and product releases.', 'wpcalibrate-timeline-builder' ); ?></p>
					<a href="https://wpcalibrate.com" target="_blank" rel="noopener noreferrer" class="button">
						wpcalibrate.com &rarr;
					</a>
				</div>

				<!-- Marketplace Card -->
				<div class="wpcal-support-card">
					<div class="wpcal-support-icon">🛍️</div>
					<h3><?php esc_html_e( 'WPCalibrate Marketplace', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Discover high-performance WordPress plugins, extensions, and integration tools.', 'wpcalibrate-timeline-builder' ); ?></p>
					<a href="https://marketplace.wpcalibrate.com/" target="_blank" rel="noopener noreferrer" class="button">
						marketplace.wpcalibrate.com &rarr;
					</a>
				</div>
			</div>

			<!-- Quick Usage Documentation -->
			<div class="wpcal-panel-card" style="margin-top: 24px;">
				<h3 class="wpcal-card-heading"><?php esc_html_e( 'Quick Integration Guide', 'wpcalibrate-timeline-builder' ); ?></h3>
				<div class="wpcal-guide-steps">
					<div class="wpcal-step">
						<span class="wpcal-step-num">1</span>
						<h4><?php esc_html_e( 'Create a Timeline', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Go to Timelines > Add New Timeline, name your timeline, and configure visual styles.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
					<div class="wpcal-step">
						<span class="wpcal-step-num">2</span>
						<h4><?php esc_html_e( 'Add Milestone Items', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Add milestone events with dates, images, icons, and descriptions. Reorder using drag-and-drop or accessible arrow buttons.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
					<div class="wpcal-step">
						<span class="wpcal-step-num">3</span>
						<h4><?php esc_html_e( 'Display Anywhere', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Use the Gutenberg block "WPCalibrate Timeline", the shortcode [wpcalibrate_timeline id="123"], or the Elementor widget.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
