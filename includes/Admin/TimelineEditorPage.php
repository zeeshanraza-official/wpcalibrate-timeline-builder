<?php
/**
 * Admin Timeline Editor Page.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Plugin;
use WPCalibrate\TimelineBuilder\Timeline\TimelineModel;
use WPCalibrate\TimelineBuilder\Core\Capabilities;
use WPCalibrate\TimelineBuilder\Core\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineEditorPage
 */
class TimelineEditorPage {

	/**
	 * Available safe icons list for selector.
	 *
	 * @var string[]
	 */
	public const SAFE_ICONS = [
		'circle'    => 'Dot / Circle',
		'star'      => 'Star',
		'calendar'  => 'Calendar',
		'flag'      => 'Flag',
		'briefcase' => 'Briefcase',
		'check'     => 'Checkmark',
		'trophy'    => 'Trophy',
		'rocket'    => 'Rocket',
		'lightbulb' => 'Lightbulb',
		'code'      => 'Code',
		'heart'     => 'Heart',
		'milestone' => 'Milestone Post',
		'clock'     => 'Clock',
	];

	/**
	 * Render the full interactive timeline editor.
	 */
	public static function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-timeline-builder' ) );
		}

		$repository  = Plugin::get_instance()->get_repository();
		$timeline_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $timeline_id > 0 ) {
			$timeline = $repository->get_timeline( $timeline_id );
			if ( ! $timeline ) {
				wp_die( esc_html__( 'Timeline not found.', 'wpcalibrate-timeline-builder' ) );
			}
		} else {
			$timeline        = new TimelineModel();
			$timeline->title = __( 'New Timeline', 'wpcalibrate-timeline-builder' );
		}

		$items      = $timeline->id > 0 ? $repository->get_items( $timeline->id, [ 'post_status' => 'any', 'sorting' => 'manual' ] ) : [];
		$categories = get_terms( [ 'taxonomy' => Taxonomies::TAXONOMY, 'hide_empty' => false ] );
		if ( is_wp_error( $categories ) ) {
			$categories = [];
		}

		$shortcode = $timeline->id > 0 ? sprintf( '[wpcalibrate_timeline id="%d"]', $timeline->id ) : '';
		?>
		<div class="wrap wpcal-admin-wrap wpcal-editor-wrap">
			<?php MenuManager::render_header_tabs( $timeline->id > 0 ? 'timelines' : 'new' ); ?>

			<form id="wpcal-timeline-form" method="post" action="">
				<?php wp_nonce_field( 'wpcal_timeline_admin_nonce', 'wpcal_nonce' ); ?>
				<input type="hidden" name="timeline_id" id="wpcal_timeline_id" value="<?php echo esc_attr( (string) $timeline->id ); ?>" />

				<!-- Editor Top Action Bar -->
				<div class="wpcal-editor-topbar">
					<div class="wpcal-topbar-left">
						<input type="text" name="timeline_title" id="wpcal_timeline_title"
							value="<?php echo esc_attr( $timeline->title ); ?>"
							placeholder="<?php esc_attr_e( 'Enter Timeline Title...', 'wpcalibrate-timeline-builder' ); ?>"
							class="wpcal-title-input" required />
						
						<?php if ( $timeline->id > 0 ) : ?>
							<span class="wpcal-shortcode-pill" title="<?php esc_attr_e( 'Click to copy shortcode', 'wpcalibrate-timeline-builder' ); ?>" data-copy="<?php echo esc_attr( $shortcode ); ?>">
								<code><?php echo esc_html( $shortcode ); ?></code>
							</span>
						<?php endif; ?>
					</div>

					<div class="wpcal-topbar-right">
						<select name="timeline_status" id="wpcal_timeline_status" class="wpcal-select-status">
							<option value="publish" <?php selected( $timeline->status, 'publish' ); ?>><?php esc_html_e( 'Published', 'wpcalibrate-timeline-builder' ); ?></option>
							<option value="draft" <?php selected( $timeline->status, 'draft' ); ?>><?php esc_html_e( 'Draft', 'wpcalibrate-timeline-builder' ); ?></option>
						</select>

						<?php if ( $timeline->id > 0 ) : ?>
							<button type="button" class="button wpcal-preview-modal-trigger" id="wpcal_open_preview">
								👁 <?php esc_html_e( 'Preview Timeline', 'wpcalibrate-timeline-builder' ); ?>
							</button>
						<?php endif; ?>

						<button type="submit" class="button button-primary button-hero" id="wpcal_save_timeline_btn">
							💾 <?php esc_html_e( 'Save Timeline', 'wpcalibrate-timeline-builder' ); ?>
						</button>
					</div>
				</div>

				<!-- Dual Panel Editor Layout -->
				<div class="wpcal-editor-grid">
					
					<!-- Left/Main Column: Items List & Item Builder -->
					<div class="wpcal-editor-main">
						<div class="wpcal-panel-card">
							<div class="wpcal-panel-header">
								<div>
									<h3><?php esc_html_e( 'Timeline Items', 'wpcalibrate-timeline-builder' ); ?></h3>
									<p class="wpcal-panel-desc"><?php esc_html_e( 'Add, customize, and reorder milestone events.', 'wpcalibrate-timeline-builder' ); ?></p>
								</div>
								<button type="button" class="button button-primary" id="wpcal_add_item_btn">
									+ <?php esc_html_e( 'Add Item', 'wpcalibrate-timeline-builder' ); ?>
								</button>
							</div>

							<div id="wpcal-items-container" class="wpcal-items-list" data-empty-text="<?php esc_attr_e( 'No items yet. Click "+ Add Item" above to create your first milestone.', 'wpcalibrate-timeline-builder' ); ?>">
								<?php if ( empty( $items ) ) : ?>
									<div class="wpcal-items-empty-state">
										<p><?php esc_html_e( 'No items yet. Click "+ Add Item" above to create your first milestone event.', 'wpcalibrate-timeline-builder' ); ?></p>
									</div>
								<?php else : ?>
									<?php
									$order_index = 0;
									foreach ( $items as $item ) {
										self::render_item_editor_row( $item, $order_index++, $categories );
									}
									?>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Right Column: Settings, Layout, and Styling -->
					<div class="wpcal-editor-sidebar">
						
						<!-- Layout & Preset Settings -->
						<div class="wpcal-panel-card">
							<h3 class="wpcal-card-heading"><?php esc_html_e( 'Layout & Preset', 'wpcalibrate-timeline-builder' ); ?></h3>

							<div class="wpcal-form-group">
								<label for="wpcal_layout"><strong><?php esc_html_e( 'Layout Mode', 'wpcalibrate-timeline-builder' ); ?></strong></label>
								<select name="layout" id="wpcal_layout" class="widefat">
									<option value="vertical" <?php selected( $timeline->layout, 'vertical' ); ?>><?php esc_html_e( 'Vertical Timeline', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="horizontal" <?php selected( $timeline->layout, 'horizontal' ); ?>><?php esc_html_e( 'Horizontal Timeline', 'wpcalibrate-timeline-builder' ); ?></option>
								</select>
							</div>

							<div class="wpcal-form-group" id="wpcal_vertical_style_group" style="<?php echo ( 'horizontal' === $timeline->layout ) ? 'display:none;' : ''; ?>">
								<label for="wpcal_vertical_style"><?php esc_html_e( 'Vertical Alignment', 'wpcalibrate-timeline-builder' ); ?></label>
								<select name="vertical_style" id="wpcal_vertical_style" class="widefat">
									<option value="both" <?php selected( $timeline->vertical_style, 'both' ); ?>><?php esc_html_e( 'Alternating (Both sides)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="left" <?php selected( $timeline->vertical_style, 'left' ); ?>><?php esc_html_e( 'Single Side - Left Aligned', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="right" <?php selected( $timeline->vertical_style, 'right' ); ?>><?php esc_html_e( 'Single Side - Right Aligned', 'wpcalibrate-timeline-builder' ); ?></option>
								</select>
							</div>

							<div class="wpcal-form-group">
								<label for="wpcal_preset"><strong><?php esc_html_e( 'Visual Preset', 'wpcalibrate-timeline-builder' ); ?></strong></label>
								<select name="preset" id="wpcal_preset" class="widefat">
									<option value="clean" <?php selected( $timeline->preset, 'clean' ); ?>><?php esc_html_e( 'Clean (Minimal lines, light dots)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="cards" <?php selected( $timeline->preset, 'cards' ); ?>><?php esc_html_e( 'Cards (Boxed shadow cards)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="minimal" <?php selected( $timeline->preset, 'minimal' ); ?>><?php esc_html_e( 'Minimal (Typography focused, borderless)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="modern" <?php selected( $timeline->preset, 'modern' ); ?>><?php esc_html_e( 'Modern (Gradient accents, sleek badges)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="compact" <?php selected( $timeline->preset, 'compact' ); ?>><?php esc_html_e( 'Compact (Condensed spacing for sidebars)', 'wpcalibrate-timeline-builder' ); ?></option>
								</select>
							</div>

							<div class="wpcal-form-group">
								<label for="wpcal_sorting"><strong><?php esc_html_e( 'Ordering Method', 'wpcalibrate-timeline-builder' ); ?></strong></label>
								<select name="sorting" id="wpcal_sorting" class="widefat">
									<option value="manual" <?php selected( $timeline->sorting, 'manual' ); ?>><?php esc_html_e( 'Manual (Drag & Drop / Reorder buttons)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="oldest" <?php selected( $timeline->sorting, 'oldest' ); ?>><?php esc_html_e( 'Chronological - Oldest First (Ascending)', 'wpcalibrate-timeline-builder' ); ?></option>
									<option value="newest" <?php selected( $timeline->sorting, 'newest' ); ?>><?php esc_html_e( 'Chronological - Newest First (Descending)', 'wpcalibrate-timeline-builder' ); ?></option>
								</select>
								<span class="description"><?php esc_html_e( 'Deterministic secondary sorting applies to tied dates.', 'wpcalibrate-timeline-builder' ); ?></span>
							</div>
						</div>

						<!-- Design & Colors Settings -->
						<div class="wpcal-panel-card">
							<h3 class="wpcal-card-heading"><?php esc_html_e( 'Colors & Styling', 'wpcalibrate-timeline-builder' ); ?></h3>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_accent_color"><?php esc_html_e( 'Accent Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="accent_color" id="wpcal_accent_color" value="<?php echo esc_attr( $timeline->accent_color ); ?>" class="wpcal-color-picker" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_line_color"><?php esc_html_e( 'Line Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="line_color" id="wpcal_line_color" value="<?php echo esc_attr( $timeline->line_color ); ?>" class="wpcal-color-picker" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_marker_color"><?php esc_html_e( 'Marker Node Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="marker_color" id="wpcal_marker_color" value="<?php echo esc_attr( $timeline->marker_color ); ?>" class="wpcal-color-picker" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_marker_size"><?php esc_html_e( 'Marker Size (px)', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="number" name="marker_size" id="wpcal_marker_size" min="8" max="48" value="<?php echo esc_attr( (string) $timeline->marker_size ); ?>" class="small-text" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_card_bg"><?php esc_html_e( 'Card Background', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="card_bg" id="wpcal_card_bg" value="<?php echo esc_attr( $timeline->card_bg ); ?>" class="wpcal-color-picker" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_card_border"><?php esc_html_e( 'Card Border Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="card_border" id="wpcal_card_border" value="<?php echo esc_attr( $timeline->card_border ); ?>" class="wpcal-color-picker" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_text_color"><?php esc_html_e( 'Text Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="text_color" id="wpcal_text_color" value="<?php echo esc_attr( $timeline->text_color ); ?>" class="wpcal-color-picker" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_date_color"><?php esc_html_e( 'Date Label Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="date_color" id="wpcal_date_color" value="<?php echo esc_attr( $timeline->date_color ); ?>" class="wpcal-color-picker" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_border_radius"><?php esc_html_e( 'Border Radius (px)', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="number" name="border_radius" id="wpcal_border_radius" min="0" max="40" value="<?php echo esc_attr( (string) $timeline->border_radius ); ?>" class="small-text" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_max_width"><?php esc_html_e( 'Max Container Width (px)', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="number" name="max_width" id="wpcal_max_width" min="300" max="2400" value="<?php echo esc_attr( (string) $timeline->max_width ); ?>" class="small-text" />
								</div>
							</div>

							<hr class="wpcal-section-divider" style="margin: 20px 0 16px 0; border: 0; border-top: 1px solid #e2e8f0;" />
							<h4 class="wpcal-subheading" style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a;"><?php esc_html_e( 'Horizontal Slider Buttons Styling', 'wpcalibrate-timeline-builder' ); ?></h4>
							<p class="description" style="margin: 0 0 14px 0; font-size: 12px; color: #64748b;"><?php esc_html_e( 'Customize sliding buttons appearance, colors, and positioning for horizontal timeline.', 'wpcalibrate-timeline-builder' ); ?></p>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_position"><?php esc_html_e( 'Button Position', 'wpcalibrate-timeline-builder' ); ?></label>
									<select name="nav_btn_position" id="wpcal_nav_btn_position" class="widefat">
										<option value="sides" <?php selected( $timeline->nav_btn_position, 'sides' ); ?>><?php esc_html_e( 'Sides (Dedicated Gutters - No Card Overlap)', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="top" <?php selected( $timeline->nav_btn_position, 'top' ); ?>><?php esc_html_e( 'Top Header (Aligned Right)', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="bottom" <?php selected( $timeline->nav_btn_position, 'bottom' ); ?>><?php esc_html_e( 'Bottom Track (Centered)', 'wpcalibrate-timeline-builder' ); ?></option>
									</select>
								</div>
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_shape"><?php esc_html_e( 'Button Shape', 'wpcalibrate-timeline-builder' ); ?></label>
									<select name="nav_btn_shape" id="wpcal_nav_btn_shape" class="widefat">
										<option value="circle" <?php selected( $timeline->nav_btn_shape, 'circle' ); ?>><?php esc_html_e( 'Circular', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="rounded" <?php selected( $timeline->nav_btn_shape, 'rounded' ); ?>><?php esc_html_e( 'Rounded Corners', 'wpcalibrate-timeline-builder' ); ?></option>
										<option value="square" <?php selected( $timeline->nav_btn_shape, 'square' ); ?>><?php esc_html_e( 'Square', 'wpcalibrate-timeline-builder' ); ?></option>
									</select>
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_size"><?php esc_html_e( 'Button Size (px)', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="number" name="nav_btn_size" id="wpcal_nav_btn_size" min="28" max="64" value="<?php echo esc_attr( (string) $timeline->nav_btn_size ); ?>" class="small-text" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_bg"><?php esc_html_e( 'Button Background', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="nav_btn_bg" id="wpcal_nav_btn_bg" value="<?php echo esc_attr( $timeline->nav_btn_bg ); ?>" class="wpcal-color-picker" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_color"><?php esc_html_e( 'Button Arrow Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="nav_btn_color" id="wpcal_nav_btn_color" value="<?php echo esc_attr( $timeline->nav_btn_color ); ?>" class="wpcal-color-picker" />
								</div>
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_hover_bg"><?php esc_html_e( 'Button Hover Background', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="nav_btn_hover_bg" id="wpcal_nav_btn_hover_bg" value="<?php echo esc_attr( $timeline->nav_btn_hover_bg ); ?>" class="wpcal-color-picker" />
								</div>
							</div>

							<div class="wpcal-form-row">
								<div class="wpcal-col">
									<label for="wpcal_nav_btn_hover_color"><?php esc_html_e( 'Button Hover Arrow Color', 'wpcalibrate-timeline-builder' ); ?></label>
									<input type="text" name="nav_btn_hover_color" id="wpcal_nav_btn_hover_color" value="<?php echo esc_attr( $timeline->nav_btn_hover_color ); ?>" class="wpcal-color-picker" />
								</div>
							</div>
						</div>

						<!-- Filtering & Animation Options -->
						<div class="wpcal-panel-card">
							<h3 class="wpcal-card-heading"><?php esc_html_e( 'Frontend Filters & Animation', 'wpcalibrate-timeline-builder' ); ?></h3>

							<div class="wpcal-checkbox-row">
								<label>
									<input type="checkbox" name="filter_cat" value="yes" <?php checked( $timeline->filter_cat ); ?> />
									<?php esc_html_e( 'Enable Category Filter Tabs', 'wpcalibrate-timeline-builder' ); ?>
								</label>
							</div>

							<div class="wpcal-checkbox-row">
								<label>
									<input type="checkbox" name="filter_year" value="yes" <?php checked( $timeline->filter_year ); ?> />
									<?php esc_html_e( 'Enable Year Filter Tabs', 'wpcalibrate-timeline-builder' ); ?>
								</label>
							</div>

							<div class="wpcal-checkbox-row">
								<label>
									<input type="checkbox" name="animation" value="yes" <?php checked( $timeline->animation ); ?> />
									<?php esc_html_e( 'Enable Scroll Reveal Animations', 'wpcalibrate-timeline-builder' ); ?>
								</label>
							</div>
						</div>

					</div>
				</div>
			</form>

			<!-- Live Preview Modal Container -->
			<div id="wpcal_preview_modal" class="wpcal-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="wpcal_preview_title">
				<div class="wpcal-modal-backdrop"></div>
				<div class="wpcal-modal-dialog">
					<div class="wpcal-modal-header">
						<h3 id="wpcal_preview_title"><?php esc_html_e( 'Live Timeline Preview', 'wpcalibrate-timeline-builder' ); ?></h3>
						<button type="button" class="wpcal-modal-close" aria-label="<?php esc_attr_e( 'Close Preview', 'wpcalibrate-timeline-builder' ); ?>">&times;</button>
					</div>
					<div class="wpcal-modal-body" id="wpcal_preview_canvas">
						<div class="wpcal-preview-loading"><?php esc_html_e( 'Generating preview...', 'wpcalibrate-timeline-builder' ); ?></div>
					</div>
				</div>
			</div>

			<!-- Underscore / JavaScript template for new timeline item row -->
			<script type="text/template" id="tmpl-wpcal-item-row">
				<?php
				$dummy_item = new \WPCalibrate\TimelineBuilder\Timeline\TimelineItemModel();
				$dummy_item->title = __( 'New Milestone Event', 'wpcalibrate-timeline-builder' );
				$dummy_item->date_val = current_time( 'Y-m-d' );
				self::render_item_editor_row( $dummy_item, 9999, $categories, true );
				?>
			</script>
		</div>
		<?php
	}

	/**
	 * Render single item editor collapsible row.
	 *
	 * @param \WPCalibrate\TimelineBuilder\Timeline\TimelineItemModel $item Item model.
	 * @param int                                                    $index Index.
	 * @param array                                                  $categories Categories list.
	 * @param bool                                                   $is_template Is JS template.
	 */
	public static function render_item_editor_row( $item, int $index, array $categories = [], bool $is_template = false ): void {
		$idx_token = $is_template ? '{{data.index}}' : (string) $index;
		$id_val    = $is_template ? '0' : (string) $item->id;
		$image_url = ( $item->image_id > 0 ) ? wp_get_attachment_image_url( $item->image_id, 'thumbnail' ) : '';
		?>
		<div class="wpcal-item-row" data-index="<?php echo esc_attr( $idx_token ); ?>" data-item-id="<?php echo esc_attr( $id_val ); ?>">
			<input type="hidden" name="items[<?php echo esc_attr( $idx_token ); ?>][id]" value="<?php echo esc_attr( $id_val ); ?>" class="wpcal-item-id-field" />
			<input type="hidden" name="items[<?php echo esc_attr( $idx_token ); ?>][order]" value="<?php echo esc_attr( (string) $item->order ); ?>" class="wpcal-item-order-field" />

			<!-- Collapsible Header -->
			<div class="wpcal-item-row-header">
				<div class="wpcal-item-drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'wpcalibrate-timeline-builder' ); ?>">☰</div>
				
				<!-- Keyboard Accessible Reordering Buttons -->
				<div class="wpcal-reorder-buttons" role="group" aria-label="<?php esc_attr_e( 'Reorder item', 'wpcalibrate-timeline-builder' ); ?>">
					<button type="button" class="button button-small wpcal-move-up-btn" aria-label="<?php esc_attr_e( 'Move item up', 'wpcalibrate-timeline-builder' ); ?>" title="<?php esc_attr_e( 'Move Up', 'wpcalibrate-timeline-builder' ); ?>">▲</button>
					<button type="button" class="button button-small wpcal-move-down-btn" aria-label="<?php esc_attr_e( 'Move item down', 'wpcalibrate-timeline-builder' ); ?>" title="<?php esc_attr_e( 'Move Down', 'wpcalibrate-timeline-builder' ); ?>">▼</button>
				</div>

				<div class="wpcal-item-title-summary">
					<strong class="wpcal-summary-title"><?php echo esc_html( $item->title ?: __( 'Untitled Milestone', 'wpcalibrate-timeline-builder' ) ); ?></strong>
					<span class="wpcal-summary-date wpcal-badge wpcal-badge-neutral"><?php echo esc_html( $item->get_formatted_date() ?: __( 'No Date', 'wpcalibrate-timeline-builder' ) ); ?></span>
				</div>

				<div class="wpcal-item-row-actions">
					<button type="button" class="button button-small wpcal-duplicate-item-btn" title="<?php esc_attr_e( 'Duplicate Item', 'wpcalibrate-timeline-builder' ); ?>">📋</button>
					<button type="button" class="button button-small wpcal-delete-item-btn text-danger" title="<?php esc_attr_e( 'Delete Item', 'wpcalibrate-timeline-builder' ); ?>">🗑</button>
					<button type="button" class="button button-small wpcal-toggle-item-btn" aria-expanded="true">▾</button>
				</div>
			</div>

			<!-- Collapsible Body -->
			<div class="wpcal-item-row-body">
				<div class="wpcal-row-fields">
					
					<!-- Title & Secondary Label -->
					<div class="wpcal-form-row">
						<div class="wpcal-col wpcal-col-8">
							<label><strong><?php esc_html_e( 'Milestone Title', 'wpcalibrate-timeline-builder' ); ?></strong></label>
							<input type="text" name="items[<?php echo esc_attr( $idx_token ); ?>][title]" value="<?php echo esc_attr( $item->title ); ?>" class="widefat wpcal-item-title-input" placeholder="<?php esc_attr_e( 'e.g., Company Founded, Series A Funding', 'wpcalibrate-timeline-builder' ); ?>" />
						</div>
						<div class="wpcal-col wpcal-col-4">
							<label><?php esc_html_e( 'Secondary Badge (Optional)', 'wpcalibrate-timeline-builder' ); ?></label>
							<input type="text" name="items[<?php echo esc_attr( $idx_token ); ?>][secondary_label]" value="<?php echo esc_attr( $item->secondary_label ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g., Milestone 1, v2.0', 'wpcalibrate-timeline-builder' ); ?>" />
						</div>
					</div>

					<!-- Date Type & Value -->
					<div class="wpcal-form-row wpcal-date-row">
						<div class="wpcal-col wpcal-col-4">
							<label><strong><?php esc_html_e( 'Date Type', 'wpcalibrate-timeline-builder' ); ?></strong></label>
							<select name="items[<?php echo esc_attr( $idx_token ); ?>][date_type]" class="widefat wpcal-date-type-select">
								<option value="exact" <?php selected( $item->date_type, 'exact' ); ?>><?php esc_html_e( 'Exact Date (YYYY-MM-DD)', 'wpcalibrate-timeline-builder' ); ?></option>
								<option value="month_year" <?php selected( $item->date_type, 'month_year' ); ?>><?php esc_html_e( 'Month & Year (YYYY-MM)', 'wpcalibrate-timeline-builder' ); ?></option>
								<option value="year_only" <?php selected( $item->date_type, 'year_only' ); ?>><?php esc_html_e( 'Year Only (YYYY)', 'wpcalibrate-timeline-builder' ); ?></option>
								<option value="custom" <?php selected( $item->date_type, 'custom' ); ?>><?php esc_html_e( 'Custom Text (e.g. Founded, Phase One)', 'wpcalibrate-timeline-builder' ); ?></option>
							</select>
						</div>
						<div class="wpcal-col wpcal-col-8">
							<label><strong><?php esc_html_e( 'Date / Label Value', 'wpcalibrate-timeline-builder' ); ?></strong></label>
							<input type="text" name="items[<?php echo esc_attr( $idx_token ); ?>][date_val]" value="<?php echo esc_attr( $item->date_val ); ?>" class="widefat wpcal-date-val-input" placeholder="<?php esc_attr_e( 'Enter date or custom label...', 'wpcalibrate-timeline-builder' ); ?>" />
							<span class="wpcal-date-hint description"><?php esc_html_e( 'Supports exact dates, month/year, 4-digit years, or custom non-Gregorian labels.', 'wpcalibrate-timeline-builder' ); ?></span>
						</div>
					</div>

					<!-- Content / Description -->
					<div class="wpcal-form-group">
						<label><strong><?php esc_html_e( 'Description / Body Content', 'wpcalibrate-timeline-builder' ); ?></strong></label>
						<textarea name="items[<?php echo esc_attr( $idx_token ); ?>][content]" rows="4" class="widefat wpcal-item-content-input" placeholder="<?php esc_attr_e( 'Describe this milestone event...', 'wpcalibrate-timeline-builder' ); ?>"><?php echo esc_textarea( $item->content ); ?></textarea>
					</div>

					<!-- Media Thumbnail & Icon Selection -->
					<div class="wpcal-form-row">
						<div class="wpcal-col wpcal-col-6">
							<label><?php esc_html_e( 'Featured Image', 'wpcalibrate-timeline-builder' ); ?></label>
							<div class="wpcal-media-picker">
								<input type="hidden" name="items[<?php echo esc_attr( $idx_token ); ?>][image_id]" value="<?php echo esc_attr( (string) $item->image_id ); ?>" class="wpcal-image-id-field" />
								<div class="wpcal-image-preview-wrap">
									<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="wpcal-image-preview" style="<?php echo empty( $image_url ) ? 'display:none;' : ''; ?>" />
								</div>
								<div class="wpcal-media-actions">
									<button type="button" class="button button-small wpcal-select-img-btn"><?php esc_html_e( 'Choose Image', 'wpcalibrate-timeline-builder' ); ?></button>
									<button type="button" class="button button-small wpcal-remove-img-btn text-danger" style="<?php echo empty( $image_url ) ? 'display:none;' : ''; ?>"><?php esc_html_e( 'Remove', 'wpcalibrate-timeline-builder' ); ?></button>
								</div>
							</div>
						</div>

						<div class="wpcal-col wpcal-col-6">
							<label><?php esc_html_e( 'Marker Icon', 'wpcalibrate-timeline-builder' ); ?></label>
							<select name="items[<?php echo esc_attr( $idx_token ); ?>][icon]" class="widefat">
								<?php foreach ( self::SAFE_ICONS as $icon_slug => $icon_name ) : ?>
									<option value="<?php echo esc_attr( $icon_slug ); ?>" <?php selected( $item->icon, $icon_slug ); ?>>
										<?php echo esc_html( $icon_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<div style="margin-top: 12px;">
								<label><?php esc_html_e( 'Custom Item Accent Color (Optional)', 'wpcalibrate-timeline-builder' ); ?></label>
								<input type="text" name="items[<?php echo esc_attr( $idx_token ); ?>][accent_color]" value="<?php echo esc_attr( $item->accent_color ); ?>" class="wpcal-color-picker" />
							</div>
						</div>
					</div>

					<!-- Button Settings -->
					<div class="wpcal-form-row">
						<div class="wpcal-col wpcal-col-4">
							<label><?php esc_html_e( 'Button Text (Optional)', 'wpcalibrate-timeline-builder' ); ?></label>
							<input type="text" name="items[<?php echo esc_attr( $idx_token ); ?>][button_text]" value="<?php echo esc_attr( $item->button_text ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Read More', 'wpcalibrate-timeline-builder' ); ?>" />
						</div>
						<div class="wpcal-col wpcal-col-5">
							<label><?php esc_html_e( 'Button URL', 'wpcalibrate-timeline-builder' ); ?></label>
							<input type="url" name="items[<?php echo esc_attr( $idx_token ); ?>][button_url]" value="<?php echo esc_attr( $item->button_url ); ?>" class="widefat" placeholder="https://" />
						</div>
						<div class="wpcal-col wpcal-col-3">
							<label><?php esc_html_e( 'Target', 'wpcalibrate-timeline-builder' ); ?></label>
							<select name="items[<?php echo esc_attr( $idx_token ); ?>][button_target]" class="widefat">
								<option value="_self" <?php selected( $item->button_target, '_self' ); ?>><?php esc_html_e( 'Same Window (_self)', 'wpcalibrate-timeline-builder' ); ?></option>
								<option value="_blank" <?php selected( $item->button_target, '_blank' ); ?>><?php esc_html_e( 'New Tab (_blank)', 'wpcalibrate-timeline-builder' ); ?></option>
							</select>
						</div>
					</div>

					<!-- Category Assignment -->
					<?php if ( ! empty( $categories ) ) : ?>
						<div class="wpcal-form-group">
							<label><?php esc_html_e( 'Categories', 'wpcalibrate-timeline-builder' ); ?></label>
							<div class="wpcal-categories-checklist">
								<?php foreach ( $categories as $cat ) : ?>
									<label class="wpcal-cat-label">
										<input type="checkbox" name="items[<?php echo esc_attr( $idx_token ); ?>][categories][]" value="<?php echo esc_attr( (string) $cat->term_id ); ?>" <?php checked( in_array( (int) $cat->term_id, $item->categories, true ) ); ?> />
										<?php echo esc_html( $cat->name ); ?>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
		<?php
	}
}
