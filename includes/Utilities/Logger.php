<?php
/**
 * Safe Diagnostic Logger.
 *
 * @package WPCalibrate\TimelineBuilder\Utilities
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Utilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Logger
 */
class Logger {

	/**
	 * Log an error or info message safely.
	 *
	 * @param string $message The message.
	 * @param array  $context Context data (secrets must be stripped).
	 * @return void
	 */
	public static function log( string $message, array $context = [] ): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		// Ensure sensitive data is not logged.
		unset( $context['license_key'], $context['password'], $context['token'] );

		$entry = sprintf(
			'[WPCalibrate Timeline Builder] %s %s',
			$message,
			! empty( $context ) ? wp_json_encode( $context ) : ''
		);

		error_log( $entry ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
