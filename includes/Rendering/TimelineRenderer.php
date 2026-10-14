<?php
/**
 * Central Timeline Renderer.
 *
 * @package WPCalibrate\TimelineBuilder\Rendering
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Rendering;

use WPCalibrate\TimelineBuilder\Timeline\TimelineModel;
use WPCalibrate\TimelineBuilder\Timeline\TimelineItemModel;
use WPCalibrate\TimelineBuilder\Timeline\TimelineRepository;
use WPCalibrate\TimelineBuilder\Core\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineRenderer
 */
class TimelineRenderer {

	/**
	 * Repository.
	 *
	 * @var TimelineRepository
	 */
	private TimelineRepository $repository;

	/**
	 * Constructor.
	 *
	 * @param TimelineRepository $repository Repository.
	 */
	public function __construct( TimelineRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Central render method used by Shortcode, Gutenberg, Elementor, and Admin Preview.
	 *
	 * @param int   $timeline_id Timeline post ID.
	 * @param array $overrides Optional layout/preset/filter overrides.
	 * @param bool  $is_preview Whether rendering inside admin preview.
	 * @return string Rendered HTML output.
	 */
	public function render( int $timeline_id, array $overrides = [], bool $is_preview = false ): string {
		if ( $timeline_id <= 0 ) {
			return $is_preview ? '<div class="wpcal-timeline-notice">' . esc_html__( 'Please select or create a timeline.', 'wpcalibrate-timeline-builder' ) . '</div>' : '';
		}

		$timeline = $this->repository->get_timeline( $timeline_id );
		if ( ! $timeline ) {
			return $is_preview ? '<div class="wpcal-timeline-notice wpcal-notice-error">' . esc_html__( 'Timeline not found or has been deleted.', 'wpcalibrate-timeline-builder' ) . '</div>' : '';
		}

		// Don't render unpublished timelines on frontend unless admin preview or current user can edit.
		if ( ! $is_preview && 'publish' !== $timeline->status && ! current_user_can( 'edit_post', $timeline_id ) ) {
			return '';
		}

		// Enqueue required frontend assets.
		if ( ! $is_preview ) {
			AssetManager::enqueue_frontend_assets();
		}

		// Apply overrides (from shortcode/block/widget attributes).
		if ( ! empty( $overrides['layout'] ) && in_array( $overrides['layout'], [ 'vertical', 'horizontal' ], true ) ) {
			$timeline->layout = $overrides['layout'];
		}
		if ( ! empty( $overrides['preset'] ) && in_array( $overrides['preset'], [ 'clean', 'cards', 'minimal', 'modern', 'compact' ], true ) ) {
			$timeline->preset = $overrides['preset'];
		}
		if ( isset( $overrides['filter'] ) ) {
			if ( 'none' === $overrides['filter'] ) {
				$timeline->filter_cat  = false;
				$timeline->filter_year = false;
			} elseif ( 'category' === $overrides['filter'] ) {
				$timeline->filter_cat  = true;
				$timeline->filter_year = false;
			} elseif ( 'year' === $overrides['filter'] ) {
				$timeline->filter_cat  = false;
				$timeline->filter_year = true;
			} elseif ( 'all' === $overrides['filter'] ) {
				$timeline->filter_cat  = true;
				$timeline->filter_year = true;
			}
		}

		// Query items.
		$item_status = $is_preview ? 'any' : 'publish';
		$items       = $this->repository->get_items( $timeline_id, [ 'post_status' => $item_status ] );

		/**
		 * Action before timeline render.
		 *
		 * @param TimelineModel     $timeline Timeline instance.
		 * @param TimelineItemModel[] $items Items list.
		 */
		do_action( 'wpcal_timeline_before_render', $timeline, $items );

		// Build CSS variables.
		$css_vars = $this->build_css_variables( $timeline );

		// Container classes.
		$wrap_classes = [
			'wpcal-timeline-wrap',
			'wpcal-timeline-' . $timeline->id,
			'wpcal-layout-' . sanitize_html_class( $timeline->layout ),
			'wpcal-preset-' . sanitize_html_class( $timeline->preset ),
			'wpcal-align-' . sanitize_html_class( $timeline->content_align ),
		];

		if ( 'vertical' === $timeline->layout ) {
			$wrap_classes[] = 'wpcal-vertical-' . sanitize_html_class( $timeline->vertical_style );
		}
		if ( $timeline->animation ) {
			$wrap_classes[] = 'wpcal-has-animation';
		}
		if ( $is_preview ) {
			$wrap_classes[] = 'wpcal-is-preview';
		}

		ob_start();
		?>
		<div id="wpcal-timeline-<?php echo esc_attr( (string) $timeline->id ); ?>"
			class="<?php echo esc_attr( implode( ' ', $wrap_classes ) ); ?>"
			style="<?php echo esc_attr( $css_vars ); ?>"
			data-timeline-id="<?php echo esc_attr( (string) $timeline->id ); ?>"
			data-layout="<?php echo esc_attr( $timeline->layout ); ?>"
			data-preset="<?php echo esc_attr( $timeline->preset ); ?>"
			role="region"
			aria-label="<?php echo esc_attr( $timeline->title ); ?>">

			<?php
			// Render Filter Controls if enabled and items exist.
			if ( ! empty( $items ) && ( $timeline->filter_cat || $timeline->filter_year ) ) {
				$this->render_filters( $timeline, $items );
			}
			?>

			<?php if ( empty( $items ) ) : ?>
				<div class="wpcal-timeline-empty" role="status">
					<p><?php esc_html_e( 'No timeline items to display.', 'wpcalibrate-timeline-builder' ); ?></p>
				</div>
			<?php else : ?>
				<?php if ( 'horizontal' === $timeline->layout ) : ?>
					<div class="wpcal-horizontal-container wpcal-nav-pos-<?php echo esc_attr( $timeline->nav_btn_position ); ?>">
						<?php if ( 'top' === $timeline->nav_btn_position ) : ?>
							<div class="wpcal-horizontal-top-controls">
								<button type="button" class="wpcal-nav-btn wpcal-nav-prev" aria-label="<?php esc_attr_e( 'Previous timeline item', 'wpcalibrate-timeline-builder' ); ?>">
									<span aria-hidden="true">&lsaquo;</span>
								</button>
								<button type="button" class="wpcal-nav-btn wpcal-nav-next" aria-label="<?php esc_attr_e( 'Next timeline item', 'wpcalibrate-timeline-builder' ); ?>">
									<span aria-hidden="true">&rsaquo;</span>
								</button>
							</div>
						<?php elseif ( 'sides' === $timeline->nav_btn_position ) : ?>
							<button type="button" class="wpcal-nav-btn wpcal-nav-prev" aria-label="<?php esc_attr_e( 'Previous timeline item', 'wpcalibrate-timeline-builder' ); ?>">
								<span aria-hidden="true">&lsaquo;</span>
							</button>
							<button type="button" class="wpcal-nav-btn wpcal-nav-next" aria-label="<?php esc_attr_e( 'Next timeline item', 'wpcalibrate-timeline-builder' ); ?>">
								<span aria-hidden="true">&rsaquo;</span>
							</button>
						<?php endif; ?>

						<div class="wpcal-horizontal-track" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Horizontal timeline scroll track', 'wpcalibrate-timeline-builder' ); ?>">
							<div class="wpcal-timeline-line" aria-hidden="true"></div>
							<div class="wpcal-timeline-items">
								<?php
								$index = 0;
								foreach ( $items as $item ) {
									$this->render_item( $item, $timeline, $index++ );
								}
								?>
							</div>
						</div>

						<?php if ( 'bottom' === $timeline->nav_btn_position ) : ?>
							<div class="wpcal-horizontal-bottom-controls">
								<button type="button" class="wpcal-nav-btn wpcal-nav-prev" aria-label="<?php esc_attr_e( 'Previous timeline item', 'wpcalibrate-timeline-builder' ); ?>">
									<span aria-hidden="true">&lsaquo;</span>
								</button>
								<button type="button" class="wpcal-nav-btn wpcal-nav-next" aria-label="<?php esc_attr_e( 'Next timeline item', 'wpcalibrate-timeline-builder' ); ?>">
									<span aria-hidden="true">&rsaquo;</span>
								</button>
							</div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="wpcal-vertical-container">
						<div class="wpcal-timeline-line" aria-hidden="true"></div>
						<div class="wpcal-timeline-items">
							<?php
							$index = 0;
							foreach ( $items as $item ) {
								$this->render_item( $item, $timeline, $index++ );
							}
							?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

		</div>
		<?php
		$html = ob_get_clean();

		/**
		 * Action after timeline render.
		 *
		 * @param TimelineModel     $timeline Timeline instance.
		 * @param TimelineItemModel[] $items Items list.
		 */
		do_action( 'wpcal_timeline_after_render', $timeline, $items );

		/**
		 * Filter rendered timeline HTML.
		 *
		 * @param string            $html Rendered output.
		 * @param TimelineModel     $timeline Timeline instance.
		 * @param TimelineItemModel[] $items Items list.
		 */
		return apply_filters( 'wpcal_timeline_rendered_html', $html, $timeline, $items );
	}

	/**
	 * Render single timeline item.
	 *
	 * @param TimelineItemModel $item Timeline item model.
	 * @param TimelineModel     $timeline Parent timeline model.
	 * @param int               $index Item sequential index.
	 */
	private function render_item( TimelineItemModel $item, TimelineModel $timeline, int $index ): void {
		$side_class = ( 0 === $index % 2 ) ? 'wpcal-item-left' : 'wpcal-item-right';
		if ( 'vertical' === $timeline->layout && 'left' === $timeline->vertical_style ) {
			$side_class = 'wpcal-item-right';
		} elseif ( 'vertical' === $timeline->layout && 'right' === $timeline->vertical_style ) {
			$side_class = 'wpcal-item-left';
		}

		$item_classes = [
			'wpcal-timeline-item',
			'wpcal-item-' . $item->id,
			$side_class,
		];

		if ( $timeline->animation ) {
			$item_classes[] = 'wpcal-animated-item';
		}

		// Categories data attribute for filtering.
		$cat_slugs = [];
		if ( ! empty( $item->categories ) ) {
			foreach ( $item->categories as $term_id ) {
				$term = get_term( $term_id, Taxonomies::TAXONOMY );
				if ( $term && ! is_wp_error( $term ) ) {
					$cat_slugs[] = $term->slug;
				}
			}
		}

		$year_val = $item->get_year();

		// Per-item accent color override style.
		$item_style = '';
		if ( ! empty( $item->accent_color ) ) {
			$item_style = '--wpcal-item-accent: ' . esc_attr( $item->accent_color ) . ';';
		}

		/**
		 * Filter single item HTML classes.
		 *
		 * @param array             $item_classes Array of CSS classes.
		 * @param TimelineItemModel $item Item model.
		 * @param TimelineModel     $timeline Timeline model.
		 */
		$item_classes = apply_filters( 'wpcal_timeline_item_classes', $item_classes, $item, $timeline );
		?>
		<article class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"
			data-item-id="<?php echo esc_attr( (string) $item->id ); ?>"
			data-categories="<?php echo esc_attr( implode( ',', $cat_slugs ) ); ?>"
			data-year="<?php echo esc_attr( $year_val ); ?>"
			<?php echo ! empty( $item_style ) ? 'style="' . esc_attr( $item_style ) . '"' : ''; ?>>

			<?php if ( 'vertical' === $timeline->layout ) : ?>
				<div class="wpcal-timeline-meta" aria-hidden="true">
					<?php if ( ! empty( $item->date_val ) ) : ?>
						<time class="wpcal-meta-date" datetime="<?php echo esc_attr( $item->date_val ); ?>">
							<?php echo esc_html( $item->get_formatted_date() ); ?>
						</time>
					<?php endif; ?>
					<?php if ( ! empty( $item->secondary_label ) ) : ?>
						<span class="wpcal-meta-badge"><?php echo esc_html( $item->secondary_label ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="wpcal-timeline-marker" aria-hidden="true">
				<span class="wpcal-marker-node">
					<?php echo $this->get_icon_svg( $item->icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>

			<?php if ( 'horizontal' === $timeline->layout ) : ?>
				<div class="wpcal-marker-stem" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="wpcal-timeline-card">
				<header class="wpcal-card-header">
					<?php if ( ! empty( $item->date_val ) ) : ?>
						<time class="wpcal-timeline-date" datetime="<?php echo esc_attr( $item->date_val ); ?>">
							<?php echo esc_html( $item->get_formatted_date() ); ?>
						</time>
					<?php endif; ?>

					<?php if ( ! empty( $item->secondary_label ) ) : ?>
						<span class="wpcal-timeline-badge">
							<?php echo esc_html( $item->secondary_label ); ?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $item->title ) ) : ?>
						<h3 class="wpcal-timeline-title"><?php echo esc_html( $item->title ); ?></h3>
					<?php endif; ?>
				</header>

				<?php if ( $item->image_id > 0 ) : ?>
					<div class="wpcal-timeline-image">
						<?php
						echo wp_get_attachment_image(
							$item->image_id,
							'large',
							false,
							[
								'class'   => 'wpcal-card-img',
								'loading' => 'lazy',
							]
						);
						?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $item->content ) || ! empty( $item->excerpt ) ) : ?>
					<div class="wpcal-timeline-content">
						<?php
						if ( ! empty( $item->content ) ) {
							echo wp_kses_post( $item->content );
						} elseif ( ! empty( $item->excerpt ) ) {
							echo wp_kses_post( wpautop( $item->excerpt ) );
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $item->button_text ) && ! empty( $item->button_url ) ) : ?>
					<footer class="wpcal-card-footer">
						<a href="<?php echo esc_url( $item->button_url ); ?>"
							class="wpcal-timeline-btn"
							target="<?php echo esc_attr( $item->button_target ); ?>"
							<?php echo ( '_blank' === $item->button_target ) ? 'rel="noopener noreferrer"' : ''; ?>>
							<?php echo esc_html( $item->button_text ); ?>
						</a>
					</footer>
				<?php endif; ?>
			</div>

		</article>
		<?php
	}

	/**
	 * Render category and year filtering UI.
	 *
	 * @param TimelineModel     $timeline Timeline model.
	 * @param TimelineItemModel[] $items Items list.
	 */
	private function render_filters( TimelineModel $timeline, array $items ): void {
		$categories = [];
		$years      = [];

		foreach ( $items as $item ) {
			if ( $timeline->filter_cat && ! empty( $item->categories ) ) {
				foreach ( $item->categories as $term_id ) {
					if ( ! isset( $categories[ $term_id ] ) ) {
						$term = get_term( $term_id, Taxonomies::TAXONOMY );
						if ( $term && ! is_wp_error( $term ) ) {
							$categories[ $term_id ] = $term;
						}
					}
				}
			}

			if ( $timeline->filter_year ) {
				$year = $item->get_year();
				if ( ! empty( $year ) && ! in_array( $year, $years, true ) ) {
					$years[] = $year;
				}
			}
		}

		if ( empty( $categories ) && empty( $years ) ) {
			return;
		}

		sort( $years );
		?>
		<div class="wpcal-timeline-filters" role="group" aria-label="<?php esc_attr_e( 'Timeline filters', 'wpcalibrate-timeline-builder' ); ?>">

			<?php if ( $timeline->filter_cat && ! empty( $categories ) ) : ?>
				<div class="wpcal-filter-group wpcal-filter-categories" role="tablist" aria-label="<?php esc_attr_e( 'Filter by Category', 'wpcalibrate-timeline-builder' ); ?>">
					<button type="button" class="wpcal-filter-btn is-active" data-filter-type="category" data-filter-value="all" role="tab" aria-selected="true">
						<?php esc_html_e( 'All', 'wpcalibrate-timeline-builder' ); ?>
					</button>
					<?php foreach ( $categories as $term ) : ?>
						<button type="button" class="wpcal-filter-btn" data-filter-type="category" data-filter-value="<?php echo esc_attr( $term->slug ); ?>" role="tab" aria-selected="false">
							<?php echo esc_html( $term->name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $timeline->filter_year && ! empty( $years ) ) : ?>
				<div class="wpcal-filter-group wpcal-filter-years" role="tablist" aria-label="<?php esc_attr_e( 'Filter by Year', 'wpcalibrate-timeline-builder' ); ?>">
					<button type="button" class="wpcal-filter-btn is-active" data-filter-type="year" data-filter-value="all" role="tab" aria-selected="true">
						<?php esc_html_e( 'All Years', 'wpcalibrate-timeline-builder' ); ?>
					</button>
					<?php foreach ( $years as $year ) : ?>
						<button type="button" class="wpcal-filter-btn" data-filter-type="year" data-filter-value="<?php echo esc_attr( $year ); ?>" role="tab" aria-selected="false">
							<?php echo esc_html( $year ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Build CSS custom variables for per-timeline scoped styling.
	 *
	 * @param TimelineModel $timeline Timeline model.
	 * @return string CSS variable string.
	 */
	private function build_css_variables( TimelineModel $timeline ): string {
		$shape_radius = '50%';
		if ( 'rounded' === $timeline->nav_btn_shape ) {
			$shape_radius = '8px';
		} elseif ( 'square' === $timeline->nav_btn_shape ) {
			$shape_radius = '2px';
		}

		$vars = [
			'--wpcal-accent'              => $timeline->accent_color,
			'--wpcal-line'                => $timeline->line_color,
			'--wpcal-marker'              => $timeline->marker_color,
			'--wpcal-marker-size'         => $timeline->marker_size . 'px',
			'--wpcal-card-bg'             => $timeline->card_bg,
			'--wpcal-card-border'         => $timeline->card_border,
			'--wpcal-border-radius'       => $timeline->border_radius . 'px',
			'--wpcal-text'                => $timeline->text_color,
			'--wpcal-date'                => $timeline->date_color,
			'--wpcal-max-width'           => $timeline->max_width . 'px',
			'--wpcal-nav-btn-bg'          => $timeline->nav_btn_bg,
			'--wpcal-nav-btn-color'       => $timeline->nav_btn_color,
			'--wpcal-nav-btn-hover-bg'    => $timeline->nav_btn_hover_bg,
			'--wpcal-nav-btn-hover-color' => $timeline->nav_btn_hover_color,
			'--wpcal-nav-btn-size'        => $timeline->nav_btn_size . 'px',
			'--wpcal-nav-btn-radius'      => $shape_radius,
		];

		$declarations = [];
		foreach ( $vars as $name => $val ) {
			$declarations[] = $name . ': ' . $val;
		}

		return implode( '; ', $declarations ) . ';';
	}

	/**
	 * Provide safe inline SVG marker icons.
	 *
	 * @param string $icon_id Icon identifier.
	 * @return string Safe inline SVG.
	 */
	public function get_icon_svg( string $icon_id ): string {
		switch ( $icon_id ) {
			case 'star':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
			case 'calendar':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>';
			case 'flag':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>';
			case 'briefcase':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>';
			case 'check':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';
			case 'trophy':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>';
			case 'rocket':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M13.13 2.05C11.66 2.61 9.87 3.86 8.5 5.23c-2.45 2.45-3.5 5.5-3.5 8.77l-2 2v2l3.5-.5.5 3.5h2l2-2c3.27 0 6.32-1.05 8.77-3.5 1.37-1.37 2.62-3.16 3.18-4.63.15-.39-.18-.77-.55-.66-1.57.48-3.32.22-4.66-1.12-1.34-1.34-1.6-3.09-1.12-4.66.11-.37-.27-.7-.66-.55z"/></svg>';
			case 'lightbulb':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z"/></svg>';
			case 'code':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>';
			case 'heart':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
			case 'milestone':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M18.5 4H5c-.55 0-1 .45-1 1v14c0 .55.45 1 1 1s1-.45 1-1v-5h12.5c.34 0 .65-.17.83-.46l3-4.5c.23-.35.23-.8 0-1.15l-3-4.5c-.18-.29-.49-.46-.83-.46z"/></svg>';
			case 'clock':
				return '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>';
			case 'circle':
			default:
				return '<span class="wpcal-dot-marker" aria-hidden="true"></span>';
		}
	}
}
