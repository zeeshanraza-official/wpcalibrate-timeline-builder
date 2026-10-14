<?php
/**
 * Admin Menu Manager for WPCalibrate parent menu reuse.
 *
 * @package WPCalibrate\TimelineBuilder\Admin
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Admin;

use WPCalibrate\TimelineBuilder\Core\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MenuManager
 */
class MenuManager {

	public const PARENT_SLUG = 'wpcalibrate';
	public const MAIN_PAGE   = 'wpcal-timeline-builder';

	/**
	 * Register admin menu hooks.
	 */
	public static function init(): void {
		add_action( 'admin_menu', [ self::class, 'register_menus' ], 20 );
		add_action( 'admin_head', [ self::class, 'admin_menu_styles' ] );
	}

	/**
	 * Register or reuse top-level WPCalibrate menu and attach Timeline Builder submenus.
	 */
	public static function register_menus(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			return;
		}

		global $menu;
		$parent_exists = false;

		if ( is_array( $menu ) ) {
			foreach ( $menu as $item ) {
				if ( isset( $item[2] ) && self::PARENT_SLUG === $item[2] ) {
					$parent_exists = true;
					break;
				}
			}
		}

		$brand_icon = WPCAL_TIMELINE_URL . 'branding/icon-white.png';

		// Create top-level WPCalibrate menu if it doesn't already exist.
		if ( ! $parent_exists ) {
			add_menu_page(
				'WPCalibrate',
				'WPCalibrate',
				Capabilities::MANAGE_TIMELINES,
				self::PARENT_SLUG,
				[ TimelineListPage::class, 'render' ],
				'none',
				59
			);
		}

		// Add "Timeline Builder" submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Timeline Builder', 'wpcalibrate-timeline-builder' ),
			__( 'Timeline Builder', 'wpcalibrate-timeline-builder' ),
			Capabilities::MANAGE_TIMELINES,
			self::MAIN_PAGE,
			[ self::class, 'route_main_page' ]
		);

		// Overview submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Timeline Overview', 'wpcalibrate-timeline-builder' ),
			__( 'Overview', 'wpcalibrate-timeline-builder' ),
			Capabilities::MANAGE_TIMELINES,
			'wpcal-timeline-builder-overview',
			[ OverviewPage::class, 'render' ]
		);

		// Settings submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Timeline Settings', 'wpcalibrate-timeline-builder' ),
			__( 'Timeline Settings', 'wpcalibrate-timeline-builder' ),
			Capabilities::MANAGE_TIMELINES,
			'wpcal-timeline-builder-settings',
			[ SettingsPage::class, 'render' ]
		);

		// License submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Timeline License', 'wpcalibrate-timeline-builder' ),
			__( 'Timeline License', 'wpcalibrate-timeline-builder' ),
			Capabilities::MANAGE_TIMELINES,
			'wpcal-timeline-builder-license',
			[ LicensePage::class, 'render' ]
		);

		// Support submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Timeline Support', 'wpcalibrate-timeline-builder' ),
			__( 'Timeline Support', 'wpcalibrate-timeline-builder' ),
			Capabilities::MANAGE_TIMELINES,
			'wpcal-timeline-builder-support',
			[ SupportPage::class, 'render' ]
		);
	}

	/**
	 * Route main page between list and editor.
	 */
	public static function route_main_page(): void {
		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'new' === $action || 'edit' === $action ) {
			TimelineEditorPage::render();
		} else {
			TimelineListPage::render();
		}
	}

	/**
	 * Output CSS in admin head to ensure brand icon inherits WordPress admin dashboard colors.
	 */
	public static function admin_menu_styles(): void {
		$icon_url = esc_url( WPCAL_TIMELINE_URL . 'branding/icon-mask.svg' );
		?>
		<style>
			#adminmenu .toplevel_page_wpcalibrate .wp-menu-image {
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
			}
			#adminmenu .toplevel_page_wpcalibrate .wp-menu-image:before {
				content: "" !important;
				display: block !important;
				width: 20px !important;
				height: 20px !important;
				padding: 0 !important;
				margin: 0 !important;
				background-color: #a7aaad;
				background-color: currentColor;
				-webkit-mask-image: url('<?php echo $icon_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>') !important;
				mask-image: url('<?php echo $icon_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>') !important;
				-webkit-mask-size: contain !important;
				mask-size: contain !important;
				-webkit-mask-repeat: no-repeat !important;
				mask-repeat: no-repeat !important;
				-webkit-mask-position: center center !important;
				mask-position: center center !important;
				transition: background-color 0.15s ease, color 0.15s ease;
			}
			#adminmenu .toplevel_page_wpcalibrate:hover .wp-menu-image:before,
			#adminmenu .toplevel_page_wpcalibrate.opensub .wp-menu-image:before,
			#adminmenu .toplevel_page_wpcalibrate > a:hover .wp-menu-image:before {
				background-color: #72aee6;
			}
			#adminmenu .toplevel_page_wpcalibrate.wp-has-current-submenu .wp-menu-image:before,
			#adminmenu .toplevel_page_wpcalibrate.wp-menu-open .wp-menu-image:before,
			#adminmenu .toplevel_page_wpcalibrate.current .wp-menu-image:before {
				background-color: #ffffff;
			}
			#adminmenu .toplevel_page_wpcalibrate .wp-menu-image img {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * Render navigation header tabs for WPCalibrate Timeline Builder screens.
	 *
	 * @param string $active_tab Active tab identifier.
	 */
	public static function render_header_tabs( string $active_tab ): void {
		$tabs = [
			'overview'  => [
				'label' => __( 'Overview', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder-overview' ),
			],
			'timelines' => [
				'label' => __( 'Timelines', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder' ),
			],
			'new'       => [
				'label' => __( 'Add New Timeline', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder&action=new' ),
			],
			'settings'  => [
				'label' => __( 'Settings', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder-settings' ),
			],
			'license'   => [
				'label' => __( 'License', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder-license' ),
			],
			'support'   => [
				'label' => __( 'Support', 'wpcalibrate-timeline-builder' ),
				'url'   => admin_url( 'admin.php?page=wpcal-timeline-builder-support' ),
			],
		];
		?>
		<div class="wpcal-admin-header">
			<div class="wpcal-admin-brand">
				<img src="<?php echo esc_url( WPCAL_TIMELINE_URL . 'branding/icon-dark.png' ); ?>" alt="WPCalibrate" class="wpcal-brand-logo" />
				<div>
					<h1 class="wpcal-admin-title"><?php esc_html_e( 'Timeline Builder', 'wpcalibrate-timeline-builder' ); ?></h1>
					<span class="wpcal-version-badge"><?php echo esc_html( 'v' . WPCAL_TIMELINE_VERSION ); ?></span>
				</div>
			</div>
			<nav class="wpcal-admin-nav" aria-label="<?php esc_attr_e( 'Timeline Builder Navigation', 'wpcalibrate-timeline-builder' ); ?>">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<a href="<?php echo esc_url( $tab['url'] ); ?>" class="wpcal-nav-link <?php echo ( $active_tab === $key ) ? 'is-active' : ''; ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
		<?php
	}
}
