<?php

namespace LWTV\Theme;

class Stats_Symbolicon {
	/**
	 * Symbolicon for Stats pages
	 *
	 * All pages have custom icons.
	 *
	 * @access public
	 *
	 * @param  string $stats_type
	 * @return array
	 */
	public function make( $stats_type ) {

		// Defaults:
		$stats_array = array(
			'image' => lwtv_plugin()->get_symbolicon( 'graph-bar.svg', 'fa-chart-area' ),
			'intro' => '',
		);

		// Based on the type of stats, set our display.
		switch ( $stats_type ) {
			case 'death':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'grim-reaper.svg', 'fa-ban' );
				$stats_array['intro'] = 'For a pure list of all dead, we have <a href="/trope/dead-queers/">shows where characters died</a> as well as <a href="/cliche/dead/">characters who have died</a> (aka the <a href="/cliche/dead/">Dead Lesbians</a> list).';
				break;
			case 'characters':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'chart-bar.svg', 'fa-chart-bar' );
				$stats_array['intro'] = 'Data specific to queer characters.';
				break;
			case 'actors':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'graph-line.svg', 'fa-chart-line' );
				$stats_array['intro'] = 'Data specific to actors who play queer characters.';
				break;
			case 'shows':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'chart-pie.svg', 'fa-chart-pie' );
				$stats_array['intro'] = 'Data specific to TV shows with queer characters.';
				break;
			case 'nations':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'globe.svg', 'fa-globe' );
				$stats_array['intro'] = 'Data specific to queer representation on shows by nation.';
				break;
			case 'stations':
				$stats_array['image'] = lwtv_plugin()->get_symbolicon( 'satellite-signal.svg', 'fa-bullhorn' );
				$stats_array['intro'] = 'Data specific to queer representation on shows by channel or station.';
				break;
			case 'formats':
				$stats_array['intro'] = 'Data specific to queer representation by show format (i.e. TV show, web series, etc.)';
				break;
			default:
				$stats_array = array(
					'image' => lwtv_plugin()->get_symbolicon( 'graph-bar.svg', 'fa-chart-area' ),
					'intro' => '',
				);
		}

		return $stats_array;
	}
}
