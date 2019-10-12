## LezWatch.TV Plugin

Custom plugin for LezWatch.TV

## Description

The LezWatch.TV plugin contains custom code for use with the site. Instead of having everything hardcoded into the theme, or shoved in a shared MU Plugin, it was moved to it's own plugin. This allows it to be updated outside of the theme and deploy new features as needed.

The code was written by Tracy Levesque and Mika Epstein, with assistance from [Yikes Inc.](https://YikesInc.com)

Further documentation can be found at [docs.lezwatchtv.com](https://docs.lezwatchtv.com)

## Features

The main file `functions.php` calls all other needed files.

Defines `LWTV_LIBRARY` - If the library files are found, this is set to true.

Defines `FIRST_LWTV_YEAR` - The first year of known queers on TV (if not otherwise defined). Currently 1961.

### Admin Panels

Stored in `/admin/` -- Makes admin panels

* `_tools.php` - Automated checks on data
* `news.php` - Loads possibly relevant queer news
* `screeners.php` - A list of all screeners

### Affiliates

Stored in `/affiliates/` -- Auto Adds in Affiliate Codes

* `/images/` - Images used by local 'ads'
* `_main.php` - Determines which ad to show and when
* `amazon.php` - Amazon (utilizes ApaiIO library (from (LezWatch Library)[https://github.com/LezWatch/lezwatch-library]))
* `apple.php` - Apple iTunes/TV ads
* `cbs.php` - CBS ads (ImpactRadius)
* `cj.php` - Commission Junction (AMC and Starz)
* `local.php` - Local Ads (Yikes, DreamHost, FacetWP)
* `widget.php` - Widget code to display ads

### Assets

Stored in `/assets/`

_CSS (`css`)_

* `lwtv-tools.css` - Style for the LWTV admin pages
* `theme.bootstrap_4.min.css` - Tablesorter CSS for Bootstrap themes

_Images (`images`)_

* `rainbow.svg` - Logo for admin panel
* `toaster.png` - A toaster icon (used when no featured image is found)
* `unicorn.png` - Unicorn (used if a toaster cannot be loaded)

_Javascript (`js`)_

* `Chart.bundle.min.js` - Stat charts from [ChartJS](https://chartjs.org)
* `Chart.colors.js` - Colors for charts (currently unused)
* `jquery.tablesorter.js` - Allow for table sorting
* `palette.js` - Colors for charts

### Custom Post Types

Stored in `/cpts/`:

* `_main.php` - Main file that calls everything else. Also disables calendar code for CPTs
* `post-meta.php` - Registers post-meta so it can be properly used and hidden from Gutenberg
* `related-posts.php` - Displays related shows and posts (based on tags)

* Actors: `/actors/` - Actor Post Type and related Taxonomies
    * `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta (for actors), Yoast Meta Replacement, AMP support
    * `calculations.php` - Does the math for various data points used to generate show scores, stores count of number of characters for faster retrieval later
    * `cmb2-metaboxes.php` - CMB2 code to make display nicer
    * `custom-columns.php` - Define columns for post listing
* Characters: `/characters/` - Character Post Type and related Taxonomies
    * `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta, Yoast Meta Replacement, AMP support
    * `calculations.php` - Does the math for various data points used to generate show scores
    * `cmb2-metaboxes.php` - CMB2 code to make display nicer
    * `custom-columns.php` - Define columns for post listing
* Shows: `/shows/` - Show Post Type and related Taxonomies
    * `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta, Yoast Meta Replacement, AMP support
    * `calculations.php` - Show score math
    * `cmb2-metaboxes.php` - CMB2 code to make display nicer
    * `custom-columns.php` - Define columns for post listing
    * `shows-link-this.php` - Calculations for 'shows like this' (uses [Related Posts by Taxonomy](https://wordpress.org/plugins/related-posts-by-taxonomy/))

### Features

Stored in `/features/` -- a collection of miscellaneous features.

* `_main.php` - Calls all other files
* `cron.php` - Custom cron jobs to load high-traffic pages and ensure Varnish is cached.
* `custom-loops.php` - Custom arrays and WP_Query calls that are repeated in multiple places.
    - Determine if actor is queer
    - Taxonomy Array
    - Taxonomy Two Array - used for generating a query of posts that are in two taxonomies
    - Post Meta Array
    - WP Meta Query
    - Post Type Array - Generate an array of all posts in a specific post type.
    - Post Meta AND Taxonomy Query - Generate an array of posts that have a specific post meta AND a specific taxonomy value. Useful for getting a list of all dead queers who are main characters (for example).
    - Related Posts by Tags.
* `debug.php` - Debugging Tools for weird content.
* `private-data.php` - Shows alert that a page is hidden if the post is set private.
* `profiles.php` - Custom profile code
* `search.php` - Extra Search Functions:
    - Join posts and postmeta tables
    - Modify Search Location to include custom fields
    - Force search to be distinct and prevent duplicates
    - Pretty Permalinks for Search
* `shortcodes.php` - Various shortcodes used on LezWatch.TV
    - Display The first year we had queers: `[firstyear]`
    - Display This Month recap: `[thismonth]` or `[thismonth date="2017-01"]`
    - Display screener information: `[screener title="Some Like It Hot" summary="A quirky black and white romcom" queer="2" worth="yes" trigger="low"]`
    - Display author information: `[author name="ipstenu"]`
* `sort-stopwords.php` - Filter post order by for shows to NOT include the/an/a when sorting.
* `wp-cli.php`- WP-CLI
    - Re-run calculations for specific post content (actors & shows): `wp lwtv calc actor ID`
    - Find miss matched data: ex. `wp lwtv find queerchars`

### Gutenberg

Stored in `/gutenberg/`

Blocks for Gutenberg. The file `_main.php` acts as an autoloader.

* Author Boxes: Display author information
* Custom Post Type Meta Data: _currently disabled_
* Glossary: Show a visual glossary of taxonomies with icons
* Grade: Give something a grade and an explanation
* Screeners: For reviews of shows that haven't aired yet
* Statistics: _currently disabled_

### Plugin Addons

Stored in `/plugins/`

The file `_main.php` acts as an autoloader.

* CMB2: `/cmb2.php`
    - calls other files
    - generates a CB2 formatted list of terms
* CMB2 Folder - `/cmb2/`
    - Select2: `/cmb-field-select2/` - CMB2 Field Type: Select2
    - Grid: `/CMB2-grid/` - A grid display system
    - Custom CSS: `/cmb2.css`
    - LWTV: `/lwtv.php` - Favorite shows for author profiles, Symbolicon support
    - Year Range: `/year-range.php` - 'date_year_range' custom field type
* FacetWP - `/facet.php`
    - calls other files
    - Only show pagination if there's more than one page
    - Reset Shortcode
* FacetWP Folder - `/facet/`
    - CMB2: `/facetwp-cmb2/cmb2.php` - FacetWP Integration with CMB2
    - CLI: `/facetwp-wp-cli/facetwp-wp-cli.php` - WP-CLI commands
    - JS: `facet.js` - Pagination Scrolling and Refresh Warning
    - LWTV: `/lwtv.php`
        - filter Data before it's saved to rename values (capitalization)
        - split actors and shows into separate entries, and add additional orderby params

### Rest API

Stored in `/rest-api/` - These files generate the REST API output.

The file `_main.php` acts as an autoloader.

* Alexa Skills: `alexa-skills.php` - Builds the basic Alexa API
* Alexa Skills - `/alexa/`
    - Validation: `/alexa-validate.php` - Validates the requests as coming from Amazon
    - Bury Your Queers: `/byq.php` - Old BYQ code
     - Flash Briefing: `/flash-brief.php` - Since the flash brief has trouble with media in post content, we've made our own special version.
    - Newest: `/newest.php` - Generate the newest shows or characters (or deaths)
    - This Year: `/this-year.php` - Gives you an idea how this year is going...
    - Who Are You: `/who-are-you.php` - Runs all code that discusses actors
* LezWatch.TV Plugin (formerly Bury Your Queers) - `/bury-your-queers.php`
    - Last Death - "It has been X days since the last WLW Death"
    - On This Day - "On this day, X died"
    - When Died - "X died on date Y"
* Export - `/export-json.php`
    - Customized export. Currently used for WikiData.
* IMDb - `/imdb.php`
    - API to communicate with IMDb and generate information (used by Alexa)
* Of The Day - `/of-the-day.php`
    - The code that runs the X Of the Day API service. Every 24 hours, a new character and show of the day are spawned
* Shows Like This - `shows-like-this.php`
    - Similar shows.
* Slack - `/slack.php`
    - Beginning of code to report newly dead characters to Slack (very buggy, currently disabled)
* Statistics - `/stats.php`
    - JSON API version of the stats (mostly)
* What Happened - `/what-happened.php`
    - Outputs data based on what happened in a given year.
* What's On - `/whats-on.php`
    - What's on TV tonight (or tomorrow).

### Statistics

Stored in `/statistics/` - These files generate everything for stats, from graphs to the rest API stuff.

* Base Code: `class LWTV_Stats` - `_main.php`
    - Generate: Statistics Base Code
* Arrays: `class LWTV_Stats_Arrays` - `array.php`
    - Taxonomy Array
    - Taxonomy Array for dead
    - Array for dead by role
    - Meta and Taxonomy Array
    - Simple Meta Array
    - Yes/No Arrays
    - Nations
    - Basic Death
    - Death By Year
    - Death on Shows
    - Death in Taxonomies
    - Show Scores
    - Actors/Characters
    - Roles on Shows
    - Queerness
* Gutenberg: `gutenberg-ssr.php`
    - Server side rendering to show statistics.
* Query Variables: `query_vars.php`
    -- Code to customize query variables
* Output: `class LWTV_Stats_Output` - `output.php`
    - Lists
    - Percentages
    - Averages
    - Linear regression function
    - Barcharts
    - Stacked Barcharts
    - Piecharts
    - Trendlines
* Templates: `/templates/`
    - Output templates used by the shortcodes and Gutenberg (as well as when included on the pages themselves). These were originally in the theme, but were moved here to allow for updates.

### This Year

Stored in `/this-year/` - Technically a subset of statistics, This Year shows you just the data for the indicated year.

* `_main.php` - Basic data loading, calls templates etc.
* `characters.php` - all data on characters per year
* `shows.php` - all data on shows per year
* `query_vars.php` - customize query variables

## Development

Update code like you normally would. If you don't want to push it anywhere, make a local branch. Always remember, merge to **development** first and check on the (private) dev server. If that works, do a pull request from development to **master** to automatically update.

### Libraries

In order to make maintenance easier, instead of checking everything all the time, we use NPM and composer for the following included libraries:

**NPM**
* [ChartJS](https://github.com/chartjs/Chart.js/)
* [TableSorter (Mottie Fork)](https://github.com/Mottie/tablesorter)

**Composer**
* [CMB2](https://github.com/WebDevStudios/CMB2)
* [CMB2 Grid](https://github.com/origgami/CMB2-grid)
* [CMB2 Field Select2](https://github.com/mustardBees/cmb-field-select2)
* [FacetWP wp-cli](https://github.com/level-level/facetwp-wp-cli)
* [FacetWP CMB2](https://github.com/WebDevStudios/facetwp-cmb2)

#### Installation and Updating

1. `npm install`
2. `composer update`

Composer will move everything into the right place, even NPM files.

### Deployment

Pushes to branches are automatically deployed via Codeship as follows:

* Development: [lezwatchtvcom.stage.site](https://lezwatchtvcom.stage.site)
* Master: [lezwatchtv.com](https://lezwatchtv.com)
