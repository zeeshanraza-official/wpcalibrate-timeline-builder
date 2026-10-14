<?php
/**
 * Admin Overview Page.
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
 * Class OverviewPage
 */
class OverviewPage {

	/**
	 * Render the overview and documentation page.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}

		$create_url   = admin_url( 'admin.php?page=wpcal-timeline-builder&action=new' );
		$list_url     = admin_url( 'admin.php?page=wpcal-timeline-builder' );
		$settings_url = admin_url( 'admin.php?page=wpcal-timeline-builder-settings' );
		$support_url  = admin_url( 'admin.php?page=wpcal-timeline-builder-support' );
		?>
		<div class="wrap wpcal-admin-wrap">
			<?php MenuManager::render_header_tabs( 'overview' ); ?>

			<!-- Hero Banner -->
			<div class="wpcal-overview-hero">
				<div class="wpcal-overview-hero-content">
					<span class="wpcal-overview-hero-badge"><?php esc_html_e( 'Official Documentation & Guide', 'wpcalibrate-timeline-builder' ); ?></span>
					<h2><?php esc_html_e( 'Welcome to WPCalibrate Timeline Builder', 'wpcalibrate-timeline-builder' ); ?></h2>
					<p class="wpcal-overview-hero-desc">
						<?php esc_html_e( 'Create visually engaging, accessible, and responsive story timelines, company roadmaps, process milestones, and event schedules in seconds with zero bloat.', 'wpcalibrate-timeline-builder' ); ?>
					</p>
					<div class="wpcal-overview-hero-actions">
						<a href="<?php echo esc_url( $create_url ); ?>" class="button button-primary button-hero">
							+ <?php esc_html_e( 'Create Your First Timeline', 'wpcalibrate-timeline-builder' ); ?>
						</a>
						<a href="<?php echo esc_url( $list_url ); ?>" class="button button-secondary button-hero">
							<?php esc_html_e( 'View All Timelines', 'wpcalibrate-timeline-builder' ); ?>
						</a>
						<a href="<?php echo esc_url( $support_url ); ?>" class="button button-secondary button-hero">
							<?php esc_html_e( 'Get Support', 'wpcalibrate-timeline-builder' ); ?>
						</a>
					</div>
				</div>
			</div>

			<!-- Three Pillar Columns: Purpose, How To Use, Method -->
			<div class="wpcal-overview-section">
				<h3 class="wpcal-overview-section-title">
					<span class="wpcal-section-icon">🎯</span> <?php esc_html_e( 'Purpose & Key Use Cases', 'wpcalibrate-timeline-builder' ); ?>
				</h3>
				<p class="wpcal-overview-section-subtitle">
					<?php esc_html_e( 'WPCalibrate Timeline Builder is engineered to solve a common WordPress problem: bloated, slow, and non-responsive timeline plugins. It provides clean, semantic presentation across four core use cases.', 'wpcalibrate-timeline-builder' ); ?>
				</p>

				<div class="wpcal-cards-grid-4">
					<div class="wpcal-card wpcal-card-purpose">
						<div class="wpcal-card-icon">🏢</div>
						<h4><?php esc_html_e( 'Company History & Milestones', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Showcase your company journey from founding day to major acquisitions, product launches, global expansions, and achievements.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
					<div class="wpcal-card wpcal-card-purpose">
						<div class="wpcal-card-icon">🚀</div>
						<h4><?php esc_html_e( 'Product Roadmaps & Releases', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Communicate upcoming product releases, quarterly goals, version changes, and beta milestones to stakeholders and customers.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
					<div class="wpcal-card wpcal-card-purpose">
						<div class="wpcal-card-icon">📋</div>
						<h4><?php esc_html_e( 'Process & Onboarding Steps', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Guide website visitors through multi-step onboarding, recruitment pipelines, delivery phases, or project workflows.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
					<div class="wpcal-card wpcal-card-purpose">
						<div class="wpcal-card-icon">📅</div>
						<h4><?php esc_html_e( 'Events & Conference Agendas', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Publish event schedules, keynote programs, webinar lineups, and festival agendas with dates, tags, and detail links.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
				</div>
			</div>

			<!-- Step-by-Step: How to Use -->
			<div class="wpcal-overview-section">
				<h3 class="wpcal-overview-section-title">
					<span class="wpcal-section-icon">📖</span> <?php esc_html_e( 'How to Use (Step-by-Step)', 'wpcalibrate-timeline-builder' ); ?>
				</h3>
				<p class="wpcal-overview-section-subtitle">
					<?php esc_html_e( 'Follow these four simple steps to build and publish a responsive timeline on your WordPress website.', 'wpcalibrate-timeline-builder' ); ?>
				</p>

				<div class="wpcal-steps-container">
					<div class="wpcal-step-row">
						<div class="wpcal-step-number">1</div>
						<div class="wpcal-step-body">
							<h4><?php esc_html_e( 'Create a Timeline & Choose Your Layout', 'wpcalibrate-timeline-builder' ); ?></h4>
							<p><?php esc_html_e( 'Go to Timelines > Add New Timeline. Give your timeline an internal title and choose your layout:', 'wpcalibrate-timeline-builder' ); ?></p>
							<ul class="wpcal-step-list">
								<li><strong><?php esc_html_e( 'Vertical Layout:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Balanced alternating card layout with center-connected spine line. Automatically collapses on mobile screens for seamless responsiveness.', 'wpcalibrate-timeline-builder' ); ?></li>
								<li><strong><?php esc_html_e( 'Horizontal Layout:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Interactive horizontal slider with navigation controls, connected marker stems, and smooth touch/swipe navigation.', 'wpcalibrate-timeline-builder' ); ?></li>
							</ul>
						</div>
					</div>

					<div class="wpcal-step-row">
						<div class="wpcal-step-number">2</div>
						<div class="wpcal-step-body">
							<h4><?php esc_html_e( 'Add & Reorder Milestone Events', 'wpcalibrate-timeline-builder' ); ?></h4>
							<p><?php esc_html_e( 'Click "+ Add Milestone" to populate your timeline. Each milestone item supports rich metadata:', 'wpcalibrate-timeline-builder' ); ?></p>
							<ul class="wpcal-step-list">
								<li><strong><?php esc_html_e( 'Title & Date / Year:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Clear milestone heading and associated timestamp or custom date label.', 'wpcalibrate-timeline-builder' ); ?></li>
								<li><strong><?php esc_html_e( 'Category / Version Badge:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Optional pill tag (e.g., "V2", "Sprint 4", "Q3 Milestone").', 'wpcalibrate-timeline-builder' ); ?></li>
								<li><strong><?php esc_html_e( 'Media Image or Icon:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Upload photos or choose custom Dashicons/SVG markers.', 'wpcalibrate-timeline-builder' ); ?></li>
								<li><strong><?php esc_html_e( 'Rich Description & Read More Link:', 'wpcalibrate-timeline-builder' ); ?></strong> <?php esc_html_e( 'Add formatted descriptions and optional call-to-action buttons.', 'wpcalibrate-timeline-builder' ); ?></li>
							</ul>
						</div>
					</div>

					<div class="wpcal-step-row">
						<div class="wpcal-step-number">3</div>
						<div class="wpcal-step-body">
							<h4><?php esc_html_e( 'Customize Styles & Presets', 'wpcalibrate-timeline-builder' ); ?></h4>
							<p><?php esc_html_e( 'Choose from four professionally tuned presets: Clean, Cards, Minimal, or Modern. Fine-tune your primary accent colors, card background tones, and node shapes to match your brand palette perfectly.', 'wpcalibrate-timeline-builder' ); ?></p>
						</div>
					</div>

					<div class="wpcal-step-row">
						<div class="wpcal-step-number">4</div>
						<div class="wpcal-step-body">
							<h4><?php esc_html_e( 'Publish & Embed on Any Page or Post', 'wpcalibrate-timeline-builder' ); ?></h4>
							<p><?php esc_html_e( 'Once saved, you can embed your timeline anywhere on your WordPress site using three flexible methods:', 'wpcalibrate-timeline-builder' ); ?></p>
							<div class="wpcal-embed-options-grid">
								<div class="wpcal-embed-box">
									<div class="wpcal-embed-header">
										<span class="wpcal-embed-badge">[ ]</span>
										<strong><?php esc_html_e( 'Shortcode', 'wpcalibrate-timeline-builder' ); ?></strong>
									</div>
									<p><?php esc_html_e( 'Copy the shortcode from your Timelines list and paste it into any Classic or Block editor:', 'wpcalibrate-timeline-builder' ); ?></p>
									<code>[wpcalibrate_timeline id="123"]</code>
								</div>
								<div class="wpcal-embed-box">
									<div class="wpcal-embed-header">
										<span class="wpcal-embed-badge">🧩</span>
										<strong><?php esc_html_e( 'Gutenberg Block', 'wpcalibrate-timeline-builder' ); ?></strong>
									</div>
									<p><?php esc_html_e( 'In the block editor, click "+" and search for "WPCalibrate Timeline" to select and preview directly.', 'wpcalibrate-timeline-builder' ); ?></p>
								</div>
								<div class="wpcal-embed-box">
									<div class="wpcal-embed-header">
										<span class="wpcal-embed-badge">⚡</span>
										<strong><?php esc_html_e( 'Elementor Widget', 'wpcalibrate-timeline-builder' ); ?></strong>
									</div>
									<p><?php esc_html_e( 'Drag the "WPCalibrate Timeline" widget into your Elementor template and select your timeline from the dropdown.', 'wpcalibrate-timeline-builder' ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Method & Architecture: How It Works -->
			<div class="wpcal-overview-section">
				<h3 class="wpcal-overview-section-title">
					<span class="wpcal-section-icon">⚙️</span> <?php esc_html_e( 'Method & Technical Architecture', 'wpcalibrate-timeline-builder' ); ?>
				</h3>
				<p class="wpcal-overview-section-subtitle">
					<?php esc_html_e( 'Built from the ground up adhering to modern WordPress core development standards and strict performance constraints.', 'wpcalibrate-timeline-builder' ); ?>
				</p>

				<div class="wpcal-cards-grid-3">
					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">⚡</div>
						<h4><?php esc_html_e( 'Zero jQuery Dependency', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Frontend scripts are 100% pure modern JavaScript (ES6+). Zero reliance on jQuery eliminates unnecessary overhead and guarantees sub-15KB asset loading times.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>

					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">🗄️</div>
						<h4><?php esc_html_e( 'Custom Post Type Structure', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Utilizes native WordPress post tables (wpcal_timeline and wpcal_item) with parent-child indexing. High-performance queries without custom SQL table clutter or database bloat.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>

					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">♿</div>
						<h4><?php esc_html_e( 'WCAG 2.1 AA Accessibility', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Semantic <ol> milestone lists, proper ARIA landmark roles, full keyboard navigation with Tab and Arrow keys, and high-contrast color balance out of the box.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>

					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">🔒</div>
						<h4><?php esc_html_e( 'Enterprise-Grade Security', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Strict role verification (current_user_can), cryptographic nonce validation on all AJAX actions, and thorough input sanitization and output escaping (esc_html, wp_kses_post).', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>

					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">🎨</div>
						<h4><?php esc_html_e( 'Adaptive Theme Styling', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Clean CSS custom property tokens (--wpcal-primary, --wpcal-bg) ensure that timelines inherit typography harmoniously while maintaining pixel-perfect layout isolation.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>

					<div class="wpcal-card wpcal-card-tech">
						<div class="wpcal-card-icon">📱</div>
						<h4><?php esc_html_e( 'Mobile-First Fluid Layouts', 'wpcalibrate-timeline-builder' ); ?></h4>
						<p><?php esc_html_e( 'Zero awkward column voids. Both vertical alternating and horizontal tracks smoothly adapt with touch gestures, hardware acceleration, and seamless media queries.', 'wpcalibrate-timeline-builder' ); ?></p>
					</div>
				</div>
			</div>

			<!-- Footer Callout -->
			<div class="wpcal-overview-footer-cta">
				<div>
					<h3><?php esc_html_e( 'Ready to showcase your milestones?', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Launch the interactive timeline builder now or configure your global settings.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
				<div class="wpcal-footer-cta-buttons">
					<a href="<?php echo esc_url( $create_url ); ?>" class="button button-primary button-hero">
						<?php esc_html_e( 'Build a Timeline Now', 'wpcalibrate-timeline-builder' ); ?>
					</a>
					<a href="<?php echo esc_url( $settings_url ); ?>" class="button button-secondary">
						<?php esc_html_e( 'Plugin Settings', 'wpcalibrate-timeline-builder' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}
