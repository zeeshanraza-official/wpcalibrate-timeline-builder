<?php
/**
 * Post Types Registration.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PostTypes
 */
class PostTypes {

	public const TIMELINE_CPT      = 'wpcal_timeline';
	public const TIMELINE_ITEM_CPT = 'wpcal_timeline_item';

	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'init', [ self::class, 'register_post_types' ], 5 );
		add_action( 'init', [ self::class, 'register_meta' ], 10 );
	}

	/**
	 * Register custom post types.
	 */
	public static function register_post_types(): void {
		// Timeline Post Type.
		register_post_type(
			self::TIMELINE_CPT,
			[
				'labels'             => [
					'name'               => _x( 'Timelines', 'post type general name', 'wpcalibrate-timeline-builder' ),
					'singular_name'      => _x( 'Timeline', 'post type singular name', 'wpcalibrate-timeline-builder' ),
					'menu_name'          => _x( 'Timelines', 'admin menu', 'wpcalibrate-timeline-builder' ),
					'name_admin_bar'     => _x( 'Timeline', 'add new on admin bar', 'wpcalibrate-timeline-builder' ),
					'add_new'            => _x( 'Add New', 'timeline', 'wpcalibrate-timeline-builder' ),
					'add_new_item'       => __( 'Add New Timeline', 'wpcalibrate-timeline-builder' ),
					'new_item'           => __( 'New Timeline', 'wpcalibrate-timeline-builder' ),
					'edit_item'          => __( 'Edit Timeline', 'wpcalibrate-timeline-builder' ),
					'view_item'          => __( 'View Timeline', 'wpcalibrate-timeline-builder' ),
					'all_items'          => __( 'All Timelines', 'wpcalibrate-timeline-builder' ),
					'search_items'       => __( 'Search Timelines', 'wpcalibrate-timeline-builder' ),
					'not_found'          => __( 'No timelines found.', 'wpcalibrate-timeline-builder' ),
					'not_found_in_trash' => __( 'No timelines found in Trash.', 'wpcalibrate-timeline-builder' ),
				],
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'query_var'          => false,
				'rewrite'            => false,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => [ 'title' ],
				'show_in_rest'       => false,
			]
		);

		// Timeline Item Post Type.
		register_post_type(
			self::TIMELINE_ITEM_CPT,
			[
				'labels'             => [
					'name'               => _x( 'Timeline Items', 'post type general name', 'wpcalibrate-timeline-builder' ),
					'singular_name'      => _x( 'Timeline Item', 'post type singular name', 'wpcalibrate-timeline-builder' ),
					'all_items'          => __( 'Timeline Items', 'wpcalibrate-timeline-builder' ),
					'add_new_item'       => __( 'Add New Item', 'wpcalibrate-timeline-builder' ),
					'edit_item'          => __( 'Edit Item', 'wpcalibrate-timeline-builder' ),
					'not_found'          => __( 'No timeline items found.', 'wpcalibrate-timeline-builder' ),
				],
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'query_var'          => false,
				'rewrite'            => false,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
				'show_in_rest'       => false,
			]
		);
	}

	/**
	 * Register post meta for structured timeline configuration.
	 */
	public static function register_meta(): void {
		$timeline_meta_keys = [
			'_wpcal_layout'          => [ 'type' => 'string', 'default' => 'vertical' ],
			'_wpcal_vertical_style'  => [ 'type' => 'string', 'default' => 'both' ],
			'_wpcal_preset'          => [ 'type' => 'string', 'default' => 'cards' ],
			'_wpcal_accent_color'    => [ 'type' => 'string', 'default' => '#2563eb' ],
			'_wpcal_line_color'      => [ 'type' => 'string', 'default' => '#e2e8f0' ],
			'_wpcal_marker_color'    => [ 'type' => 'string', 'default' => '#2563eb' ],
			'_wpcal_marker_size'     => [ 'type' => 'integer', 'default' => 16 ],
			'_wpcal_card_bg'         => [ 'type' => 'string', 'default' => '#ffffff' ],
			'_wpcal_card_border'     => [ 'type' => 'string', 'default' => '#e2e8f0' ],
			'_wpcal_border_radius'   => [ 'type' => 'integer', 'default' => 8 ],
			'_wpcal_text_color'      => [ 'type' => 'string', 'default' => '#1e293b' ],
			'_wpcal_date_color'      => [ 'type' => 'string', 'default' => '#64748b' ],
			'_wpcal_sorting'         => [ 'type' => 'string', 'default' => 'manual' ],
			'_wpcal_filter_cat'      => [ 'type' => 'string', 'default' => 'no' ],
			'_wpcal_filter_year'     => [ 'type' => 'string', 'default' => 'no' ],
			'_wpcal_animation'       => [ 'type' => 'string', 'default' => 'yes' ],
			'_wpcal_content_align'   => [ 'type' => 'string', 'default' => 'left' ],
			'_wpcal_max_width'       => [ 'type' => 'integer', 'default' => 1000 ],
		];

		foreach ( $timeline_meta_keys as $key => $schema ) {
			register_post_meta(
				self::TIMELINE_CPT,
				$key,
				[
					'show_in_rest' => false,
					'single'       => true,
					'type'         => $schema['type'],
					'default'      => $schema['default'],
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}

		$item_meta_keys = [
			'_wpcal_timeline_id'     => [ 'type' => 'integer', 'default' => 0 ],
			'_wpcal_date_type'       => [ 'type' => 'string', 'default' => 'exact' ],
			'_wpcal_date_val'        => [ 'type' => 'string', 'default' => '' ],
			'_wpcal_secondary_label' => [ 'type' => 'string', 'default' => '' ],
			'_wpcal_icon'            => [ 'type' => 'string', 'default' => 'circle' ],
			'_wpcal_button_text'     => [ 'type' => 'string', 'default' => '' ],
			'_wpcal_button_url'      => [ 'type' => 'string', 'default' => '' ],
			'_wpcal_button_target'   => [ 'type' => 'string', 'default' => '_self' ],
			'_wpcal_accent_color'    => [ 'type' => 'string', 'default' => '' ],
		];

		foreach ( $item_meta_keys as $key => $schema ) {
			register_post_meta(
				self::TIMELINE_ITEM_CPT,
				$key,
				[
					'show_in_rest' => false,
					'single'       => true,
					'type'         => $schema['type'],
					'default'      => $schema['default'],
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}
	}
}
