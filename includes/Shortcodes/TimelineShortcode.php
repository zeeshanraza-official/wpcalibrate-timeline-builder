<?php
/**
 * Shortcode Handler for [wpcalibrate_timeline].
 *
 * @package WPCalibrate\TimelineBuilder\Shortcodes
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Shortcodes;

use WPCalibrate\TimelineBuilder\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineShortcode
 */
class TimelineShortcode {

	public const TAG = 'wpcalibrate_timeline';

	/**
	 * Register shortcode.
	 */
	public static function init(): void {
		add_shortcode( self::TAG, [ self::class, 'render' ] );
	}

	/**
	 * Render shortcode output safely.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render( $atts ): string {
		$atts = shortcode_atts(
			[
				'id'     => 0,
				'layout' => '',
				'preset' => '',
				'filter' => '',
			],
			$atts,
			self::TAG
		);

		$timeline_id = (int) $atts['id'];
		if ( $timeline_id <= 0 ) {
			return '';
		}

		$overrides = [];
		if ( ! empty( $atts['layout'] ) && in_array( $atts['layout'], [ 'vertical', 'horizontal' ], true ) ) {
			$overrides['layout'] = sanitize_key( $atts['layout'] );
		}
		if ( ! empty( $atts['preset'] ) && in_array( $atts['preset'], [ 'clean', 'cards', 'minimal', 'modern', 'compact' ], true ) ) {
			$overrides['preset'] = sanitize_key( $atts['preset'] );
		}
		if ( ! empty( $atts['filter'] ) && in_array( $atts['filter'], [ 'all', 'none', 'category', 'year' ], true ) ) {
			$overrides['filter'] = sanitize_key( $atts['filter'] );
		}

		$renderer = Plugin::get_instance()->get_renderer();
		return $renderer->render( $timeline_id, $overrides, false );
	}
}
