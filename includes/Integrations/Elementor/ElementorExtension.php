<?php
/**
 * Safe Elementor Extension Integration.
 *
 * @package WPCalibrate\TimelineBuilder\Integrations\Elementor
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Integrations\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ElementorExtension
 */
class ElementorExtension {

	/**
	 * Initialize Elementor integration safely.
	 */
	public static function init(): void {
		add_action( 'plugins_loaded', [ self::class, 'setup' ], 20 );
	}

	/**
	 * Setup hooks only if Elementor is active.
	 */
	public static function setup(): void {
		if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		// Supported modern Elementor widget registration hook.
		add_action( 'elementor/widgets/register', [ self::class, 'register_widgets' ] );
	}

	/**
	 * Register WPCalibrate Timeline widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager instance.
	 */
	public static function register_widgets( $widgets_manager ): void {
		require_once __DIR__ . '/TimelineElementorWidget.php';
		$widgets_manager->register( new TimelineElementorWidget() );
	}
}
