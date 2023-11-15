## LezWatch.TV Plugin

Custom plugin for LezWatch.TV

## Description

The LezWatch.TV plugin contains custom code for use with the site. Instead of having everything hardcoded into the theme, or shoved in a shared MU Plugin, it was moved to it's own plugin. This allows it to be updated outside of the theme and deploy new features as needed.

The code was written by Tracy Levesque and Mika Epstein, with assistance from [Yikes!](https://YikesInc.com)

Usage documentation can be found at [docs.lezwatchtv.com](https://docs.lezwatchtv.com)

- Uses Node to install and manage components, as well as to build final versions of Blocks.
- Uses Composer for adding project dependencies.
- Includes automated build and deploy pipelines to servers using Github actions

## Requirements

- PHP 8.1 or higher
- [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) version 16+

It's recommended to use [Homebrew](https://brew.sh) on macOS or [Chocolatey](https://chocolatey.org) for Windows to install the project dependencies.

### Code Editor and Git Client

This repository includes a list of suggested extensions for the [Visual Studio Code editor](https://code.visualstudio.com) and Xdebug support in the `.vscode` directory.

A user-friendly Git client such as [GitHub Desktop](https://desktop.github.com) or [Tower](https://www.git-tower.com/mac) enables smaller commits and simplifies merge conflict resolution.

## Setup 🛠

1. Clone this repository: `git clone git@github.com:lezwatch/lwtv-plugin`
2. Move into the project directory: `cd lwtv-plugin`
3. Install the project dependencies: `npm install`
4. Run an initial build: `npm run build`

## Contributing

All code must pass through the `development` branch which is kept up tp date. As such, any pull requests should be made to **development**, which will push the code automatically to our development server for testing.

1. Using the `development` branch as base, create a new branch with a descriptive name like `fixing-charts` or `fix/chartjs421` or `feature/latest-posts` . Commit your work to that branch until it's ready for full testing
2. Open [a pull request](https://help.github.com/en/desktop/contributing-to-projects/creating-a-pull-request) from your feature branch to the `development` branch.
3. If you are not a main developer, your pull request will be reviewed before it can be merged. If there are issues or changes needed, you may be asked to do so, or they may be done for you.
4. When the code passes review, it will be merged into the `development` branch and can be tested on the dev server.
5. Once the code passes tests, the `development` branch will be merged into `production` and the job is done!

To install and update:

* `$ npm install` - Install all the things.
* `$ npm update` - Updates all the things.
* `$ npm run build` - Builds all the things for production.

All commits are linted automatically on commit via eslint and phpcs, to ensure nothing breaks when we push the code.

### Libraries

JS and PHP libraries are included via NPM and Composer. WordPress plugins that have been forked are now included in the main code and managed by us to prevent breakage.

The `vendor` and `node_module` files are not synced to Github anymore (as of 2023-August) to minimize the amount of files stored on the servers, and the following libraries have their required code moved via Composer and npm's post-install process:

**NPM**
* [ChartJS](https://github.com/chartjs/Chart.js/)
* [TableSorter (Mottie Fork)](https://github.com/Mottie/tablesorter)

**Composer**
* [ICal Parser](https://github.com/u01jmg3/ics-parser)

### Deployment

Pushes to branches are automatically deployed via Github Actions as follows:

* Development: [lezwatchtvcom.stage.site](https://lezwatchtvcom.stage.site) (password required - Ask Mika)
* Production: [lezwatchtv.com](https://lezwatchtv.com)

## Features

The follow is a description of all the files in the code and more or less what they do.

The main file `functions.php` calls all other needed files.

Defines:

* `FIRST_LWTV_YEAR` - The first year of known queers on TV (if not otherwise defined). Currently 1961.

### Admin Panels

Stored in `/admin/` -- Makes admin panels

* `_main.php` - Loader file and mapping
* `dashboard.php` - Powers wp-admin dashboard code
* `exclusions.php` - Lists any exclusions or overrides applied by admins (i.e. forcing someone to show as queer)
* `monitors.php` - Services monitored (runs daily)
* `validation.php` - Data consistency checks (each one runs weekly)

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
* `/scores/` - Logos and images used for show grades

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

Blocks for Gutenberg. The file `_main.php` acts as an autoloader.

* `calendar.php` - Calendar specific code
* `serverside.php` - Server-side Renders: Run PHP code in JS blocks

_Source (`src`)_

Development is fully documented in `/blocks/README.md`

Each block is broken up by folder with the following files:

* `block.js` - Main block caller
* `block.json` - Schema definition file
* `/css/editor.scss` - Style for Editor
* `/css/style.scss` - Style for Front end
* `/js/edit.js` - Editor code
* `/js/save.js` - Save code
* `/js/components/` - (optional) Components used blocks

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

### Debugger

Stored in `/debugger/` -- a collection of all code used to debug and manage content.

* `_main.php` - Generalized features and calls all sub files
    - `sanitize_social()` - Sanitizes and validates social media.
    - `format_wikidate()` - Formats dates for and from WikiData
    - `validate_imdb()` - Validates IMDb slugs
* `actors.php` - Find all problems with Actor pages.
* `characters.php` - Find all problems with Character pages.
* `queers.php` - Find all problems with Queer data (i.e. are actors queer, are characters played by queer actors)
* `shows.php` - Find all problems with Show pages.

### Features

Stored in `/features/` -- a collection of miscellaneous features.

* `/ICal` - Subfolder for the ICal Parser Library
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
* `dashboard-posts-in-progress.php` - Forked version of a plugin to show in progress posts
* `dashboard.php` - Custom column for featured images in posts lists and removal of self pings
* `embeds.php` - Embeds DisneyABCPress videos, Gleam, GoFundMe, Indiegogo
* `grading.php` - Build and format array for displaying show scores including 3rd parties.
* `ics-parser.php` - Connection to the ICS Parser for calendar data.
* `languages.php` - Support for multiple languages in a dropdown (used by Shows for alt show names)
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
* `spammers.php` - Prevent Spammers from annoying us.
* `transients.php` - Wrapper code for Transients so when you dev-test, you get updated content.
* `upgrades.php` - Handle upgrades of WP and everything included.

### Node Scripts

Stored in `/node_scripts/` -  Scripts used by NPM (for anything in `node_modules`). _This is removed by the builder script when pushed to production._

* `postinstall.js` - script run at the end of NPM to move files to the correct location.

### Of The Day

Stored in `/of-the-day/` - code used to generate character and show of the day.

* `_main.php` - generates custom SQL table to store 'of the day' records
* `rss.php` - Generates custom RSS feed for of-the-day (`/feeds/otd/`)
* `template.php` - Backup of theme template.

### Plugin Addons

Stored in `/plugins/` - all files are used to enhance other plugins installed on the site.

The file `_main.php` acts as an autoloader.

* `cache.php` - Custom Cache specific to DreamPress hosting
    - Generates data used by Proxy Cache Purge to know what to flush.
* `cmb2.php` - Integration with CMB2
    - calls other files
* `/cmb2/` - CMB2 add on libraries
    - `cmb2-field-select2/` - Updated version of field select 2.
    - `cmb2-attached-posts/` - CMB2 attached posts (HEAVILY forked)
    - `cmb2-grid/` - CMB2 Grid Display
    - `attached-posts.php` - Custom code for Attached Posts
    - `cmb2.css` - Custom CSS
    - `meta-by-role.php` - Only allow people with certain roles to edit certain post meta.
    - `metabox-profiles.php` - Add custom metaboxes for user profiles
    - `symbolicons.php` - Symbolicon integration for taxonomies.
    - `taxonomies.php` - Custom filters for taxonomies so they display properly in meta boxes.
    - `year-range.php` - Year Range -- 'date_year_range' custom field type
* `comment_probation.php` - Fork of abandoned plugin
* `facetwp.php` -- Facet WP
    - calls other files
    - Reset Shortcode
* `/facetwp/` - FacetWP Folder
    - `/facetwp-cmb2/` - FacetWP Integration with CMB2 (forked)
    - `indexing.php`
        - filter Data before it's saved to rename values (capitalization)
        - split actors and shows into separate entries, and add additional orderby params
    - `pagination.js` - Pagination Scrolling and Refresh Warning
    - `pagination.php` - Only show pagination if there's more than one page
* `gravity-forms.php` - Gravity Forms Integration
    - Prevents views from being counted
    - calls other files
* `/gravity-forms/` - Gravity Forms Folder
    - `class-gf-approvals.php` - Approval Code (forked from another plugin)
    - `stop-spammers.php` - Block Spammers
* `gutenslam.php` - make Block Editor stop being such a dillhole and forget preferences
* `jetpack.php`  - Jetpack integration
    - Adds Post Type to sort.
    - Show Feedback in "Right Now"
    - Custom Icon for Feedback in "Right Now"
    - Mark feedbacks as having been answered
    - Protection from spammers via disallowed keys
* `related-posts-by-taxonomy.php` - Filters to allow a custom layout for "Shows Like This"
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
    - `const DATA_CLASS_MATCHER` - Array of data types to classes
    - `const FORMAT_CLASS_MATCHER` - Array of format types to classes
    - `const META_PARAMS` - Array of custom params for meta data searches
    - `function enqueue_scripts()` - Enqueues scripts selectively
    - `function generate()` - Generates base stats. This makes a lot of calls to arrays and outputs
    - `function build_array()` - Builds array
    - `function build_output()` - Builds formatted output
    - `function maybe_year()` - Determine if the data is a valid year.
    - `function maybe_complex()` - Deep Dive for custom data that is extra complex.
    - `function count_shows()` - Slices shows into smaller chunks (i.e 'all shows in Australia') and can output raw counts, on-air counts, scores, or on-air scores.
* `gutenberg-ssr.php` - Gutenberg Server side rendering to show stats
* `query_vars.php` - Query Variables customization (to make virtual pages) and Yoast meta

_Build (`/build/`)_

Each file has a `generate()` function which build an array that will be passed to the formatter code and ouput.

* `class-actor-char-dead.php` - Stats for dead character per actor.
* `class-actor-chars.php` - How many actors or characters per actor or character...
* `class-complex-taxonomy.php` - How many characters are played by out queer actors, but also how many characters for each term.
* `class-dead-basic.php` - Simple counts of all shows with dead, or all dead characters
* `class-dead-complex-taxonomy.php` - Complex death taxonomies for stations and nations.
* `class-dead-meta-tax.php` - Generate array to parse taxonomy content as it relates to post metas (for dead characters)
* `class-dead-role.php` - Array for dead characters by role (regular, etc)
* `class-dead_shows.php` - Array of shows with (and without) dead characters, but because of Sara Lance, we have to cross relate to make sure all the shows with death have actually dead characters (yes, a show can have a dead-flag but no actively dead characters)
* `class-dead-taxonomy.php` - Taxonomy Array for dead characters
* `class-dead-year.php` - Simple counts of death by year (Sara Lance...)
* `class-meta.php` - Generate array to parse post meta data
* `class-on_air.php` - Shows or characters on air per year
* `class-scores.php` - Show Scores
* `class-show-roles.php` - Roles of characters on Shows, with how many of each role are dead
* `class-taxonomy.php` - Parse taxonomy content
* `class-taxonomy-breakdowns.php` - generates complex arrays of cross related data from multiple taxonomies to list 'all miniseries in the USA' (this one makes us cry)
* `class-this-year.php` - Generate this year data
* `class-yes-no.php` - Generates data for content that has Yes/No values (shows we love, on air)

_Formats (`/formats`)_

Each file has a `build()` function which formats the arrays build in the `build` section (above) for proper display.

* `class-averages.php` - Averages, highs, and lows (ex show scores)
* `class-barcharts.php` - Horizontal barcharts
* `class-barcharts-stacked.php` - Stacked Barcharts (also horizontal)
* `class-lists.php` - Table lists with simple counts
* `class-percentages.php` - Table lists with percentages and a bar
* `class-piecharts.php` - Piecharts (actually donuts...)
* `class-trendline.php` - Trendlines (against a vertical barchart)
    - `calculate_trendline()` - Calculates trendline data
    - `linear_regression()` - Calculates linear regression

_Templates (`/templates/`)_

Templates used by the shortcodes and Gutenberg (as well as when included on the pages themselves).

* `actors.php` - Actor stats
* `characters.php` - Character stats
* `death.php` - Death stats
* `formats.php` - Formats (tv series, web series, etc) stats
* `main.php` - Main stats page
* `nations.php` - Nation stats
* `post_type_actors.php` - Partial for showing the character stats for a single actor
* `shows.php` - Show statistics
* `stations.php` - Networks/Stations statistics

### This Year

Stored in `/this-year/` - Technically a subset of statistics, This Year shows you just the data for the indicated year.

* `_main.php` - Basic data loading, calls templates etc.
* `characters.php` - all data on characters per year
* `query_vars.php` - customize query variables
* `shows.php` - all data on shows per year

### Ways to Watch

Stored in `/ways-to-watch/` -- Code to customize Ways to Watch links and add affiliate data, or alter display names.

* `_main.php` - Loader file.
* `global.php` - All global data, such as header/meta and content regex.
* `ways-to-watch.php` - Affiliate links and pretty-fication of services

* `/images/` - Images used by local promotions.

## WP-CLI

Stored in `/wp-cli/` -- All code for WP-CLI

* `_main.php` - Loader file.
* `calc.php` - Calculations on content (scores, character count, etc) - `wp lwtv CALC [ID]`
* `check.php` - Data validation checkers - `wp lwtv CHECK [queerchars|wiki] [id]`
* `generate.php` - Generate custom content - `wp lwtv GENERATE [otd|tvmaze]`

## Developer Features

The following folders/files are for use by Developers. They are not pushed to the dev nor production servers.

* `./.github/` - all Github specific files such as workflows, dependabot, and pull request templates
* `/.husky/` - all Husky commands
* `/.vscode/` - default VSCode settings
* `.editorconfig` - Basic editor configuration
* `.gitignore` - Files and folders to be exempt from Git syncs
* `.npmrc` - NPM configuration requirements
* `.nvmrc` - NVM version control
* `composer.json` - Composer settings, includes all libraries used
* `package-lock.json` - Saved package.json data
* `package.json` - NPM configuration, commands, and libraries used
* `phpcs.xml.dist` - PHPCS configuration
