<?php
/**
 * License Manager Interface.
 *
 * @package WPCalibrate\TimelineBuilder\Licensing
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface LicenseManagerInterface
 */
interface LicenseManagerInterface {

	/**
	 * Check if remote licensing service is configured.
	 *
	 * @return bool
	 */
	public function is_configured(): bool;

	/**
	 * Get current license status.
	 *
	 * @return string (e.g., 'not_configured', 'unlicensed', 'active', 'expired')
	 */
	public function get_status(): string;

	/**
	 * Get masked license key for display.
	 *
	 * @return string
	 */
	public function get_masked_key(): string;

	/**
	 * Save license key.
	 *
	 * @param string $key Raw key.
	 * @return bool
	 */
	public function save_key( string $key ): bool;

	/**
	 * Activate license.
	 *
	 * @return array{success: bool, message: string}
	 */
	public function activate(): array;

	/**
	 * Deactivate license.
	 *
	 * @return array{success: bool, message: string}
	 */
	public function deactivate(): array;

	/**
	 * Get expiration date string if available.
	 *
	 * @return string
	 */
	public function get_expiry_date(): string;

	/**
	 * Get renewal URL if available.
	 *
	 * @return string
	 */
	public function get_renewal_url(): string;
}
