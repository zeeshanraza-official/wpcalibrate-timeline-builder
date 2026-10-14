<?php
/**
 * Taxonomy Registration.
 *
 * @package WPCalibrate\TimelineBuilder\Core
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Taxonomies
 */
class Taxonomies {

	public const TAXONOMY = 'wpcal_timeline_cat';

	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'init', [ self::class, 'register_taxonomy' ], 6 );
	}

	/**
	 * Register timeline categories taxonomy.
	 */
	public static function register_taxonomy(): void {
		register_taxonomy(
			self::TAXONOMY,
			PostTypes::TIMELINE_ITEM_CPT,
			[
				'labels'            => [
					'name'          => _x( 'Timeline Categories', 'taxonomy general name', 'wpcalibrate-timeline-builder' ),
					'singular_name' => _x( 'Timeline Category', 'taxonomy singular name', 'wpcalibrate-timeline-builder' ),
					'search_items'  => __( 'Search Categories', 'wpcalibrate-timeline-builder' ),
					'all_items'     => __( 'All Categories', 'wpcalibrate-timeline-builder' ),
					'edit_item'     => __( 'Edit Category', 'wpcalibrate-timeline-builder' ),
					'update_item'   => __( 'Update Category', 'wpcalibrate-timeline-builder' ),
					'add_new_item'  => __( 'Add New Category', 'wpcalibrate-timeline-builder' ),
					'new_item_name' => __( 'New Category Name', 'wpcalibrate-timeline-builder' ),
					'menu_name'     => __( 'Categories', 'wpcalibrate-timeline-builder' ),
				],
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => false,
				'show_admin_column' => false,
				'query_var'         => false,
				'rewrite'           => false,
				'show_in_rest'      => false,
			]
		);
	}
}
