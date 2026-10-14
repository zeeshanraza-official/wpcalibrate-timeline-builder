<?php
/**
 * Plugin Activation Handler.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 */
class Activator {

	/**
	 * Run activation routine.
	 */
	public static function activate(): void {
		// Register post types and taxonomies so they exist immediately.
		PostTypes::register_post_types();
		Taxonomies::register_taxonomy();

		// Add custom capabilities.
		Capabilities::add_capabilities();

		// Run schema migrations.
		Migrations::run();

		// Store active plugin version.
		update_option( 'wpcal_timeline_version', WPCAL_TIMELINE_VERSION );
	}
}
