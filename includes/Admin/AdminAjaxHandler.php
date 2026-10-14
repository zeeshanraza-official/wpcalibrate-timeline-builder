<?php
/**
 * Admin AJAX Handlers.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Plugin;
use WPCalibrate\TimelineBuilder\Timeline\TimelineModel;
use WPCalibrate\TimelineBuilder\Timeline\TimelineItemModel;
use WPCalibrate\TimelineBuilder\Core\Capabilities;
use WPCalibrate\TimelineBuilder\Utilities\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminAjaxHandler
 */
class AdminAjaxHandler {

	/**
	 * Register AJAX hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_wpcal_save_timeline', [ self::class, 'save_timeline' ] );
		add_action( 'wp_ajax_wpcal_delete_timeline', [ self::class, 'delete_timeline' ] );
		add_action( 'wp_ajax_wpcal_duplicate_timeline', [ self::class, 'duplicate_timeline' ] );
		add_action( 'wp_ajax_wpcal_duplicate_item', [ self::class, 'duplicate_item' ] );
		add_action( 'wp_ajax_wpcal_delete_item', [ self::class, 'delete_item' ] );
		add_action( 'wp_ajax_wpcal_reorder_items', [ self::class, 'reorder_items' ] );
		add_action( 'wp_ajax_wpcal_preview_timeline', [ self::class, 'preview_timeline' ] );
	}

	/**
	 * Verify permissions and nonce for AJAX operations.
	 */
	private static function verify_access(): void {
		if ( ! check_ajax_referer( 'wpcal_timeline_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed. Please refresh the page and try again.', 'wpcalibrate-timeline-builder' ) ], 403 );
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'wpcalibrate-timeline-builder' ) ], 403 );
		}
	}

	/**
	 * Save full timeline and items via AJAX.
	 */
	public static function save_timeline(): void {
		self::verify_access();

		$repository  = Plugin::get_instance()->get_repository();
		$timeline_id = isset( $_POST['timeline_id'] ) ? (int) $_POST['timeline_id'] : 0;

		$timeline = $timeline_id > 0 ? $repository->get_timeline( $timeline_id ) : new TimelineModel();
		if ( ! $timeline ) {
			$timeline = new TimelineModel();
		}

		$timeline->title          = Sanitizer::sanitize_text( $_POST['timeline_title'] ?? '' );
		$timeline->status         = in_array( $_POST['timeline_status'] ?? '', [ 'publish', 'draft' ], true ) ? sanitize_key( $_POST['timeline_status'] ) : 'publish';
		$timeline->layout         = in_array( $_POST['layout'] ?? '', [ 'vertical', 'horizontal' ], true ) ? sanitize_key( $_POST['layout'] ) : 'vertical';
		$timeline->vertical_style = in_array( $_POST['vertical_style'] ?? '', [ 'both', 'left', 'right' ], true ) ? sanitize_key( $_POST['vertical_style'] ) : 'both';
		$timeline->preset         = in_array( $_POST['preset'] ?? '', [ 'clean', 'cards', 'minimal', 'modern', 'compact' ], true ) ? sanitize_key( $_POST['preset'] ) : 'cards';
		$timeline->accent_color   = Sanitizer::sanitize_hex_color( $_POST['accent_color'] ?? '#2563eb', '#2563eb' );
		$timeline->line_color     = Sanitizer::sanitize_hex_color( $_POST['line_color'] ?? '#e2e8f0', '#e2e8f0' );
		$timeline->marker_color   = Sanitizer::sanitize_hex_color( $_POST['marker_color'] ?? '#2563eb', '#2563eb' );
		$timeline->marker_size    = Sanitizer::sanitize_int( $_POST['marker_size'] ?? 16, 16, 8, 48 );
		$timeline->card_bg        = Sanitizer::sanitize_hex_color( $_POST['card_bg'] ?? '#ffffff', '#ffffff' );
		$timeline->card_border    = Sanitizer::sanitize_hex_color( $_POST['card_border'] ?? '#e2e8f0', '#e2e8f0' );
		$timeline->border_radius  = Sanitizer::sanitize_int( $_POST['border_radius'] ?? 8, 8, 0, 40 );
		$timeline->text_color     = Sanitizer::sanitize_hex_color( $_POST['text_color'] ?? '#1e293b', '#1e293b' );
		$timeline->date_color     = Sanitizer::sanitize_hex_color( $_POST['date_color'] ?? '#64748b', '#64748b' );
		$timeline->sorting        = in_array( $_POST['sorting'] ?? '', [ 'manual', 'oldest', 'newest' ], true ) ? sanitize_key( $_POST['sorting'] ) : 'manual';
		$timeline->filter_cat     = isset( $_POST['filter_cat'] ) && 'yes' === $_POST['filter_cat'];
		$timeline->filter_year     = isset( $_POST['filter_year'] ) && 'yes' === $_POST['filter_year'];
		$timeline->animation       = isset( $_POST['animation'] ) && 'yes' === $_POST['animation'];
		$timeline->max_width       = Sanitizer::sanitize_int( $_POST['max_width'] ?? 1000, 1000, 300, 2400 );
		$timeline->nav_btn_bg      = Sanitizer::sanitize_hex_color( $_POST['nav_btn_bg'] ?? '#ffffff', '#ffffff' );
		$timeline->nav_btn_color   = Sanitizer::sanitize_hex_color( $_POST['nav_btn_color'] ?? '#1e293b', '#1e293b' );
		$timeline->nav_btn_hover_bg= Sanitizer::sanitize_hex_color( $_POST['nav_btn_hover_bg'] ?? '#2563eb', '#2563eb' );
		$timeline->nav_btn_hover_color = Sanitizer::sanitize_hex_color( $_POST['nav_btn_hover_color'] ?? '#ffffff', '#ffffff' );
		$timeline->nav_btn_size    = Sanitizer::sanitize_int( $_POST['nav_btn_size'] ?? 42, 42, 28, 64 );
		$timeline->nav_btn_shape   = in_array( $_POST['nav_btn_shape'] ?? '', [ 'circle', 'rounded', 'square' ], true ) ? sanitize_key( $_POST['nav_btn_shape'] ) : 'circle';
		$timeline->nav_btn_position= in_array( $_POST['nav_btn_position'] ?? '', [ 'sides', 'top', 'bottom' ], true ) ? sanitize_key( $_POST['nav_btn_position'] ) : 'sides';

		$saved_id = $repository->save_timeline( $timeline );
		if ( ! $saved_id ) {
			wp_send_json_error( [ 'message' => __( 'Failed to save timeline.', 'wpcalibrate-timeline-builder' ) ] );
		}

		// Process items if submitted.
		$items_data = isset( $_POST['items'] ) && is_array( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : [];
		$order      = 0;
		$saved_items = [];

		foreach ( $items_data as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$item_id = isset( $row['id'] ) ? (int) $row['id'] : 0;
			$item    = $item_id > 0 ? $repository->get_item( $item_id ) : new TimelineItemModel();
			if ( ! $item ) {
				$item = new TimelineItemModel();
			}

			$item->timeline_id     = $saved_id;
			$item->title           = Sanitizer::sanitize_text( $row['title'] ?? '' );
			$item->secondary_label = Sanitizer::sanitize_text( $row['secondary_label'] ?? '' );
			$item->date_type       = in_array( $row['date_type'] ?? '', [ 'exact', 'month_year', 'year_only', 'custom' ], true ) ? sanitize_key( $row['date_type'] ) : 'exact';
			$item->date_val        = Sanitizer::sanitize_text( $row['date_val'] ?? '' );
			$item->content         = Sanitizer::sanitize_html( $row['content'] ?? '' );
			$item->image_id        = (int) ( $row['image_id'] ?? 0 );
			$item->icon            = Sanitizer::sanitize_text( $row['icon'] ?? 'circle' );
			$item->accent_color    = Sanitizer::sanitize_hex_color( $row['accent_color'] ?? '', '' );
			$item->button_text     = Sanitizer::sanitize_text( $row['button_text'] ?? '' );
			$item->button_url      = Sanitizer::sanitize_url( $row['button_url'] ?? '' );
			$item->button_target   = in_array( $row['button_target'] ?? '', [ '_self', '_blank' ], true ) ? sanitize_key( $row['button_target'] ) : '_self';
			$item->order           = $order++;
			$item->status          = 'publish';

			if ( isset( $row['categories'] ) && is_array( $row['categories'] ) ) {
				$item->categories = array_map( 'intval', $row['categories'] );
			}

			$saved_item_id = $repository->save_item( $item );
			if ( $saved_item_id ) {
				$saved_items[] = [
					'temp_id' => $item_id,
					'real_id' => $saved_item_id,
				];
			}
		}

		$shortcode = sprintf( '[wpcalibrate_timeline id="%d"]', $saved_id );

		wp_send_json_success(
			[
				'timeline_id' => $saved_id,
				'shortcode'   => $shortcode,
				'items'       => $saved_items,
				'redirect'    => admin_url( 'admin.php?page=wpcal-timeline-builder&action=edit&id=' . $saved_id ),
				'message'     => __( 'Timeline saved successfully!', 'wpcalibrate-timeline-builder' ),
			]
		);
	}

	/**
	 * Delete a timeline.
	 */
	public static function delete_timeline(): void {
		self::verify_access();

		$id         = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$repository = Plugin::get_instance()->get_repository();

		if ( $repository->delete_timeline( $id ) ) {
			wp_send_json_success( [ 'message' => __( 'Timeline deleted successfully.', 'wpcalibrate-timeline-builder' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to delete timeline.', 'wpcalibrate-timeline-builder' ) ] );
		}
	}

	/**
	 * Duplicate a timeline.
	 */
	public static function duplicate_timeline(): void {
		self::verify_access();

		$id         = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$duplicator = Plugin::get_instance()->get_duplicator();
		$new_id     = $duplicator->duplicate_timeline( $id );

		if ( $new_id > 0 ) {
			wp_send_json_success(
				[
					'new_id'   => $new_id,
					'redirect' => admin_url( 'admin.php?page=wpcal-timeline-builder&action=edit&id=' . $new_id ),
					'message'  => __( 'Timeline duplicated successfully.', 'wpcalibrate-timeline-builder' ),
				]
			);
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to duplicate timeline.', 'wpcalibrate-timeline-builder' ) ] );
		}
	}

	/**
	 * Duplicate a single item.
	 */
	public static function duplicate_item(): void {
		self::verify_access();

		$id         = isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : 0;
		$duplicator = Plugin::get_instance()->get_duplicator();
		$new_id     = $duplicator->duplicate_item( $id );

		if ( $new_id > 0 ) {
			$repository = Plugin::get_instance()->get_repository();
			$item       = $repository->get_item( $new_id );
			wp_send_json_success(
				[
					'new_id'  => $new_id,
					'item'    => $item ? $item->to_array() : null,
					'message' => __( 'Item duplicated successfully.', 'wpcalibrate-timeline-builder' ),
				]
			);
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to duplicate item.', 'wpcalibrate-timeline-builder' ) ] );
		}
	}

	/**
	 * Delete a single item.
	 */
	public static function delete_item(): void {
		self::verify_access();

		$id         = isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : 0;
		$repository = Plugin::get_instance()->get_repository();

		if ( $repository->delete_item( $id ) ) {
			wp_send_json_success( [ 'message' => __( 'Item deleted.', 'wpcalibrate-timeline-builder' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to delete item.', 'wpcalibrate-timeline-builder' ) ] );
		}
	}

	/**
	 * Reorder items in timeline.
	 */
	public static function reorder_items(): void {
		self::verify_access();

		$timeline_id = isset( $_POST['timeline_id'] ) ? (int) $_POST['timeline_id'] : 0;
		$item_ids    = isset( $_POST['item_ids'] ) && is_array( $_POST['item_ids'] ) ? array_map( 'intval', $_POST['item_ids'] ) : [];

		if ( $timeline_id <= 0 || empty( $item_ids ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid reorder data.', 'wpcalibrate-timeline-builder' ) ] );
		}

		$repository = Plugin::get_instance()->get_repository();
		$success    = $repository->reorder_items( $timeline_id, $item_ids );

		if ( $success ) {
			wp_send_json_success( [ 'message' => __( 'Order updated successfully.', 'wpcalibrate-timeline-builder' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to update order.', 'wpcalibrate-timeline-builder' ) ] );
		}
	}

	/**
	 * Generate live preview output.
	 */
	public static function preview_timeline(): void {
		self::verify_access();

		$timeline_id = isset( $_POST['timeline_id'] ) ? (int) $_POST['timeline_id'] : 0;
		if ( $timeline_id <= 0 ) {
			wp_send_json_error( [ 'message' => __( 'Timeline must be saved before previewing.', 'wpcalibrate-timeline-builder' ) ] );
		}

		$renderer = Plugin::get_instance()->get_renderer();
		$html     = $renderer->render( $timeline_id, [], true );

		wp_send_json_success( [ 'html' => $html ] );
	}
}
