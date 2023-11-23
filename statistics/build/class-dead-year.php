<?php

class LWTV_Statistics_Dead_Year_Build {

	/*
	 * Statistics Death By Year
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions.
	 *
	 * @return array
	 */
	public function make() {
		$transient = 'dead_year_stats';
		$array     = LWTV_Features_Transients::get_transient( $transient );

		if ( false === $array ) {

			$array = array();

			// Create the date with regards to timezones
			$tz        = 'America/New_York';
			$timestamp = time();
			$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
			$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
			$this_year = $dt->format( 'Y' );

			// Death by year
			$year_first           = FIRST_LWTV_YEAR;
			$year_deathlist_array = array();
			foreach ( range( $this_year, $year_first ) as $x ) {
				$year_deathlist_array[ $x ] = $x;
			}

			foreach ( $year_deathlist_array as $year ) {
				$year_death_query = ( new LWTV_Features_Loops() )->post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $year, 'lez_cliches', 'slug', 'dead', 'REGEXP' );

				$array[ $year ] = array(
					'name'  => $year,
					'count' => $year_death_query->post_count,
					'url'   => home_url( '/this-year/' . $year . '/' ),
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
