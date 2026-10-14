<?php
/**
 * Timeline Repository for CRUD and Query Operations.
 *
 * @package WPCalibrate\TimelineBuilder\Timeline
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Timeline;

use WPCalibrate\TimelineBuilder\Core\PostTypes;
use WPCalibrate\TimelineBuilder\Core\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineRepository
 */
class TimelineRepository {

	/**
	 * Cache group.
	 */
	private const CACHE_GROUP = 'wpcal_timelines';

	/**
	 * Retrieve a timeline by ID.
	 *
	 * @param int $id Timeline ID.
	 * @return TimelineModel|null
	 */
	public function get_timeline( int $id ): ?TimelineModel {
		if ( $id <= 0 ) {
			return null;
		}

		$post = get_post( $id );
		if ( ! $post || PostTypes::TIMELINE_CPT !== $post->post_type ) {
			return null;
		}

		$model                 = new TimelineModel();
		$model->id             = $post->ID;
		$model->title          = $post->post_title;
		$model->status         = $post->post_status;
		$model->layout         = (string) get_post_meta( $id, '_wpcal_layout', true ) ?: 'vertical';
		$model->vertical_style = (string) get_post_meta( $id, '_wpcal_vertical_style', true ) ?: 'both';
		$model->preset         = (string) get_post_meta( $id, '_wpcal_preset', true ) ?: 'cards';
		$model->accent_color   = (string) get_post_meta( $id, '_wpcal_accent_color', true ) ?: '#2563eb';
		$model->line_color     = (string) get_post_meta( $id, '_wpcal_line_color', true ) ?: '#e2e8f0';
		$model->marker_color   = (string) get_post_meta( $id, '_wpcal_marker_color', true ) ?: '#2563eb';
		$model->marker_size    = (int) ( get_post_meta( $id, '_wpcal_marker_size', true ) ?: 16 );
		$model->card_bg        = (string) get_post_meta( $id, '_wpcal_card_bg', true ) ?: '#ffffff';
		$model->card_border    = (string) get_post_meta( $id, '_wpcal_card_border', true ) ?: '#e2e8f0';
		$model->border_radius  = (int) ( get_post_meta( $id, '_wpcal_border_radius', true ) ?: 8 );
		$model->text_color     = (string) get_post_meta( $id, '_wpcal_text_color', true ) ?: '#1e293b';
		$model->date_color     = (string) get_post_meta( $id, '_wpcal_date_color', true ) ?: '#64748b';
		$model->sorting        = (string) get_post_meta( $id, '_wpcal_sorting', true ) ?: 'manual';
		$model->filter_cat     = 'yes' === get_post_meta( $id, '_wpcal_filter_cat', true );
		$model->filter_year    = 'yes' === get_post_meta( $id, '_wpcal_filter_year', true );
		$model->content_align      = (string) get_post_meta( $id, '_wpcal_content_align', true ) ?: 'left';
		$model->max_width          = (int) ( get_post_meta( $id, '_wpcal_max_width', true ) ?: 1000 );
		$model->nav_btn_bg         = (string) get_post_meta( $id, '_wpcal_nav_btn_bg', true ) ?: '#ffffff';
		$model->nav_btn_color      = (string) get_post_meta( $id, '_wpcal_nav_btn_color', true ) ?: '#1e293b';
		$model->nav_btn_hover_bg   = (string) get_post_meta( $id, '_wpcal_nav_btn_hover_bg', true ) ?: '#2563eb';
		$model->nav_btn_hover_color= (string) get_post_meta( $id, '_wpcal_nav_btn_hover_color', true ) ?: '#ffffff';
		$model->nav_btn_size       = (int) ( get_post_meta( $id, '_wpcal_nav_btn_size', true ) ?: 42 );
		$model->nav_btn_shape      = (string) get_post_meta( $id, '_wpcal_nav_btn_shape', true ) ?: 'circle';
		$model->nav_btn_position   = (string) get_post_meta( $id, '_wpcal_nav_btn_position', true ) ?: 'sides';

		return $model;
	}

	/**
	 * Retrieve all timelines.
	 *
	 * @param array $args Optional query args.
	 * @return TimelineModel[]
	 */
	public function get_timelines( array $args = [] ): array {
		$defaults = [
			'post_type'      => PostTypes::TIMELINE_CPT,
			'post_status'    => [ 'publish', 'draft' ],
			'numberposts'    => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		];

		$query_args = wp_parse_args( $args, $defaults );
		$posts      = get_posts( $query_args );

		$results = [];
		foreach ( $posts as $post ) {
			$model = $this->get_timeline( $post->ID );
			if ( $model ) {
				$results[] = $model;
			}
		}

		return $results;
	}

	/**
	 * Save or update a timeline.
	 *
	 * @param TimelineModel $model Timeline model.
	 * @return int Timeline ID.
	 */
	public function save_timeline( TimelineModel $model ): int {
		$post_data = [
			'post_type'   => PostTypes::TIMELINE_CPT,
			'post_title'  => $model->title ?: __( 'Untitled Timeline', 'wpcalibrate-timeline-builder' ),
			'post_status' => $model->status ?: 'publish',
		];

		if ( $model->id > 0 ) {
			$post_data['ID'] = $model->id;
			$timeline_id     = wp_update_post( $post_data );
		} else {
			$timeline_id = wp_insert_post( $post_data );
		}

		if ( is_wp_error( $timeline_id ) || ! $timeline_id ) {
			return 0;
		}

		$model->id = (int) $timeline_id;

		// Update post meta.
		update_post_meta( $model->id, '_wpcal_layout', $model->layout );
		update_post_meta( $model->id, '_wpcal_vertical_style', $model->vertical_style );
		update_post_meta( $model->id, '_wpcal_preset', $model->preset );
		update_post_meta( $model->id, '_wpcal_accent_color', $model->accent_color );
		update_post_meta( $model->id, '_wpcal_line_color', $model->line_color );
		update_post_meta( $model->id, '_wpcal_marker_color', $model->marker_color );
		update_post_meta( $model->id, '_wpcal_marker_size', $model->marker_size );
		update_post_meta( $model->id, '_wpcal_card_bg', $model->card_bg );
		update_post_meta( $model->id, '_wpcal_card_border', $model->card_border );
		update_post_meta( $model->id, '_wpcal_border_radius', $model->border_radius );
		update_post_meta( $model->id, '_wpcal_text_color', $model->text_color );
		update_post_meta( $model->id, '_wpcal_date_color', $model->date_color );
		update_post_meta( $model->id, '_wpcal_sorting', $model->sorting );
		update_post_meta( $model->id, '_wpcal_filter_cat', $model->filter_cat ? 'yes' : 'no' );
		update_post_meta( $model->id, '_wpcal_filter_year', $model->filter_year ? 'yes' : 'no' );
		update_post_meta( $model->id, '_wpcal_animation', $model->animation ? 'yes' : 'no' );
		update_post_meta( $model->id, '_wpcal_content_align', $model->content_align );
		update_post_meta( $model->id, '_wpcal_max_width', $model->max_width );
		update_post_meta( $model->id, '_wpcal_nav_btn_bg', $model->nav_btn_bg );
		update_post_meta( $model->id, '_wpcal_nav_btn_color', $model->nav_btn_color );
		update_post_meta( $model->id, '_wpcal_nav_btn_hover_bg', $model->nav_btn_hover_bg );
		update_post_meta( $model->id, '_wpcal_nav_btn_hover_color', $model->nav_btn_hover_color );
		update_post_meta( $model->id, '_wpcal_nav_btn_size', $model->nav_btn_size );
		update_post_meta( $model->id, '_wpcal_nav_btn_shape', $model->nav_btn_shape );
		update_post_meta( $model->id, '_wpcal_nav_btn_position', $model->nav_btn_position );

		$this->invalidate_cache( $model->id );

		return $model->id;
	}

	/**
	 * Delete a timeline and all associated items.
	 *
	 * @param int $id Timeline ID.
	 * @return bool
	 */
	public function delete_timeline( int $id ): bool {
		if ( $id <= 0 ) {
			return false;
		}

		// Delete all child items.
		$items = $this->get_items( $id, [ 'post_status' => 'any' ] );
		foreach ( $items as $item ) {
			wp_delete_post( $item->id, true );
		}

		$this->invalidate_cache( $id );

		return (bool) wp_delete_post( $id, true );
	}

	/**
	 * Retrieve a single timeline item.
	 *
	 * @param int $id Item ID.
	 * @return TimelineItemModel|null
	 */
	public function get_item( int $id ): ?TimelineItemModel {
		if ( $id <= 0 ) {
			return null;
		}

		$post = get_post( $id );
		if ( ! $post || PostTypes::TIMELINE_ITEM_CPT !== $post->post_type ) {
			return null;
		}

		$item                  = new TimelineItemModel();
		$item->id              = $post->ID;
		$item->timeline_id     = (int) ( get_post_meta( $id, '_wpcal_timeline_id', true ) ?: $post->post_parent );
		$item->title           = $post->post_title;
		$item->content         = $post->post_content;
		$item->excerpt         = $post->post_excerpt;
		$item->order           = (int) $post->menu_order;
		$item->status          = $post->post_status;
		$item->date_type       = (string) get_post_meta( $id, '_wpcal_date_type', true ) ?: 'exact';
		$item->date_val        = (string) get_post_meta( $id, '_wpcal_date_val', true );
		$item->secondary_label = (string) get_post_meta( $id, '_wpcal_secondary_label', true );
		$item->image_id        = (int) ( get_post_thumbnail_id( $id ) ?: 0 );
		$item->icon            = (string) get_post_meta( $id, '_wpcal_icon', true ) ?: 'circle';
		$item->button_text     = (string) get_post_meta( $id, '_wpcal_button_text', true );
		$item->button_url      = (string) get_post_meta( $id, '_wpcal_button_url', true );
		$item->button_target   = (string) get_post_meta( $id, '_wpcal_button_target', true ) ?: '_self';
		$item->accent_color    = (string) get_post_meta( $id, '_wpcal_accent_color', true );

		// Load taxonomy terms.
		$terms = wp_get_post_terms( $id, Taxonomies::TAXONOMY, [ 'fields' => 'ids' ] );
		if ( ! is_wp_error( $terms ) && is_array( $terms ) ) {
			$item->categories = array_map( 'intval', $terms );
		}

		return $item;
	}

	/**
	 * Retrieve items belonging to a timeline.
	 *
	 * @param int   $timeline_id Parent timeline ID.
	 * @param array $args Query options.
	 * @return TimelineItemModel[]
	 */
	public function get_items( int $timeline_id, array $args = [] ): array {
		if ( $timeline_id <= 0 ) {
			return [];
		}

		$status = $args['post_status'] ?? 'publish';
		$query_args = [
			'post_type'      => PostTypes::TIMELINE_ITEM_CPT,
			'post_status'    => $status,
			'numberposts'    => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
			'meta_query'     => [
				[
					'key'     => '_wpcal_timeline_id',
					'value'   => $timeline_id,
					'compare' => '=',
				],
			],
		];

		$posts = get_posts( $query_args );

		// Fallback check by post_parent if empty.
		if ( empty( $posts ) ) {
			unset( $query_args['meta_query'] );
			$query_args['post_parent'] = $timeline_id;
			$posts                     = get_posts( $query_args );
		}

		$items = [];
		foreach ( $posts as $post ) {
			$item = $this->get_item( $post->ID );
			if ( $item ) {
				$items[] = $item;
			}
		}

		// Apply sorting mode.
		$sorting = $args['sorting'] ?? null;
		if ( null === $sorting ) {
			$timeline = $this->get_timeline( $timeline_id );
			$sorting  = $timeline ? $timeline->sorting : 'manual';
		}

		return $this->sort_items( $items, $sorting );
	}

	/**
	 * Deterministic item sorting implementation.
	 *
	 * @param TimelineItemModel[] $items Items list.
	 * @param string              $mode Sorting mode: manual, oldest, newest.
	 * @return TimelineItemModel[]
	 */
	public function sort_items( array $items, string $mode ): array {
		if ( empty( $items ) || 'manual' === $mode ) {
			usort(
				$items,
				static function ( TimelineItemModel $a, TimelineItemModel $b ): int {
					if ( $a->order !== $b->order ) {
						return $a->order <=> $b->order;
					}
					return $a->id <=> $b->id;
				}
			);
			return $items;
		}

		if ( 'oldest' === $mode ) {
			usort(
				$items,
				static function ( TimelineItemModel $a, TimelineItemModel $b ): int {
					$ts_a = $a->get_timestamp();
					$ts_b = $b->get_timestamp();

					// If both have timestamps.
					if ( null !== $ts_a && null !== $ts_b ) {
						if ( $ts_a !== $ts_b ) {
							return $ts_a <=> $ts_b;
						}
						// Tie-breaker: menu order, then ID.
						if ( $a->order !== $b->order ) {
							return $a->order <=> $b->order;
						}
						return $a->id <=> $b->id;
					}

					// Dated items come before non-dated/custom items in chronological mode.
					if ( null !== $ts_a && null === $ts_b ) {
						return -1;
					}
					if ( null === $ts_a && null !== $ts_b ) {
						return 1;
					}

					// Both are custom/non-dated: deterministic manual order, then ID.
					if ( $a->order !== $b->order ) {
						return $a->order <=> $b->order;
					}
					return $a->id <=> $b->id;
				}
			);
			return $items;
		}

		if ( 'newest' === $mode ) {
			usort(
				$items,
				static function ( TimelineItemModel $a, TimelineItemModel $b ): int {
					$ts_a = $a->get_timestamp();
					$ts_b = $b->get_timestamp();

					// If both have timestamps.
					if ( null !== $ts_a && null !== $ts_b ) {
						if ( $ts_a !== $ts_b ) {
							return $ts_b <=> $ts_a; // Descending
						}
						// Tie-breaker: menu order, then ID.
						if ( $a->order !== $b->order ) {
							return $a->order <=> $b->order;
						}
						return $a->id <=> $b->id;
					}

					// Dated items come before non-dated/custom items.
					if ( null !== $ts_a && null === $ts_b ) {
						return -1;
					}
					if ( null === $ts_a && null !== $ts_b ) {
						return 1;
					}

					// Both are custom: deterministic order.
					if ( $a->order !== $b->order ) {
						return $a->order <=> $b->order;
					}
					return $a->id <=> $b->id;
				}
			);
			return $items;
		}

		return $items;
	}

	/**
	 * Save or update a timeline item.
	 *
	 * @param TimelineItemModel $item Item model.
	 * @return int Item ID.
	 */
	public function save_item( TimelineItemModel $item ): int {
		$post_data = [
			'post_type'    => PostTypes::TIMELINE_ITEM_CPT,
			'post_title'   => $item->title ?: __( 'Untitled Item', 'wpcalibrate-timeline-builder' ),
			'post_content' => $item->content,
			'post_excerpt' => $item->excerpt,
			'post_status'  => $item->status ?: 'publish',
			'post_parent'  => $item->timeline_id,
			'menu_order'   => $item->order,
		];

		if ( $item->id > 0 ) {
			$post_data['ID'] = $item->id;
			$item_id         = wp_update_post( $post_data );
		} else {
			$item_id = wp_insert_post( $post_data );
		}

		if ( is_wp_error( $item_id ) || ! $item_id ) {
			return 0;
		}

		$item->id = (int) $item_id;

		// Save meta.
		update_post_meta( $item->id, '_wpcal_timeline_id', $item->timeline_id );
		update_post_meta( $item->id, '_wpcal_date_type', $item->date_type );
		update_post_meta( $item->id, '_wpcal_date_val', $item->date_val );
		update_post_meta( $item->id, '_wpcal_secondary_label', $item->secondary_label );
		update_post_meta( $item->id, '_wpcal_icon', $item->icon );
		update_post_meta( $item->id, '_wpcal_button_text', $item->button_text );
		update_post_meta( $item->id, '_wpcal_button_url', $item->button_url );
		update_post_meta( $item->id, '_wpcal_button_target', $item->button_target );
		update_post_meta( $item->id, '_wpcal_accent_color', $item->accent_color );

		// Featured image thumbnail.
		if ( $item->image_id > 0 ) {
			set_post_thumbnail( $item->id, $item->image_id );
		} else {
			delete_post_thumbnail( $item->id );
		}

		// Taxonomy categories.
		wp_set_post_terms( $item->id, $item->categories, Taxonomies::TAXONOMY, false );

		$this->invalidate_cache( $item->timeline_id );

		return $item->id;
	}

	/**
	 * Delete a single timeline item.
	 *
	 * @param int $id Item ID.
	 * @return bool
	 */
	public function delete_item( int $id ): bool {
		$item = $this->get_item( $id );
		if ( ! $item ) {
			return false;
		}

		$timeline_id = $item->timeline_id;
		$deleted     = wp_delete_post( $id, true );

		if ( $deleted ) {
			$this->invalidate_cache( $timeline_id );
			return true;
		}

		return false;
	}

	/**
	 * Reorder items in a timeline.
	 *
	 * @param int   $timeline_id Timeline ID.
	 * @param int[] $item_ids Ordered array of item IDs.
	 * @return bool
	 */
	public function reorder_items( int $timeline_id, array $item_ids ): bool {
		global $wpdb;

		$order = 0;
		foreach ( $item_ids as $item_id ) {
			$item_id = (int) $item_id;
			if ( $item_id > 0 ) {
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $order ],
					[ 'ID' => $item_id ],
					[ '%d' ],
					[ '%d' ]
				);
				clean_post_cache( $item_id );
				$order++;
			}
		}

		$this->invalidate_cache( $timeline_id );

		return true;
	}

	/**
	 * Invalidate cached output for a timeline.
	 *
	 * @param int $timeline_id Timeline ID.
	 */
	public function invalidate_cache( int $timeline_id ): void {
		if ( $timeline_id <= 0 ) {
			return;
		}
		delete_transient( 'wpcal_timeline_render_' . $timeline_id );
	}
}
