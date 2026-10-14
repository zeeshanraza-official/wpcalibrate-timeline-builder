<?php
/**
 * Timeline Model Entity.
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
 * Class TimelineModel
 */
class TimelineModel {

	public int $id = 0;
	public string $title = '';
	public string $status = 'publish';
	public string $layout = 'vertical';
	public string $vertical_style = 'both';
	public string $preset = 'cards';
	public string $accent_color = '#2563eb';
	public string $line_color = '#e2e8f0';
	public string $marker_color = '#2563eb';
	public int $marker_size = 16;
	public string $card_bg = '#ffffff';
	public string $card_border = '#e2e8f0';
	public int $border_radius = 8;
	public string $text_color = '#1e293b';
	public string $date_color = '#64748b';
	public string $sorting = 'manual';
	public bool $filter_cat = false;
	public bool $filter_year = false;
	public bool $animation = true;
	public string $content_align = 'left';
	public int $max_width = 1000;
	public string $nav_btn_bg = '#ffffff';
	public string $nav_btn_color = '#1e293b';
	public string $nav_btn_hover_bg = '#2563eb';
	public string $nav_btn_hover_color = '#ffffff';
	public int $nav_btn_size = 42;
	public string $nav_btn_shape = 'circle';
	public string $nav_btn_position = 'sides';

	/**
	 * Construct model from array or post.
	 *
	 * @param array $data Attributes.
	 */
	public function __construct( array $data = [] ) {
		if ( ! empty( $data ) ) {
			$this->populate( $data );
		}
	}

	/**
	 * Populate from array.
	 *
	 * @param array $data Data.
	 */
	public function populate( array $data ): void {
		if ( isset( $data['id'] ) ) {
			$this->id = (int) $data['id'];
		}
		if ( isset( $data['title'] ) ) {
			$this->title = Sanitizer::sanitize_text( $data['title'] );
		}
		if ( isset( $data['status'] ) ) {
			$this->status = Sanitizer::sanitize_text( $data['status'] );
		}
		if ( isset( $data['layout'] ) && in_array( $data['layout'], [ 'vertical', 'horizontal' ], true ) ) {
			$this->layout = $data['layout'];
		}
		if ( isset( $data['vertical_style'] ) && in_array( $data['vertical_style'], [ 'both', 'left', 'right' ], true ) ) {
			$this->vertical_style = $data['vertical_style'];
		}
		if ( isset( $data['preset'] ) && in_array( $data['preset'], [ 'clean', 'cards', 'minimal', 'modern', 'compact' ], true ) ) {
			$this->preset = $data['preset'];
		}
		if ( isset( $data['accent_color'] ) ) {
			$this->accent_color = Sanitizer::sanitize_hex_color( $data['accent_color'], '#2563eb' );
		}
		if ( isset( $data['line_color'] ) ) {
			$this->line_color = Sanitizer::sanitize_hex_color( $data['line_color'], '#e2e8f0' );
		}
		if ( isset( $data['marker_color'] ) ) {
			$this->marker_color = Sanitizer::sanitize_hex_color( $data['marker_color'], '#2563eb' );
		}
		if ( isset( $data['marker_size'] ) ) {
			$this->marker_size = Sanitizer::sanitize_int( $data['marker_size'], 16, 8, 48 );
		}
		if ( isset( $data['card_bg'] ) ) {
			$this->card_bg = Sanitizer::sanitize_hex_color( $data['card_bg'], '#ffffff' );
		}
		if ( isset( $data['card_border'] ) ) {
			$this->card_border = Sanitizer::sanitize_hex_color( $data['card_border'], '#e2e8f0' );
		}
		if ( isset( $data['border_radius'] ) ) {
			$this->border_radius = Sanitizer::sanitize_int( $data['border_radius'], 8, 0, 40 );
		}
		if ( isset( $data['text_color'] ) ) {
			$this->text_color = Sanitizer::sanitize_hex_color( $data['text_color'], '#1e293b' );
		}
		if ( isset( $data['date_color'] ) ) {
			$this->date_color = Sanitizer::sanitize_hex_color( $data['date_color'], '#64748b' );
		}
		if ( isset( $data['sorting'] ) && in_array( $data['sorting'], [ 'manual', 'oldest', 'newest' ], true ) ) {
			$this->sorting = $data['sorting'];
		}
		if ( isset( $data['filter_cat'] ) ) {
			$this->filter_cat = ( 'yes' === $data['filter_cat'] || true === $data['filter_cat'] || '1' === (string) $data['filter_cat'] );
		}
		if ( isset( $data['filter_year'] ) ) {
			$this->filter_year = ( 'yes' === $data['filter_year'] || true === $data['filter_year'] || '1' === (string) $data['filter_year'] );
		}
		if ( isset( $data['animation'] ) ) {
			$this->animation = ( 'yes' === $data['animation'] || true === $data['animation'] || '1' === (string) $data['animation'] );
		}
		if ( isset( $data['content_align'] ) && in_array( $data['content_align'], [ 'left', 'center', 'right' ], true ) ) {
			$this->content_align = $data['content_align'];
		}
		if ( isset( $data['max_width'] ) ) {
			$this->max_width = Sanitizer::sanitize_int( $data['max_width'], 1000, 300, 2400 );
		}
		if ( isset( $data['nav_btn_bg'] ) ) {
			$this->nav_btn_bg = Sanitizer::sanitize_hex_color( $data['nav_btn_bg'], '#ffffff' );
		}
		if ( isset( $data['nav_btn_color'] ) ) {
			$this->nav_btn_color = Sanitizer::sanitize_hex_color( $data['nav_btn_color'], '#1e293b' );
		}
		if ( isset( $data['nav_btn_hover_bg'] ) ) {
			$this->nav_btn_hover_bg = Sanitizer::sanitize_hex_color( $data['nav_btn_hover_bg'], '#2563eb' );
		}
		if ( isset( $data['nav_btn_hover_color'] ) ) {
			$this->nav_btn_hover_color = Sanitizer::sanitize_hex_color( $data['nav_btn_hover_color'], '#ffffff' );
		}
		if ( isset( $data['nav_btn_size'] ) ) {
			$this->nav_btn_size = Sanitizer::sanitize_int( $data['nav_btn_size'], 42, 28, 64 );
		}
		if ( isset( $data['nav_btn_shape'] ) && in_array( $data['nav_btn_shape'], [ 'circle', 'rounded', 'square' ], true ) ) {
			$this->nav_btn_shape = $data['nav_btn_shape'];
		}
		if ( isset( $data['nav_btn_position'] ) && in_array( $data['nav_btn_position'], [ 'sides', 'top', 'bottom' ], true ) ) {
			$this->nav_btn_position = $data['nav_btn_position'];
		}
	}

	/**
	 * Convert to array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		return [
			'id'                  => $this->id,
			'title'               => $this->title,
			'status'              => $this->status,
			'layout'              => $this->layout,
			'vertical_style'      => $this->vertical_style,
			'preset'              => $this->preset,
			'accent_color'        => $this->accent_color,
			'line_color'          => $this->line_color,
			'marker_color'        => $this->marker_color,
			'marker_size'         => $this->marker_size,
			'card_bg'             => $this->card_bg,
			'card_border'         => $this->card_border,
			'border_radius'       => $this->border_radius,
			'text_color'          => $this->text_color,
			'date_color'          => $this->date_color,
			'sorting'             => $this->sorting,
			'filter_cat'          => $this->filter_cat ? 'yes' : 'no',
			'filter_year'         => $this->filter_year ? 'yes' : 'no',
			'animation'           => $this->animation ? 'yes' : 'no',
			'content_align'       => $this->content_align,
			'max_width'           => $this->max_width,
			'nav_btn_bg'          => $this->nav_btn_bg,
			'nav_btn_color'       => $this->nav_btn_color,
			'nav_btn_hover_bg'    => $this->nav_btn_hover_bg,
			'nav_btn_hover_color' => $this->nav_btn_hover_color,
			'nav_btn_size'        => $this->nav_btn_size,
			'nav_btn_shape'       => $this->nav_btn_shape,
			'nav_btn_position'    => $this->nav_btn_position,
		];
	}
}
