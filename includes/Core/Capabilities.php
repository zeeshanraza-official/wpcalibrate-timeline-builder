<?php
/**
 * Capability management.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Capabilities
 */
class Capabilities {

	public const MANAGE_TIMELINES = 'manage_wpcal_timelines';

	/**
	 * Initialize capabilities.
	 */
	public static function init(): void {
		// Map meta capabilities if needed.
	}

	/**
	 * Add default capabilities on activation.
	 */
	public static function add_capabilities(): void {
		$admin_role = get_role( 'administrator' );
		if ( $admin_role && ! $admin_role->has_cap( self::MANAGE_TIMELINES ) ) {
			$admin_role->add_cap( self::MANAGE_TIMELINES );
		}
	}

	/**
	 * Remove capabilities on uninstall.
	 */
	public static function remove_capabilities(): void {
		$roles = [ 'administrator', 'editor' ];
		foreach ( $roles as $role_slug ) {
			$role = get_role( $role_slug );
			if ( $role && $role->has_cap( self::MANAGE_TIMELINES ) ) {
				$role->remove_cap( self::MANAGE_TIMELINES );
			}
		}
	}

	/**
	 * Check if current user can manage timelines.
	 *
	 * @return bool
	 */
	public static function current_user_can_manage(): bool {
		return current_user_can( self::MANAGE_TIMELINES ) || current_user_can( 'manage_options' );
	}
}
