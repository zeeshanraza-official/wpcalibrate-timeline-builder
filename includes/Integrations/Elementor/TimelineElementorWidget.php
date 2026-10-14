<?php
/**
 * Elementor Widget for WPCalibrate Timeline.
 *
 * @package WPCalibrate\TimelineBuilder\Integrations\Elementor
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Integrations\Elementor;

use WPCalibrate\TimelineBuilder\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineElementorWidget
 */
class TimelineElementorWidget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'wpcalibrate_timeline';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'WPCalibrate Timeline', 'wpcalibrate-timeline-builder' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-time-line';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories(): array {
		return [ 'general' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Timeline Settings', 'wpcalibrate-timeline-builder' ),
			]
		);

		// Build options list of published timelines.
		$repository = Plugin::get_instance()->get_repository();
		$timelines  = $repository->get_timelines( [ 'post_status' => 'publish' ] );
		$options    = [ '0' => __( '-- Select a Timeline --', 'wpcalibrate-timeline-builder' ) ];

		foreach ( $timelines as $tl ) {
			$options[ (string) $tl->id ] = ( $tl->title ?: __( 'Untitled Timeline', 'wpcalibrate-timeline-builder' ) ) . ' (#' . $tl->id . ')';
		}

		$this->add_control(
			'timeline_id',
			[
				'label'   => __( 'Select Timeline', 'wpcalibrate-timeline-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $options,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout Override (Optional)', 'wpcalibrate-timeline-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''           => __( 'Default from timeline', 'wpcalibrate-timeline-builder' ),
					'vertical'   => __( 'Vertical', 'wpcalibrate-timeline-builder' ),
					'horizontal' => __( 'Horizontal', 'wpcalibrate-timeline-builder' ),
				],
			]
		);

		$this->add_control(
			'preset',
			[
				'label'   => __( 'Preset Override (Optional)', 'wpcalibrate-timeline-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => __( 'Default from timeline', 'wpcalibrate-timeline-builder' ),
					'clean'   => __( 'Clean', 'wpcalibrate-timeline-builder' ),
					'cards'   => __( 'Cards', 'wpcalibrate-timeline-builder' ),
					'minimal' => __( 'Minimal', 'wpcalibrate-timeline-builder' ),
					'modern'  => __( 'Modern', 'wpcalibrate-timeline-builder' ),
					'compact' => __( 'Compact', 'wpcalibrate-timeline-builder' ),
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$timeline_id = (int) ( $settings['timeline_id'] ?? 0 );

		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( $timeline_id <= 0 ) {
			if ( $is_editor ) {
				echo '<div class="elementor-alert elementor-alert-info"><p>' . esc_html__( 'Please select a timeline in the widget settings.', 'wpcalibrate-timeline-builder' ) . '</p></div>';
			}
			return;
		}

		$overrides = [];
		if ( ! empty( $settings['layout'] ) ) {
			$overrides['layout'] = sanitize_key( $settings['layout'] );
		}
		if ( ! empty( $settings['preset'] ) ) {
			$overrides['preset'] = sanitize_key( $settings['preset'] );
		}

		$renderer = Plugin::get_instance()->get_renderer();
		echo $renderer->render( $timeline_id, $overrides, $is_editor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
