<?php
/**
 * Duplicator for Timelines and Timeline Items.
 *
 * @package WPCalibrate\TimelineBuilder\Timeline
 */

declare(strict_types=1);

namespace WPCalibrate\TimelineBuilder\Timeline;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TimelineDuplicator
 */
class TimelineDuplicator {

	/**
	 * Repository.
	 *
	 * @var TimelineRepository
	 */
	private TimelineRepository $repository;

	/**
	 * Constructor.
	 *
	 * @param TimelineRepository $repository Repository.
	 */
	public function __construct( TimelineRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Duplicate a full timeline and all of its items.
	 *
	 * @param int $source_id Source timeline ID.
	 * @return int New duplicated timeline ID, or 0 on failure.
	 */
	public function duplicate_timeline( int $source_id ): int {
		$source = $this->repository->get_timeline( $source_id );
		if ( ! $source ) {
			return 0;
		}

		$copy                 = clone $source;
		$copy->id             = 0;
		$copy->status         = 'draft';
		/* translators: %s: original timeline title */
		$copy->title          = sprintf( __( '%s (Copy)', 'wpcalibrate-timeline-builder' ), $source->title );

		$new_timeline_id = $this->repository->save_timeline( $copy );
		if ( ! $new_timeline_id ) {
			return 0;
		}

		// Copy items.
		$source_items = $this->repository->get_items( $source_id, [ 'post_status' => 'any', 'sorting' => 'manual' ] );
		foreach ( $source_items as $item ) {
			$item_copy              = clone $item;
			$item_copy->id          = 0;
			$item_copy->timeline_id = $new_timeline_id;
			$this->repository->save_item( $item_copy );
		}

		return $new_timeline_id;
	}

	/**
	 * Duplicate a single timeline item.
	 *
	 * @param int $item_id Source item ID.
	 * @return int New item ID or 0 on failure.
	 */
	public function duplicate_item( int $item_id ): int {
		$source = $this->repository->get_item( $item_id );
		if ( ! $source ) {
			return 0;
		}

		$copy        = clone $source;
		$copy->id    = 0;
		/* translators: %s: original item title */
		$copy->title = sprintf( __( '%s (Copy)', 'wpcalibrate-timeline-builder' ), $source->title );
		$copy->order = $source->order + 1;

		return $this->repository->save_item( $copy );
	}
}
