<?php
/**
 * Plugin Name: WPCalibrate Timeline Builder
 * Plugin URI: https://marketplace.wpcalibrate.com/
 * Description: Lightweight, responsive WordPress timeline and roadmap builder. Display milestones, company history, and process steps using vertical or horizontal layouts.
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 8.2
 * Tested up to: 7.1.3
 * Author: WPCalibrate
 * Author URI: https://wpcalibrate.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpcalibrate-timeline-builder
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/zeeshanraza-official/wpcalibrate-timeline-builder
 * Primary Branch: main
 *
 * @package WPCalibrate\TimelineBuilder
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Constants.
define( 'WPCAL_TIMELINE_VERSION', '1.0.0' );
define( 'WPCAL_TIMELINE_FILE', __FILE__ );
define( 'WPCAL_TIMELINE_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPCAL_TIMELINE_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCAL_TIMELINE_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCAL_TIMELINE_MIN_PHP', '8.2.0' );
define( 'WPCAL_TIMELINE_MIN_WP', '6.2' );

// Safety check for PHP version.
if ( version_compare( PHP_VERSION, WPCAL_TIMELINE_MIN_PHP, '<' ) ) {
	add_action(
		'admin_notices',
		static function (): void {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: Minimum PHP version, 2: Current PHP version */
						__( 'WPCalibrate Timeline Builder requires PHP %1$s or higher. Your server is running PHP %2$s. Please upgrade your PHP version.', 'wpcalibrate-timeline-builder' ),
						WPCAL_TIMELINE_MIN_PHP,
						PHP_VERSION
					)
				)
			);
		}
	);
	return;
}

// Register autoloader.
require_once WPCAL_TIMELINE_DIR . 'includes/Core/Autoloader.php';
\WPCalibrate\TimelineBuilder\Core\Autoloader::register( WPCAL_TIMELINE_DIR . 'includes' );

// Activation and deactivation hooks.
register_activation_hook( __FILE__, [ \WPCalibrate\TimelineBuilder\Core\Activator::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \WPCalibrate\TimelineBuilder\Core\Deactivator::class, 'deactivate' ] );

/**
 * Bootstrap the plugin.
 */
function wpcal_timeline_init(): \WPCalibrate\TimelineBuilder\Plugin {
	return \WPCalibrate\TimelineBuilder\Plugin::get_instance();
}

// Initialize on plugins_loaded.
add_action( 'plugins_loaded', 'wpcal_timeline_init' );
