<?php

namespace LWTV\Helpers;

class Components {

	/**
	 * Specify list of Required Core components.
	 *
	 * The components are called in order (top down), so if a component is used by another,
	 * it must be on top.
	 *
	 * @return array
	 */
	public function core_components(): array {
		return array(
			// Features Must be First
			'LWTV_Features',
			'LWTV_Features_Upgrades',
			'LWTV_Features_ClickJacking',
			'LWTV_Features_Cron',
			'LWTV_Features_Roles',
			'LWTV_Features_Embeds',
			'LWTV_Features_Private_Posts',
			'LWTV_Features_User_Profiles',
			'LWTV_Features_Shortcodes',
			// Symbolicons
			'LWTV_Assets_Symbolicons',
			// Calendar
			'LWTV_Calendar',
			// Debugger
			'LWTV_Debugger',
			// Admin Menus
			'LWTV_AdminMenu_Menu',
			'LWTV_AdminMenu_Dashboard_Widget',
			// Statistics
			'LWTV_Statistics',
			'LWTV_This_Year',
			// Blocks
			'LWTV_Blocks',
			// Of The Day RSS Feed
			'LWTV_Of_The_Day_RSS',
			// Rest API
			'LWTV_Rest_API_Fresh_JSON',
			'LWTV_Rest_API_Alexa_Skills',
			'LWTV_Rest_API_Export_JSON',
			'LWTV_Rest_API_IMDb_JSON',
			'LWTV_Rest_API_List_JSON',
			'LWTV_Rest_API_OTD_JSON',
			'LWTV_Rest_API_Shows_Like_JSON',
			'LWTV_Rest_API_Stats_JSON',
			'LWTV_Rest_API_This_Year_JSON',
			'LWTV_Rest_API_What_Happened_JSON',
			'LWTV_Rest_API_Whats_On_JSON',
			// Plugins
			'LWTV_Plugins_CMB2',
			'LWTV_Plugins_Comment_Probation',
			'LWTV_Plugins_FacetWP',
			'LWTV_Plugins_Gravity_Forms',
			'LWTV_Plugins_Gutenslam',
			'LWTV_Plugins_Jetpack',
			'LWTV_Plugins_Yoast',
			'LWTV_Plugins_Related_Posts_By_Taxonomy',
			// WP-CLI
			'LWTV_WP_CLI',
			// Dashboard: MUST be near the end.
			'LWTV_Features_Dashboard',
			'LWTV_Features_Dashboard_Posts_In_Progress',
			// Custom Post Types: This MUST be at the end.
			'LWTV_CPTs',
		);
	}
}
