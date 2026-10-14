<?php
/**
 * License Manager Implementation.
 *
 * @package WPCalibrate\TimelineBuilder\Licensing
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Licensing;

use WPCalibrate\TimelineBuilder\Utilities\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicenseManager
 */
class LicenseManager implements LicenseManagerInterface {

	public const OPTION_NAME = 'wpcal_timeline_license';

	/**
	 * Check if remote licensing service is configured.
	 * Returns false because endpoint/provider details have not been supplied.
	 *
	 * @return bool
	 */
	public function is_configured(): bool {
		return false;
	}

	/**
	 * Get current status.
	 *
	 * @return string
	 */
	public function get_status(): string {
		if ( ! $this->is_configured() ) {
			return 'not_configured';
		}
		$data = $this->get_license_data();
		return $data['status'] ?? 'inactive';
	}

	/**
	 * Get masked key for UI display.
	 * Never exposes the full raw key.
	 *
	 * @return string
	 */
	public function get_masked_key(): string {
		$data = $this->get_license_data();
		$raw  = $data['key'] ?? '';
		if ( empty( $raw ) ) {
			return '';
		}
		return Sanitizer::mask_license_key( $raw );
	}

	/**
	 * Save license key.
	 *
	 * @param string $key Raw key.
	 * @return bool
	 */
	public function save_key( string $key ): bool {
		$data        = $this->get_license_data();
		$data['key'] = sanitize_text_field( trim( $key ) );
		return update_option( self::OPTION_NAME, $data );
	}

	/**
	 * Activate license.
	 * Explicitly fails gracefully because remote service is unconfigured.
	 *
	 * @return array{success: bool, message: string}
	 */
	public function activate(): array {
		if ( ! $this->is_configured() ) {
			return [
				'success' => false,
				'message' => __( 'Licensing endpoint is not configured in this build. Remote activation is disabled.', 'wpcalibrate-timeline-builder' ),
			];
		}

		return [
			'success' => false,
			'message' => __( 'License activation failed.', 'wpcalibrate-timeline-builder' ),
		];
	}

	/**
	 * Deactivate license.
	 *
	 * @return array{success: bool, message: string}
	 */
	public function deactivate(): array {
		if ( ! $this->is_configured() ) {
			return [
				'success' => false,
				'message' => __( 'Licensing endpoint is not configured in this build. Remote deactivation is disabled.', 'wpcalibrate-timeline-builder' ),
			];
		}

		return [
			'success' => false,
			'message' => __( 'License deactivation failed.', 'wpcalibrate-timeline-builder' ),
		];
	}

	/**
	 * Get expiration date.
	 *
	 * @return string
	 */
	public function get_expiry_date(): string {
		$data = $this->get_license_data();
		return ! empty( $data['expires'] ) ? Sanitizer::sanitize_text( $data['expires'] ) : __( 'N/A', 'wpcalibrate-timeline-builder' );
	}

	/**
	 * Get renewal URL.
	 *
	 * @return string
	 */
	public function get_renewal_url(): string {
		return 'https://marketplace.wpcalibrate.com/';
	}

	/**
	 * Internal helper to fetch stored license options.
	 *
	 * @return array
	 */
	private function get_license_data(): array {
		$data = get_option( self::OPTION_NAME, [] );
		return is_array( $data ) ? $data : [];
	}
}
