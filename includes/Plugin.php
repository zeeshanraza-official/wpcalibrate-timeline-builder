<?php
/**
 * Main Plugin Orchestrator.
 *
 * @package WPCalibrate\TimelineBuilder
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder;

use WPCalibrate\TimelineBuilder\Core\PostTypes;
use WPCalibrate\TimelineBuilder\Core\Taxonomies;
use WPCalibrate\TimelineBuilder\Core\Capabilities;
use WPCalibrate\TimelineBuilder\Timeline\TimelineRepository;
use WPCalibrate\TimelineBuilder\Timeline\TimelineDuplicator;
use WPCalibrate\TimelineBuilder\Rendering\TimelineRenderer;
use WPCalibrate\TimelineBuilder\Rendering\AssetManager;
use WPCalibrate\TimelineBuilder\Admin\MenuManager;
use WPCalibrate\TimelineBuilder\Admin\AdminAjaxHandler;
use WPCalibrate\TimelineBuilder\Shortcodes\TimelineShortcode;
use WPCalibrate\TimelineBuilder\Blocks\TimelineBlock;
use WPCalibrate\TimelineBuilder\Integrations\Elementor\ElementorExtension;
use WPCalibrate\TimelineBuilder\Licensing\LicenseManagerInterface;
use WPCalibrate\TimelineBuilder\Licensing\LicenseManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Repository.
	 *
	 * @var TimelineRepository
	 */
	private TimelineRepository $repository;

	/**
	 * Central Renderer.
	 *
	 * @var TimelineRenderer
	 */
	private TimelineRenderer $renderer;

	/**
	 * Duplicator.
	 *
	 * @var TimelineDuplicator
	 */
	private TimelineDuplicator $duplicator;

	/**
	 * Licensing Manager.
	 *
	 * @var LicenseManagerInterface
	 */
	private LicenseManagerInterface $license_manager;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_services();
		$this->register_hooks();
	}

	/**
	 * Initialize internal services.
	 */
	private function init_services(): void {
		$this->repository      = new TimelineRepository();
		$this->renderer        = new TimelineRenderer( $this->repository );
		$this->duplicator      = new TimelineDuplicator( $this->repository );
		$this->license_manager = new LicenseManager();
	}

	/**
	 * Register WordPress hooks.
	 */
	private function register_hooks(): void {
		// Core registrations.
		PostTypes::init();
		Taxonomies::init();
		Capabilities::init();

		// Asset loading.
		AssetManager::init();

		// Admin experience.
		if ( is_admin() ) {
			MenuManager::init();
			AdminAjaxHandler::init();
		}

		// Frontend display channels.
		TimelineShortcode::init();
		TimelineBlock::init();
		ElementorExtension::init();

		// Localization.
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'wpcalibrate-timeline-builder',
			false,
			dirname( WPCAL_TIMELINE_BASENAME ) . '/languages'
		);
	}

	/**
	 * Get timeline repository.
	 *
	 * @return TimelineRepository
	 */
	public function get_repository(): TimelineRepository {
		return $this->repository;
	}

	/**
	 * Get central renderer.
	 *
	 * @return TimelineRenderer
	 */
	public function get_renderer(): TimelineRenderer {
		return $this->renderer;
	}

	/**
	 * Get timeline duplicator.
	 *
	 * @return TimelineDuplicator
	 */
	public function get_duplicator(): TimelineDuplicator {
		return $this->duplicator;
	}

	/**
	 * Get license manager.
	 *
	 * @return LicenseManagerInterface
	 */
	public function get_license_manager(): LicenseManagerInterface {
		return $this->license_manager;
	}
}
