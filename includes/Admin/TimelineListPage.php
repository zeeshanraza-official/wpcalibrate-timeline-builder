<?php
/**
 * Admin Timelines List Page.
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
 * Class TimelineListPage
 */
class TimelineListPage {

	/**
	 * Render the timelines listing screen.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}

		$repository = Plugin::get_instance()->get_repository();
		$timelines  = $repository->get_timelines();
		?>
		<div class="wrap wpcal-admin-wrap">
			<?php MenuManager::render_header_tabs( 'timelines' ); ?>

			<div class="wpcal-section-header">
				<div>
					<h2><?php esc_html_e( 'Your Timelines', 'wpcalibrate-timeline-builder' ); ?></h2>
					<p class="wpcal-section-desc"><?php esc_html_e( 'Manage your company histories, roadmaps, process milestones, and event timelines.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcal-timeline-builder&action=new' ) ); ?>" class="button button-primary button-hero">
					+ <?php esc_html_e( 'Add New Timeline', 'wpcalibrate-timeline-builder' ); ?>
				</a>
			</div>

			<?php if ( empty( $timelines ) ) : ?>
				<div class="wpcal-empty-state">
					<div class="wpcal-empty-icon">⏳</div>
					<h3><?php esc_html_e( 'No timelines created yet', 'wpcalibrate-timeline-builder' ); ?></h3>
					<p><?php esc_html_e( 'Get started by creating your first milestone roadmap or company history timeline.', 'wpcalibrate-timeline-builder' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcal-timeline-builder&action=new' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Create Your First Timeline', 'wpcalibrate-timeline-builder' ); ?>
					</a>
				</div>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped table-view-list wpcal-table">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-title column-primary"><?php esc_html_e( 'Timeline Title', 'wpcalibrate-timeline-builder' ); ?></th>
							<th scope="col" class="manage-column column-items"><?php esc_html_e( 'Items', 'wpcalibrate-timeline-builder' ); ?></th>
							<th scope="col" class="manage-column column-layout"><?php esc_html_e( 'Layout & Preset', 'wpcalibrate-timeline-builder' ); ?></th>
							<th scope="col" class="manage-column column-shortcode"><?php esc_html_e( 'Shortcode', 'wpcalibrate-timeline-builder' ); ?></th>
							<th scope="col" class="manage-column column-status"><?php esc_html_e( 'Status', 'wpcalibrate-timeline-builder' ); ?></th>
							<th scope="col" class="manage-column column-actions"><?php esc_html_e( 'Actions', 'wpcalibrate-timeline-builder' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $timelines as $tl ) :
							$items      = $repository->get_items( $tl->id, [ 'post_status' => 'any' ] );
							$item_count = count( $items );
							$edit_url   = admin_url( 'admin.php?page=wpcal-timeline-builder&action=edit&id=' . $tl->id );
							$shortcode  = sprintf( '[wpcalibrate_timeline id="%d"]', $tl->id );
							?>
							<tr data-timeline-id="<?php echo esc_attr( (string) $tl->id ); ?>">
								<td class="column-title has-row-actions">
									<strong>
										<a href="<?php echo esc_url( $edit_url ); ?>" class="row-title">
											<?php echo esc_html( $tl->title ?: __( 'Untitled Timeline', 'wpcalibrate-timeline-builder' ) ); ?>
										</a>
									</strong>
									<div class="row-actions">
										<span class="edit"><a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit', 'wpcalibrate-timeline-builder' ); ?></a> | </span>
										<span class="duplicate"><a href="#" class="wpcal-duplicate-timeline-btn" data-id="<?php echo esc_attr( (string) $tl->id ); ?>"><?php esc_html_e( 'Duplicate', 'wpcalibrate-timeline-builder' ); ?></a> | </span>
										<span class="trash"><a href="#" class="wpcal-delete-timeline-btn text-danger" data-id="<?php echo esc_attr( (string) $tl->id ); ?>"><?php esc_html_e( 'Delete', 'wpcalibrate-timeline-builder' ); ?></a></span>
									</div>
								</td>
								<td class="column-items">
									<span class="wpcal-badge wpcal-badge-neutral"><?php echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'wpcalibrate-timeline-builder' ), $item_count ) ); ?></span>
								</td>
								<td class="column-layout">
									<span class="wpcal-badge wpcal-badge-info"><?php echo esc_html( ucfirst( $tl->layout ) ); ?></span>
									<span class="wpcal-badge wpcal-badge-secondary"><?php echo esc_html( ucfirst( $tl->preset ) ); ?></span>
								</td>
								<td class="column-shortcode">
									<div class="wpcal-shortcode-copy-box">
										<input type="text" readonly value="<?php echo esc_attr( $shortcode ); ?>" class="wpcal-shortcode-input" />
										<button type="button" class="button button-small wpcal-copy-btn" data-clipboard-text="<?php echo esc_attr( $shortcode ); ?>" title="<?php esc_attr_e( 'Copy shortcode', 'wpcalibrate-timeline-builder' ); ?>">
											<?php esc_html_e( 'Copy', 'wpcalibrate-timeline-builder' ); ?>
										</button>
									</div>
								</td>
								<td class="column-status">
									<?php if ( 'publish' === $tl->status ) : ?>
										<span class="wpcal-status-pill is-published"><?php esc_html_e( 'Published', 'wpcalibrate-timeline-builder' ); ?></span>
									<?php else : ?>
										<span class="wpcal-status-pill is-draft"><?php esc_html_e( 'Draft', 'wpcalibrate-timeline-builder' ); ?></span>
									<?php endif; ?>
								</td>
								<td class="column-actions">
									<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-small"><?php esc_html_e( 'Edit Timeline', 'wpcalibrate-timeline-builder' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}
}
