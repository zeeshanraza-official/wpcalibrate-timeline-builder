<?php
/**
 * Asset Manager for Conditional Enqueuing.
 *
 * @package WPCalibrate\TimelineBuilder\Rendering
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Rendering;

use WPCalibrate\TimelineBuilder\Core\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AssetManager
 */
class AssetManager {

	/**
	 * Tracks if frontend assets were enqueued.
	 *
	 * @var bool
	 */
	private static bool $frontend_enqueued = false;

	/**
	 * Initialize hooks.
	 */
	public static function init(): void {
		add_action( 'wp_enqueue_scripts', [ self::class, 'register_frontend_assets' ] );
		add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_admin_assets' ] );
	}

	/**
	 * Register frontend scripts and styles without enqueuing sitewide.
	 */
	public static function register_frontend_assets(): void {
		wp_register_style(
			'wpcal-timeline-frontend',
			WPCAL_TIMELINE_URL . 'assets/css/frontend/timeline.css',
			[],
			WPCAL_TIMELINE_VERSION
		);

		wp_register_script(
			'wpcal-timeline-frontend',
			WPCAL_TIMELINE_URL . 'assets/js/frontend/timeline.js',
			[],
			WPCAL_TIMELINE_VERSION,
			true
		);

		wp_localize_script(
			'wpcal-timeline-frontend',
			'wpcalTimelineData',
			[
				'i18n' => [
					'all'        => __( 'All', 'wpcalibrate-timeline-builder' ),
					'prev'       => __( 'Previous', 'wpcalibrate-timeline-builder' ),
					'next'       => __( 'Next', 'wpcalibrate-timeline-builder' ),
					'empty'      => __( 'No items found matching the selected filter.', 'wpcalibrate-timeline-builder' ),
				],
			]
		);
	}

	/**
	 * Enqueue frontend assets when a timeline is rendered.
	 */
	public static function enqueue_frontend_assets(): void {
		if ( self::$frontend_enqueued ) {
			return;
		}

		self::register_frontend_assets();
		wp_enqueue_style( 'wpcal-timeline-frontend' );
		wp_enqueue_script( 'wpcal-timeline-frontend' );
		self::$frontend_enqueued = true;
	}

	/**
	 * Enqueue admin scripts and styles only on plugin pages.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public static function enqueue_admin_assets( string $hook_suffix ): void {
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Only enqueue on WPCalibrate Timeline Builder pages.
		$is_plugin_page = (
			false !== strpos( $hook_suffix, 'wpcal' ) ||
			false !== strpos( $hook_suffix, 'wpcalibrate' ) ||
			false !== strpos( $page, 'wpcal' ) ||
			false !== strpos( $page, 'wpcalibrate' )
		);

		if ( ! $is_plugin_page ) {
			return;
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			return;
		}

		// Enqueue media library scripts for item image picker.
		wp_enqueue_media();

		$css_file = WPCAL_TIMELINE_DIR . 'assets/css/admin/admin.css';
		$css_ver  = WPCAL_TIMELINE_VERSION . ( file_exists( $css_file ) ? '.' . filemtime( $css_file ) : '' );

		wp_enqueue_style(
			'wpcal-timeline-admin',
			WPCAL_TIMELINE_URL . 'assets/css/admin/admin.css',
			[ 'wp-color-picker' ],
			$css_ver
		);

		$js_file = WPCAL_TIMELINE_DIR . 'assets/js/admin/admin.js';
		$js_ver  = WPCAL_TIMELINE_VERSION . ( file_exists( $js_file ) ? '.' . filemtime( $js_file ) : '' );

		wp_enqueue_script(
			'wpcal-timeline-admin',
			WPCAL_TIMELINE_URL . 'assets/js/admin/admin.js',
			[ 'jquery', 'wp-color-picker' ],
			$js_ver,
			true
		);

		wp_localize_script(
			'wpcal-timeline-admin',
			'wpcalAdminData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wpcal_timeline_admin_nonce' ),
				'i18n'    => [
					'confirmDelete'     => __( 'Are you sure you want to delete this item?', 'wpcalibrate-timeline-builder' ),
					'confirmDeleteTL'   => __( 'Are you sure you want to delete this entire timeline and all its items?', 'wpcalibrate-timeline-builder' ),
					'copySuccess'       => __( 'Shortcode copied to clipboard!', 'wpcalibrate-timeline-builder' ),
					'saving'            => __( 'Saving...', 'wpcalibrate-timeline-builder' ),
					'saved'             => __( 'Saved successfully!', 'wpcalibrate-timeline-builder' ),
					'error'             => __( 'An error occurred. Please try again.', 'wpcalibrate-timeline-builder' ),
					'selectImage'       => __( 'Select Item Image', 'wpcalibrate-timeline-builder' ),
					'useImage'          => __( 'Use This Image', 'wpcalibrate-timeline-builder' ),
				],
			]
		);
	}
}
