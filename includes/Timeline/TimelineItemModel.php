<?php
/**
 * Timeline Item Model Entity.
 *
 * @package WPCalibrate\TimelineBuilder\Timeline
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Timeline;

use WPCalibrate\TimelineBuilder\Utilities\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineItemModel
 */
class TimelineItemModel {

	public int $id = 0;
	public int $timeline_id = 0;
	public string $title = '';
	public string $content = '';
	public string $excerpt = '';
	public string $date_type = 'exact';
	public string $date_val = '';
	public string $secondary_label = '';
	public int $image_id = 0;
	public string $icon = 'circle';
	public string $button_text = '';
	public string $button_url = '';
	public string $button_target = '_self';
	public array $categories = [];
	public string $accent_color = '';
	public int $order = 0;
	public string $status = 'publish';

	/**
	 * Construct from array.
	 *
	 * @param array $data Data.
	 */
	public function __construct( array $data = [] ) {
		if ( ! empty( $data ) ) {
			$this->populate( $data );
		}
	}

	/**
	 * Populate from array.
	 *
	 * @param array $data Attributes.
	 */
	public function populate( array $data ): void {
		if ( isset( $data['id'] ) ) {
			$this->id = (int) $data['id'];
		}
		if ( isset( $data['timeline_id'] ) ) {
			$this->timeline_id = (int) $data['timeline_id'];
		}
		if ( isset( $data['title'] ) ) {
			$this->title = Sanitizer::sanitize_text( $data['title'] );
		}
		if ( isset( $data['content'] ) ) {
			$this->content = Sanitizer::sanitize_html( $data['content'] );
		}
		if ( isset( $data['excerpt'] ) ) {
			$this->excerpt = Sanitizer::sanitize_text( $data['excerpt'] );
		}
		if ( isset( $data['date_type'] ) && in_array( $data['date_type'], [ 'exact', 'month_year', 'year_only', 'custom' ], true ) ) {
			$this->date_type = $data['date_type'];
		}
		if ( isset( $data['date_val'] ) ) {
			$this->date_val = Sanitizer::sanitize_text( $data['date_val'] );
		}
		if ( isset( $data['secondary_label'] ) ) {
			$this->secondary_label = Sanitizer::sanitize_text( $data['secondary_label'] );
		}
		if ( isset( $data['image_id'] ) ) {
			$this->image_id = (int) $data['image_id'];
		}
		if ( isset( $data['icon'] ) ) {
			$this->icon = Sanitizer::sanitize_text( $data['icon'] );
		}
		if ( isset( $data['button_text'] ) ) {
			$this->button_text = Sanitizer::sanitize_text( $data['button_text'] );
		}
		if ( isset( $data['button_url'] ) ) {
			$this->button_url = Sanitizer::sanitize_url( $data['button_url'] );
		}
		if ( isset( $data['button_target'] ) && in_array( $data['button_target'], [ '_self', '_blank' ], true ) ) {
			$this->button_target = $data['button_target'];
		}
		if ( isset( $data['categories'] ) && is_array( $data['categories'] ) ) {
			$this->categories = array_map( 'intval', $data['categories'] );
		}
		if ( isset( $data['accent_color'] ) ) {
			$this->accent_color = Sanitizer::sanitize_hex_color( $data['accent_color'], '' );
		}
		if ( isset( $data['order'] ) ) {
			$this->order = (int) $data['order'];
		}
		if ( isset( $data['status'] ) ) {
			$this->status = Sanitizer::sanitize_text( $data['status'] );
		}
	}

	/**
	 * Get formatted date label for display.
	 *
	 * @return string
	 */
	public function get_formatted_date(): string {
		$raw = trim( $this->date_val );
		if ( empty( $raw ) ) {
			return '';
		}

		switch ( $this->date_type ) {
			case 'exact':
				$time = strtotime( $raw );
				if ( false !== $time ) {
					// Use WordPress date format setting.
					$date_format = get_option( 'date_format', 'F j, Y' );
					return date_i18n( $date_format, $time );
				}
				return $raw;

			case 'month_year':
				// e.g., '2024-05' or 'May 2024'
				$time = strtotime( $raw . '-01' );
				if ( false !== $time ) {
					return date_i18n( 'F Y', $time );
				}
				return $raw;

			case 'year_only':
				// e.g. '2024'
				return preg_replace( '/[^0-9]/', '', $raw ) ?: $raw;

			case 'custom':
			default:
				return $raw;
		}
	}

	/**
	 * Extract year string for filtering (if determinable).
	 *
	 * @return string
	 */
	public function get_year(): string {
		$raw = trim( $this->date_val );
		if ( empty( $raw ) ) {
			return '';
		}

		if ( 'year_only' === $this->date_type ) {
			return preg_replace( '/[^0-9]/', '', $raw ) ?: '';
		}

		if ( 'exact' === $this->date_type || 'month_year' === $this->date_type ) {
			$time = strtotime( 'exact' === $this->date_type ? $raw : $raw . '-01' );
			if ( false !== $time ) {
				return date( 'Y', $time );
			}
		}

		// If custom, attempt to extract 4 digit year if present.
		if ( preg_match( '/\b(19\d\d|20\d\d)\b/', $raw, $matches ) ) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * Get comparable chronological timestamp for sorting.
	 * Returns null for custom text without dates.
	 *
	 * @return int|null
	 */
	public function get_timestamp(): ?int {
		$raw = trim( $this->date_val );
		if ( empty( $raw ) ) {
			return null;
		}

		switch ( $this->date_type ) {
			case 'exact':
				$time = strtotime( $raw );
				return false !== $time ? $time : null;

			case 'month_year':
				$time = strtotime( $raw . '-01' );
				return false !== $time ? $time : null;

			case 'year_only':
				$year = preg_replace( '/[^0-9]/', '', $raw );
				if ( ! empty( $year ) ) {
					$time = strtotime( $year . '-01-01' );
					return false !== $time ? $time : null;
				}
				return null;

			case 'custom':
			default:
				return null;
		}
	}

	/**
	 * Convert to array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		return [
			'id'              => $this->id,
			'timeline_id'     => $this->timeline_id,
			'title'           => $this->title,
			'content'         => $this->content,
			'excerpt'         => $this->excerpt,
			'date_type'       => $this->date_type,
			'date_val'        => $this->date_val,
			'formatted_date'  => $this->get_formatted_date(),
			'secondary_label' => $this->secondary_label,
			'image_id'        => $this->image_id,
			'image_url'       => $this->image_id > 0 ? wp_get_attachment_image_url( $this->image_id, 'large' ) : '',
			'icon'            => $this->icon,
			'button_text'     => $this->button_text,
			'button_url'      => $this->button_url,
			'button_target'   => $this->button_target,
			'categories'      => $this->categories,
			'accent_color'    => $this->accent_color,
			'order'           => $this->order,
			'status'          => $this->status,
			'year'            => $this->get_year(),
		];
	}
}
