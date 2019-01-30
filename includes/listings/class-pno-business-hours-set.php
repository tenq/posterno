<?php
/**
 * Representation of a listing's business hours set.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

namespace PNO\Listing\BusinessHours;

use DateTime;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Represents a listing's business hours set for a day.
 */
class Set {

	/**
	 * Opening time.
	 *
	 * @var DateTime|null
	 */
	public $start;

	/**
	 * Closing time.
	 *
	 * @var DateTime|null
	 */
	public $end;

	/**
	 * Whether it closes after midnight.
	 *
	 * @var boolean
	 */
	public $after_midnight = false;

	/**
	 * Human readable formatted opening time.
	 *
	 * @var string
	 */
	public $start_time;

	/**
	 * Human readable formatted closing time.
	 *
	 * @var string
	 */
	public $end_time;

	/**
	 * Numeric representation of the day of the week.
	 *
	 * @var string|int
	 */
	public $day_of_week;

	/**
	 * The name of the day.
	 *
	 * @var string
	 */
	public $day_name;

	/**
	 * Get things started.
	 *
	 * @param DateTime $start opening time.
	 * @param DateTime $end closing time.
	 */
	public function __construct( DateTime $start = null, DateTime $end = null ) {

		$this->start = $start;
		$this->end   = $end;

		if ( $start !== null & $end !== null ) {

			$this->day_of_week = (int) $this->start->format( 'N' );
			$this->day_name    = $this->start->format( 'l' );
			$this->start_time  = $this->start->format( 'g:i A' );
			$this->end_time    = $this->end->format( 'g:i A' );

		}

	}

	/**
	 * Verify if two sets of opening hours are equal.
	 *
	 * @param Set     $opening_hours set to compare.
	 * @param boolean $including_day whether to include the day or not into the comparison.
	 * @return boolean
	 */
	public function is_equal( Set $opening_hours, $including_day = false ) {

		if ( $including_day && $this->day_of_week !== $opening_hours->day_of_week ) {
			return false;
		}

		return $this->start_time === $opening_hours->start_time && $this->end_time === $opening_hours->end_time;

	}

	/**
	 * Print the business hours set on the frontend.
	 *
	 * @param string $format custom format.
	 * @param string $separator custom separator.
	 * @return string
	 */
	public function to_string( $format = 'g:i A', $separator = ' &mdash; ' ) {

		$start = $this->start->format( $format );
		$end   = $this->end->format( $format );

		if ( pno_get_option( 'business_hours_remove_zeroes' ) ) {
			$start = str_replace( ':00', '', $start );
			$end   = str_replace( ':00', '', $end );
		}

		return sprintf(
			'%s%s%s',
			$start,
			$separator,
			$end
		);

	}

}
