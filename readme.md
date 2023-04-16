## LezWatch.TV Plugin

Custom plugin for LezWatch.TV

## Description

The LezWatch.TV plugin contains custom code for use with the site. Instead of having everything hardcoded into the theme, or shoved in a shared MU Plugin, it was moved to it's own plugin. This allows it to be updated outside of the theme and deploy new features as needed.

The code was written by Tracy Levesque and Mika Epstein, with assistance from [Yikes Inc.](https://YikesInc.com)

Further documentation can be found at [docs.lezwatchtv.com](https://docs.lezwatchtv.com)

## Features

The main file `functions.php` calls all other needed files.

Defines:

* `FIRST_LWTV_YEAR` - The first year of known queers on TV (if not otherwise defined). Currently 1961.

### Admin Panels

Stored in `/admin/` -- Makes admin panels

* `_main.php` - Loader file and mapping
* `admin_tools.php` - UX for checkers
* `dashboard.php` - Powers wp-admin dashboard code

### Affiliates

Stored in `/affiliates/` -- Auto Adds in Affiliate Codes. Currently requires AdRotate to display ads.

* `/images/` - Images used by local 'ads'
* `_main.php` - Determines which ad to show and when
* `ways-to-watch.php` - Affiliate links and prettyfication of services
* `widget.php` - Widget code to display ads

### Assets

Stored in `/assets/`

* `symbolicons.php` - Symbolicons

_CSS (`css`)_

* `author-box.css` - CSS for the Author Box (will be moved eventually)
* `lwtv-tools.css` - Style for the LWTV admin pages
* `symbolicons-admin.css` - Symbolicons CSS
* `theme.bootstrap_4.min.css` - Tablesorter CSS for Bootstrap themes
* `wp-admin.css` - WP Admin CSS

_Images (`images`)_

* `diane-fuck-off.gif` - Diane flipping people off
* `lezwatchtv.png` - login page icons
* `rainbow.svg` - Logo for admin panel
* `toaster.png` - A toaster icon (used when no featured image is found)
* `unicorn.png` - Unicorn (used if a toaster cannot be loaded)

_Javascript (`js`)_

* `chart.js` - Stat charts from [ChartJS](https://chartjs.org)
* `chart.umd.js.map` - Map for Charts (got renamed, movin' on)
* `chartjs-plugin-annotation.min.js` - Annotations for ChartJS
* `cmb2_ajax.js` - Ajaxified code for CMB2 and post edits
* `facetwp-pagination.js` - Facet WP pagination
* `jquery.tablesorter.js` - Allow for table sorting
* `palette.js` - Colors for charts

### Blocks (aka Gutenberg)

Stored in `/blocks/`

Blocks for Gutenberg. The file `_main.php` acts as an autoloader. If you're updating the CSS, you will need to rebuild the blocks. Read `/blocks/README.md` for more information or just tell Mika what to change.

* `calendar.php` - Calendar specific code
* `serverside.php` - Server-side Renders: Run PHP code in JS blocks

The source code is in `/src/` broken up by folder, with one special file

* `index.js` - Master file to call everything else.
* `/_common/` - Common PHP, CSS, and JS code for all blocks
* `/affiliate-grid/` - A bootstrap styled grid to show affiliate links
* `/author-box/` - Author Boxes: Display author information
* `/featured-image/` - Featured Image: Shows the image inline, not just on the sidebar
* `/glossary/` - Glossary: Show a visual glossary of taxonomies with icons
* `/grade/` - Grade: Give something a grade and an explanation
* `/private-note/` - Private Note: Shows something ONLY to logged in editors
* `/screener/` - Screeners: For reviews of shows that haven't aired yet
* `/spoiler/` - Spoiler: Warning for spoilers
* `/tvshow-calendar/` - TV Show Calendar: Lists all the shows on air

### Custom Post Types

Stored in `/cpts/`:

* `_main.php` - Main file that calls everything else. Also disables calendar code for CPTs
* `post-meta.php` - Registers post-meta so it can be properly used and hidden from Gutenberg
* `related-posts.php` - Displays related shows and posts (based on tags)

_Actors (`actors`)_

* `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta (for actors), Yoast Meta Replacement, AMP support
* `calculations.php` - Does the math for various data points used to generate show scores, stores count of number of characters for faster retrieval later
* `cmb2-metaboxes.php` - CMB2 code to make display nicer
* `custom-columns.php` - Define columns for post listing

_Characters (`/characters/`)_

* `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta, Yoast Meta Replacement, AMP support
* `calculations.php` - Does the math for various data points used to generate show scores
* `cmb2-metaboxes.php` - CMB2 code to make display nicer
* `custom-columns.php` - Define columns for post listing

_Shows (`/shows/`)_

* `_main.php` - Builds CPT and Taxonomies, adds to Dashboard, triggers saving related post meta, Yoast Meta Replacement, AMP support
* `calculations.php` - Show score math
* `calendar-names.php` - Processes TV show names when they don't match TV Maze
* `cmb2-metaboxes.php` - CMB2 code to make display nicer
* `custom-columns.php` - Define columns for post listing
* `shows-link-this.php` - Calculations for 'shows like this' (uses [Related Posts by Taxonomy](https://wordpress.org/plugins/related-posts-by-taxonomy/))

### Features

Stored in `/features/` -- a collection of miscellaneous features.

* `_main.php` - Calls all other files
* `apis.php` - Calls to third party APIs
    - `tvmaze_episodes()` - output of next ep for TV Maze
* `clickjacking.php` - Prevents Clickjacking (or at least people claiming it's an issue)
* `cron.php` - Custom cron jobs to pre-cache high-traffic pages, and run regular jobs.
* `custom-loops.php` - `class LWTV_Loops` - Custom arrays and WP_Query calls that are repeated in multiple places.
    - `is_actor_queer()` - Determine if actor is queer (yes/no)
    - `is_actor_trans()` - Determine if an actor is trans (called by is queer) (yes/no)
    - `is_show_on_air()` - Determine if a show is on air or not (bool)
    - `tax_query()` - Taxonomy Array
    - `tax_two_query()` - Taxonomy Two Array is used for generating a query of posts that are in two taxonomies
    - `post_meta_query()` - Post Meta Array returns the whole post data. Can handle compares and likes
    - `wp_meta_query()` - SQL version of meta query _(not currently used)_
    - `post_type_query()` - Generate an object of all posts in a specific post type.
    - `post_meta_and_tax_query()` - Generate an array of posts that have a specific post meta AND a specific taxonomy value. Useful for getting a list of all dead queers who are main characters (for example).
    - `related_posts_by_tag()` - Related Posts by Tags.
* `custom-roles.php` - Custom roles created for curation of content
* `dashboard.php` - Custom column for featured images in posts lists and removal of self pings
* `debug.php` - Debugging Tools for weird content.
* `embeds.php` - Embeds DisneyABCPress videos, Gleam, GoFundMe, Indiegogo
* `grading.php` - Build and format array for displaying show scores including 3rd parties.
* `ics-parser.php` - Connection to the ICS Parser for calendar data.
* `private-data.php` - Shows alert that a page is hidden if the post is set private.
* `profiles.php` - Custom profile code
* `shortcodes.php` - Various shortcodes - mostly deprecated, but some used by blocks.
    - Badges: `[badge url=LINK class="class class" role="role"]TEXT[/badge]`
    - Copyright Year: `[copyright year=(start year) text=(copyright text)]`
    - Number of Posts: `[numposts data="posts" posttype="post type" term="term slug" taxonomy="taxonomy slug"]`
    - Display The first year we had queers: `[firstyear]`
    - Display This Month recap: `[thismonth]` or `[thismonth date="2017-01"]`
	- Deprecated Shortcodes (remaining for backcompat):
		* Display screener information: `[screener title="Some Like It Hot" summary="A quirky black and white romcom" queer="2" worth="yes" trigger="low"]` (now a block)
		* Author Box:  `[author-box users=username]`
		* Embed Gleam: `[gleam url="https://gleam.io/iR0GQ/gleam-demo-competition"]Gleam Demo Competition[/gleam]`
		* Embed IndieGoGo: `[indiegogo url=URL]`
		* Glossary: `[glossary taxonomy=TAXONOMY]`
		* Spoilers: `[spoilers]` or `[spoilers warning="OMG SPIDERS!!!"]`
* `spammers.php` - Prevent Spammers from annoying us
* `upgrades.php` - Handle upgrades of WP and everything included.
* `wp-cli.php`- WP-CLI
    - Re-run calculations for specific post content (actors & shows): `wp lwtv calc [actor|show|character] ID`
    - Compare data to WikiData: `wp lwtv wiki [actor] ID`
    - Find miss matched data: ex. `wp lwtv find queerchars`

### Node Scripts

Stored in `/node_scripts/` -  Scripts used by NPM (for anything in `node_modules`). _This is removed by the builder script when pushed to production._

* `postinstall.js` - script run at the end of NPM to move files to the correct location.

### Plugin Addons

Stored in `/plugins/`

The file `_main.php` acts as an autoloader.

* `cache.php` - Custom Cache specific to DreamPress hosting
    - Generates data used by Proxy Cache Purge and WP Rocket to know what to flush.
* `cmb2.php` - Integration with CMB2
    - calls other files
    - generates a CB2 formatted list of terms
* `/cmb2/` - CMB2 add on libraries
    - `cmb2-attached-posts/` - CMB2 attached posts (HEAVILY forked)
    - `cmb2-grid/` - CMB2 Grid Display
    - `cmb2.css` - Custom CSS
    - `lwtv.php` - Special code for us -- Favorite shows for author profiles, Symbolicon support
    - `year-range.php` - Year Range -- 'date_year_range' custom field type
* `comment_probation.php` - Fork of abandoned plugin
* `facetwp.php` -- Facet WP
    - calls other files
    - Only show pagination if there's more than one page
    - Reset Shortcode
* `/facetwp/` - FacetWP Folder
    - `/facetwp-cmb2/` - FacetWP Integration with CMB2
    - `facet.js` - Pagination Scrolling and Refresh Warning
    - `lwtv.php`
        - filter Data before it's saved to rename values (capitalization)
        - split actors and shows into separate entries, and add additional orderby params
* `gravity-forms.php` - Protection from spammers via disallowed keys
* `/gravity-forms/` - Gravity Forms Folder
    - `class-gf-approvals.php` - Approval Code (forked from another plugin)
* `gutenslam.php` - make Block Editor stop being such a dillhole and forget preferences
* `jetpack.php`  - Jetpack integration
    - Adds Post Type to sort.
    - Show Feedback in "Right Now"
    - Custom Icon for Feedback in "Right Now"
    - Mark feedbacks as having been answered
    - Protection from spammers via disallowed keys
* `varnish.php` - Generate a list of special URLs to flush per post type.
* `yoast.php` - Custom Yoast controls

### Rest API

Stored in `/rest-api/` - These files generate the REST API output.

* `_main.php` - autoloader
* `alexa-skills.php` - Builds the basic Alexa API (see also Alexa Skills section below)
* `bury-your-queers.php` -  LezWatch.TV Plugin (formerly Bury Your Queers)
    - Last Death - "It has been X days since the last WLW Death"
    - On This Day - "On this day, X died"
    - When Died - "X died on date Y"
* `export-json.php` - Export content in JSON format. Mostly used for WikiData and Universities.
* `fresh.php` - Generates 'whats new' content
* `imdb.php` - API to communicate with IMDb and generate information (used by Alexa)
* `list.php` - Generates lists
* `of-the-day.php` - X Of The Day API service. Every 24 hours, a new character and show of the day are spawned
* `shows-like-this.php` - Similar shows.
* `slack.php` - Beginning of code to report newly dead characters to Slack _(very buggy, currently disabled)_
* `stats.php` - JSON API version of the stats (mostly)
* `this-year.php` - Outputs simplified lists of what happened in a given year.
* `what-happened.php` - Outputs data based on what happened in a given year, year-month, or specific day.
* `whats-on.php` - What's on TV tonight (or tomorrow).

_Alexa Skills (`/alexa/`)_

* `_common.php` - Code used by multiple Alexa skills
* `_validate.php` - Validates the requests as coming from Amazon
* `byq.php` - Old BYQ code
* `flash-brief.php` - Since the flash brief has trouble with media in post content, we've made our own special version.
* `newest.php` - Generate the newest shows or characters (or deaths)
* `shows.php` - Skills for interactions with shows (similar shows, recommended shows, etc.)
* `this-year.php` - Gives you an idea how this year is going...
* `whats-on.php` - Generates what's on TV stuff.
* `who-are-you.php` - Runs all code that discusses actors, characters, and shows.

_Templates (`/templates/`)_

* `export-json.php` - uses var query data to determine what to show.

### Statistics

Stored in `/statistics/` - These files generate everything for stats, from graphs to the rest API stuff.

* `_main.php` - Base Code: `class LWTV_Stats`
    - `function generate()` - Generates base stats. This makes a lot of calls to arrays and outputs
    - `function showcount()` - Slices shows into smaller chunks (i.e 'all shows in Australia') and can output raw counts, on-air counts, scores, or on-air scores.
* `array.php` -  Arrays: `class LWTV_Stats_Arrays`
    - `function taxonomy()` - Generate array to parse taxonomy content
    - `functions dead_taxonomy()` - Generate Taxonomy Array for dead characters
    - `function dead_role()` - Array for dead characters by role (regular, etc)
    - `function dead_meta_tax()` - Generate array to parse taxonomy content as it relates to post metas (for dead characters)
    - `function meta()` - Generate array to parse post meta data
    - `function yes_no()` - Generates arrays for content that has Yes/No values (shows we love, on air)
    - `function taxonomy_breakdowns()` - generates complex arrays of cross related data from multiple taxonomies to list 'all miniseries in the USA' (this one makes us cry)
    - `function dead_basic()` - Simple counts of all shows with dead, or all dead characters
    - `function dead_year()` - Simple counts of death by year (Sara Lance...)
    - `function on_air()` - Shows or characters on air per year
    - `function dead_shows()` - Array of shows with (and without) dead characters, but because of Sara Lance, we have to cross relate to make sure all the shows with death have actually dead characters (yes, a show can have a dead-flag but no actively dead characters)
    - `function dead_complex_taxonomy()` - Complex death taxonomies for stations and nations.
    - `function scores()` - Show Scores
    - `function actor_chars()` - How many actors or characters per actor or character...
    - `function show_roles()` - Roles of characters on Shows, with how many of each role are dead
    - `function complex_taxonomy()` - How many characters are played by out queer actors, but also how many characters for each term.
* `gutenberg-ssr.php` - Gutenberg Server side rendering to show stats
*  `output.php` - Output: `class LWTV_Stats_Output`
    - `function lists()` - Table lists with simple counts
    - `function percentages()` - Table lists with percentages and a bar
    - `function averages()` - Averages, highs, and lows (ex show scores)
    - `function calculate_trendline()` - Calculates trendline data
    - `function linear_regression()` - Calculates linear regression (used by trends)
    - `function barcharts()` - Horizontal barcharts
    - `function stacked_barcharts()` - Stacked Barcharts (also horizontal)
    - `function piecharts()` - Piecharts (actually donuts...)
    - `function trendline()` - Trendlines (against a vertical barchart)
* `query_vars.php` - Query Variables customization (to make virtual pages) and Yoast meta

_Templates (`/templates/`)_

Output templates used by the shortcodes and Gutenberg (as well as when included on the pages themselves). These were originally in the theme, but were moved here to allow for easier updates.

### This Year

Stored in `/this-year/` - Technically a subset of statistics, This Year shows you just the data for the indicated year.

* `_main.php` - Basic data loading, calls templates etc.
* `characters.php` - all data on characters per year
* `query_vars.php` - customize query variables
* `shows.php` - all data on shows per year

### Vendor Files

Stored in `/vendor/` - this has to be included for things to function properly. Everything is loaded by `autoload.php`. This is all autogenerated by Composer.

* `/bin/` - temp holder for composer
* `/composer/` - composer library and auto loader
* `/johngrogg/ics-parser` - ICS parser code (for TV calendar)
* `/nesbot/carbon/` - Required by ICS Parser
* `/symfony/` - Required by ICS parser

## Development

Update code like you normally would. If you don't want to push it anywhere, make a local branch. Always remember, merge to **development** first and check on the (private) dev server. If that works, do a pull request from development to **production** to automatically update.

### Libraries

In order to make maintenance easier, instead of checking everything all the time, we use NPM and composer for the following included libraries:

**NPM**
* [ChartJS](https://github.com/chartjs/Chart.js/)
* [TableSorter (Mottie Fork)](https://github.com/Mottie/tablesorter)
* [CMB2 Grid](https://github.com/origgami/CMB2-grid)

#### Installation and Updating

`$ npm update && npm install && composer update`

The scripts will install everything where they need to be.

### Deployment

Pushes to branches are automatically deployed via Actions as follows:

* Development: [lezwatchtvcom.stage.site](https://lezwatchtvcom.stage.site) (password required - Ask Mika)
* Production: [lezwatchtv.com](https://lezwatchtv.com)
