<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package WPCalibrate\TimelineBuilder
 */

declare(strict_types=1);

// Exit if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check global data retention setting.
// Default is OFF (data is preserved unless explicitly enabled).
$delete_data = get_option( 'wpcal_timeline_delete_data_on_uninstall', 'no' );
if ( 'yes' !== $delete_data && true !== $delete_data && '1' !== $delete_data ) {
	return;
}

global $wpdb;

// 1. Delete all timeline items.
$timeline_items = get_posts(
	[
		'post_type'      => 'wpcal_timeline_item',
		'post_status'    => 'any',
		'numberposts'    => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);
foreach ( $timeline_items as $item_id ) {
	wp_delete_post( $item_id, true );
}

// 2. Delete all timelines.
$timelines = get_posts(
	[
		'post_type'      => 'wpcal_timeline',
		'post_status'    => 'any',
		'numberposts'    => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);
foreach ( $timelines as $timeline_id ) {
	wp_delete_post( $timeline_id, true );
}

// 3. Delete taxonomy terms and relationships for wpcal_timeline_cat.
$terms = get_terms(
	[
		'taxonomy'   => 'wpcal_timeline_cat',
		'hide_empty' => false,
		'fields'     => 'ids',
	]
);
if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
	foreach ( $terms as $term_id ) {
		wp_delete_term( $term_id, 'wpcal_timeline_cat' );
	}
}

// 4. Delete registered options strictly belonging to this plugin.
delete_option( 'wpcal_timeline_settings' );
delete_option( 'wpcal_timeline_license' );
delete_option( 'wpcal_timeline_version' );
delete_option( 'wpcal_timeline_db_version' );
delete_option( 'wpcal_timeline_delete_data_on_uninstall' );

// 5. Delete transients created by this plugin.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wpcal_timeline_%' OR option_name LIKE '_transient_timeout_wpcal_timeline_%'" );

// 6. Remove custom capabilities from administrator and editor roles if mapped.
$roles = [ 'administrator', 'editor' ];
foreach ( $roles as $role_slug ) {
	$role = get_role( $role_slug );
	if ( $role ) {
		$role->remove_cap( 'manage_wpcal_timelines' );
		$role->remove_cap( 'edit_wpcal_timelines' );
		$role->remove_cap( 'publish_wpcal_timelines' );
		$role->remove_cap( 'delete_wpcal_timelines' );
	}
}
