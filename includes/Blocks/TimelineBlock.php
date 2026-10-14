<?php
/**
 * Gutenberg Block Registration.
 *
 * @package WPCalibrate\TimelineBuilder\Blocks
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Blocks;

use WPCalibrate\TimelineBuilder\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineBlock
 */
class TimelineBlock {

	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'init', [ self::class, 'register_block' ] );
		add_action( 'enqueue_block_editor_assets', [ self::class, 'localize_editor_data' ] );
	}

	/**
	 * Register the Gutenberg dynamic block.
	 */
	public static function register_block(): void {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			WPCAL_TIMELINE_DIR . 'blocks/timeline',
			[
				'render_callback' => [ self::class, 'render' ],
			]
		);
	}

	/**
	 * Localize list of timelines for block inspector controls.
	 */
	public static function localize_editor_data(): void {
		$repository = Plugin::get_instance()->get_repository();
		$timelines  = $repository->get_timelines( [ 'post_status' => 'publish' ] );

		$list = [];
		foreach ( $timelines as $tl ) {
			$list[] = [
				'id'    => $tl->id,
				'title' => $tl->title ?: sprintf( __( 'Timeline #%d', 'wpcalibrate-timeline-builder' ), $tl->id ),
			];
		}

		wp_localize_script(
			'wpcalibrate-timeline-editor-script',
			'wpcalBlockData',
			[
				'timelines' => $list,
			]
		);
	}

	/**
	 * Server-side render callback for block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Rendered HTML.
	 */
	public static function render( array $attributes ): string {
		$timeline_id = (int) ( $attributes['timelineId'] ?? 0 );
		if ( $timeline_id <= 0 ) {
			if ( is_admin() ) {
				return '<div class="wpcal-block-placeholder"><p>' . esc_html__( 'Please select a timeline from the block settings in the sidebar.', 'wpcalibrate-timeline-builder' ) . '</p></div>';
			}
			return '';
		}

		$overrides = [];
		if ( ! empty( $attributes['layout'] ) ) {
			$overrides['layout'] = sanitize_key( $attributes['layout'] );
		}
		if ( ! empty( $attributes['preset'] ) ) {
			$overrides['preset'] = sanitize_key( $attributes['preset'] );
		}

		$is_preview = is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		$renderer   = Plugin::get_instance()->get_renderer();

		return $renderer->render( $timeline_id, $overrides, $is_preview );
	}
}
