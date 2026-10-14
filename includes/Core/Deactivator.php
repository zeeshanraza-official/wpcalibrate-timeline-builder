<?php
/**
 * Plugin Deactivation Handler.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 */
class Deactivator {

	/**
	 * Run deactivation routine.
	 * Preserves all user timelines, items, and settings.
	 */
	public static function deactivate(): void {
		global $wpdb;

		// Clear temporary transients created by timeline rendering.
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wpcal_timeline_%' OR option_name LIKE '_transient_timeout_wpcal_timeline_%'" );
	}
}
