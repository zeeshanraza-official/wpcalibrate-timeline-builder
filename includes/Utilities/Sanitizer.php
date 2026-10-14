<?php
/**
 * Data Sanitizer and Validator.
 *
 * @package WPCalibrate\TimelineBuilder\Utilities
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Utilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sanitizer
 */
class Sanitizer {

	/**
	 * Sanitize generic text.
	 *
	 * @param mixed $value Input.
	 * @return string
	 */
	public static function sanitize_text( mixed $value ): string {
		return sanitize_text_field( (string) ( $value ?? '' ) );
	}

	/**
	 * Sanitize rich text/HTML for descriptions using wp_kses_post.
	 *
	 * @param mixed $value Input.
	 * @return string
	 */
	public static function sanitize_html( mixed $value ): string {
		return wp_kses_post( (string) ( $value ?? '' ) );
	}

	/**
	 * Sanitize hex color code with fallback.
	 *
	 * @param mixed  $color Color string.
	 * @param string $default Fallback.
	 * @return string
	 */
	public static function sanitize_hex_color( mixed $color, string $default = '' ): string {
		$sanitized = sanitize_hex_color( (string) ( $color ?? '' ) );
		return ! empty( $sanitized ) ? $sanitized : $default;
	}

	/**
	 * Sanitize and validate URL, preventing unsafe protocols like javascript:.
	 *
	 * @param mixed $url Input URL.
	 * @return string
	 */
	public static function sanitize_url( mixed $url ): string {
		$url = trim( (string) ( $url ?? '' ) );
		if ( empty( $url ) ) {
			return '';
		}
		$escaped = esc_url_raw( $url, [ 'http', 'https', 'mailto', 'tel' ] );
		return $escaped;
	}

	/**
	 * Sanitize integer with optional bounds.
	 *
	 * @param mixed    $value Input.
	 * @param int      $default Fallback.
	 * @param int|null $min Minimum.
	 * @param int|null $max Maximum.
	 * @return int
	 */
	public static function sanitize_int( mixed $value, int $default = 0, ?int $min = null, ?int $max = null ): int {
		if ( ! is_numeric( $value ) ) {
			return $default;
		}
		$int_val = (int) $value;
		if ( null !== $min && $int_val < $min ) {
			return $min;
		}
		if ( null !== $max && $int_val > $max ) {
			return $max;
		}
		return $int_val;
	}

	/**
	 * Mask a license key securely for display.
	 *
	 * @param string $key Raw key.
	 * @return string
	 */
	public static function mask_license_key( string $key ): string {
		$key = trim( $key );
		if ( empty( $key ) ) {
			return '';
		}
		$len = strlen( $key );
		if ( $len <= 6 ) {
			return str_repeat( '*', $len );
		}
		$visible_prefix = substr( $key, 0, 4 );
		$visible_suffix = substr( $key, -4 );
		return $visible_prefix . str_repeat( '*', max( 4, $len - 8 ) ) . $visible_suffix;
	}
}
