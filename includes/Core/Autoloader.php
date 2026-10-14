<?php
/**
 * PSR-4 Autoloader for WPCalibrate Timeline Builder
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Namespace prefix.
	 *
	 * @var string
	 */
	private const PREFIX = 'WPCalibrate\\TimelineBuilder\\';

	/**
	 * Register the autoloader.
	 *
	 * @param string $base_dir Base directory path for classes (typically includes/).
	 * @return void
	 */
	public static function register( string $base_dir ): void {
		$base_dir = rtrim( $base_dir, '/\\' ) . DIRECTORY_SEPARATOR;

		spl_autoload_register(
			static function ( string $class ) use ( $base_dir ): void {
				$len = strlen( self::PREFIX );
				if ( strncmp( self::PREFIX, $class, $len ) !== 0 ) {
					return;
				}

				$relative_class = substr( $class, $len );
				$file           = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		);
	}
}
