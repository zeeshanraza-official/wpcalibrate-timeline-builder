<?php
/**
 * Database and Schema Migrations.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Migrations
 */
class Migrations {

	public const DB_VERSION = '1.0.0';

	/**
	 * Run migrations idempotently.
	 */
	public static function run(): void {
		$current_db_version = get_option( 'wpcal_timeline_db_version', '0.0.0' );

		if ( version_compare( $current_db_version, self::DB_VERSION, '<' ) ) {
			self::migrate_to_1_0_0();
			update_option( 'wpcal_timeline_db_version', self::DB_VERSION );
		}
	}

	/**
	 * Initial 1.0.0 migration.
	 */
	private static function migrate_to_1_0_0(): void {
		// Ensure default global settings exist without overwriting user choices.
		$default_settings = [
			'default_layout'       => 'vertical',
			'default_preset'       => 'cards',
			'default_accent_color' => '#2563eb',
			'default_animation'    => 'yes',
			'asset_loading'        => 'conditional',
			'data_retention'       => 'preserve',
		];

		$existing = get_option( 'wpcal_timeline_settings', [] );
		if ( ! is_array( $existing ) || empty( $existing ) ) {
			add_option( 'wpcal_timeline_settings', $default_settings );
		}
	}
}
