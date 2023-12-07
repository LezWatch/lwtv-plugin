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
* `$ npm run updater` - Updates all the things (npm, composer, etc).
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

## Development

As of 2024, we use Namespaces and auto-loading in order to properly generate and call new content dynamically.

### Components

A _component_ can be thought of as a _sub-plugin_. It is an atomic, independent module that stores business logic related to a specific feature. It may expose some template tags (functions) that can be called from within the theme.

All Components are stored in `/php/` and the details of their use in `/php/readme.md`

### Blocks

Development is fully documented in `/php/blocks/README.md`

## Features

The follow is a description of all the files in the code and more or less what they do.

* `functions.php` - Main Plugin file. Loads auto-loader, components, and defines.
* `/assets/` - Plugin assets
* `/bin/` - Scripts used for PHP Unit Testing
* `/node_scripts/` - Scripts used by Node to properly move content.
* `/php/` - All functional code
* `/plugins/` - Forked 3rd party plugins
* `/tests/` - Unit Tests

### Assets

Stored in `/assets/`

_CSS (`css`)_

* `author-box.css` - CSS for the Author Box (will be moved eventually)
* `cmb2.css` - CMB2 Styling
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

## The Code

Stored in `/php/`

* `class-plugin.php` - Main plugin file. All new components will be added there

### _Components

All top-level components, stored in `/php/_components/` - Many components have 'sub-components' stored in a folder of the same name. For example, `class-queeries.php` calls code stored in `/php/queeries/` and so on. As long as the proper namespaces are applied, they will be autoloaded.

* `class-admin-menu.php` - LWTV custom admin menu
* `class-blocks.php` - Gutenberg Blocks
* `class-calendar.php` - Calendar
* `class-cpts.php` - Custom Post Types
* `class-dashboard-widgets.php` - Dashboard Widgets
* `class-debugger.php` - Debugger
* `class-features.php` - General features
* `class-grading.php` - Show Scores / Grading
* `class-of-the-day.php` - Of The Day RSS
* `class-plugins.php` - Plugin filters and actions
* `class-queeries.php` - Customized loops
* `class-rest-api.php` - Rest API code
* `class-statistics.php` - Statistics
* `class-symbolicons.php` - Symbolicons
* `class-theme.php` - Theme code
* `class-this-year.php` - This Year features
* `class-wp-cli.php` - WP CLI code
* `class-transients.php` - Transients

* `interface-component.php` - Interface for classes that act as plugin components.
* `interface-templater.php` - Interface for classes that return template functions.

### Admin Menu

Stored in `/php/admin-menu/` -- Makes admin menu items

* `class-exclusions.php` - Lists any exclusions or overrides applied by admins (i.e. forcing someone to show as queer)
* `class-monitors.php` - Services monitored (runs daily)
* `class-validation.php` - Data consistency checks (each one runs weekly)

### Validator

Stored in `/php/validator/` -- Called by `/php/admin-menu/class-validation.php`

* `class-actor-checker.php` - Check actors for valid data
* `class-actor-empty.php` - Check actors empty required data
* `class-actor-imdb.php` - List actors missing IMDb
* `class-actor-wiki.php` - Check actors wiki data (NOT CURRENTLY USED)
* `class-character-checker.php` - Check characters for valid data
* `class-queer-checker.php` - Check that all characters for queer actors are marked properly
* `class-show-checker.php` - Check shows for valid data
* `class-show-imdb.php` - List shows missing IMDb
* `class-show-urls.php` - Check Ways-To-Watch URLs

### Blocks (aka Gutenberg)

Stored in `/php/blocks/`

* `class-serverside.php` - Server-side Renders: Run PHP code in JS blocks

_Source (`src`)_

Each block is broken up by folder with the following files:

* `block.js` - Main block caller
* `block.json` - Schema definition file
* `/css/editor.scss` - Style for Editor
* `/css/style.scss` - Style for Front end
* `/js/edit.js` - Editor code
* `/js/save.js` - Save code
* `/js/components/` - (optional) Components used blocks

### Calendar

Stored in `/php/calendar/`:

* `/ICal` - Subfolder for the ICal Parser Library
* `class-blocks.php` - Code called by the Blocks
* `class-ics-parser.php` - Connection to the ICS Parser for calendar data.
* `class-names.php` - Process TV Maze names.

### Custom Post Types

Stored in `/php/cpts/`:

* `class-actors.php` - Actor CPT code
* `class-characters.php` - Actor CPT code
* `class-post-meta.php` - Registers post-meta so it can be properly used and hidden from Gutenberg
* `class-related-posts.php` - Displays related shows and posts (based on tags)
* `class-shows.php` - Show CPT code

_Actors (`actors`)_

* `class-calculations.php` - Does the math for various data points used to generate show scores, stores count of number of characters for faster retrieval later
* `class-cmb2-metaboxes.php` - CMB2 code to make display nicer
* `class-custom-columns.php` - Define columns for post listing

_Characters (`/characters/`)_

* `class-calculations.php` - Does the math for various data points used to generate show scores
* `class-cmb2-metaboxes.php` - CMB2 code to make display nicer
* `class-custom-columns.php` - Define columns for post listing

_Shows (`/shows/`)_

* `class-calculations.php` - Show score math
* `class-cmb2-metaboxes.php` - CMB2 code to make display nicer
* `class-custom-columns.php` - Define columns for post listing
* `class-shows-link-this.php` - Calculations for 'shows like this' (uses [Related Posts by Taxonomy](https://wordpress.org/plugins/related-posts-by-taxonomy/))

### Debugger

Stored in `/php/debugger/` -- a collection of all code used to debug and manage content.

* `class-actors.php` - Find all problems with Actor pages.
* `class-characters.php` - Find all problems with Character pages.
* `class-queers.php` - Find all problems with Queer data (i.e. are actors queer, are characters played by queer actors)
* `class-shows.php` - Find all problems with Show pages.

### Features

Stored in `/php/features/` -- a collection of miscellaneous features.

* `class-cron.php` - Custom cron jobs to pre-cache high-traffic pages, and run regular jobs
* `class-dashboard-posts-in-progress.php` - Forked version of a plugin to show in progress posts
* `class-dashboard.php` - Custom column for featured images in posts lists and removal of self pings
* `class-embeds.php` - Embeds DisneyABCPress videos, Gleam, GoFundMe, Indiegogo
* `class-languages.php` - Support for multiple languages in a dropdown (used by Shows for alt show names)
* `class-private-posts.php` - Shows alert that a page is hidden if the post is set private.
* `class-roles.php` - Custom roles created for curation of content
* `class-shortcodes.php` - Various shortcodes - mostly deprecated, but some used by blocks.
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
* `class-spammers.php` - Prevent Spammers from annoying us.
* `class-upgrades.php` - Handle upgrades of WP and everything included.
* `class-user-profiles.php` - Custom profile code

### Of The Day

Stored in `/php/of-the-day/` - code used to generate character and show of the day.

* `template.php` - Backup of theme template.

### Plugin Addons

Stored in `/php/plugins/` - all files are used to enhance other plugins installed on the site.

The file `_main.php` acts as an autoloader.

* `class-cache.php` - Custom Cache specific to DreamPress hosting
    - Generates data used by Proxy Cache Purge to know what to flush.
* `class-cmb2.php` - Integration with CMB2
    - calls other files
* `/cmb2/` - CMB2 add on libraries
    - `class-attached-posts.php` - Custom code for Attached Posts
    - `class-meta-by-role.php` - Only allow people with certain roles to edit certain post meta.
    - `class-metabox-profiles.php` - Add custom metaboxes for user profiles
    - `class-symbolicons.php` - Symbolicon integration for taxonomies.
    - `class-taxonomies.php` - Custom filters for taxonomies so they display properly in meta boxes.
    - `class-year-range.php` - Year Range -- 'date_year_range' custom field type
* `class-comment-probation.php` - Fork of abandoned plugin
* `class-facetwp.php` -- Facet WP
    - calls other files
    - Reset Shortcode
* `/facetwp/` - FacetWP Folder
    - `class-indexing.php`
        - filter Data before it's saved to rename values (capitalization)
        - split actors and shows into separate entries, and add additional orderby params
    - `class-pagination.php` - Only show pagination if there's more than one page
* `class-gravity-forms.php` - Gravity Forms Integration
    - Prevents views from being counted
    - calls other files
* `/gravity-forms/` - Gravity Forms Folder
    - `class-gf-approvals.php` - Approval Code (forked from another plugin)
    - `class-stop-spammers.php` - Block Spammers
* `class-gutenberg.php` - make Block Editor stop being such a dillhole and forget preferences
* `class-jetpack.php`  - Jetpack integration
    - Adds Post Type to sort.
    - Show Feedback in "Right Now"
    - Custom Icon for Feedback in "Right Now"
    - Mark feedbacks as having been answered
    - Protection from spammers via disallowed keys
* `class-related-posts-by-taxonomy.php` - Filters to allow a custom layout for "Shows Like This"
* `class-yoast.php` - Custom Yoast controls

### Queeries

Stored in `/php/queeries/` - Custom arrays and WP_Query calls that are repeated in multiple places.

* `class-is-actor-queer.php` - Determine if actor is queer (yes/no)
* `class-is-actor-trans.php` - Determine if an actor is trans (yes/no)
* `class-is-show-on-air.php` - Determine if a show is on air or not (bool)
* `class-post-meta-query.php` - Post Meta Array returns the whole post data. Can handle compares and likes
* `class-post-type-query.php` - Generate an object of all posts in a specific post type.
* `class-post-meta-and-tax-query.php` - Generate an array of posts that have a specific post meta AND a specific taxonomy value. Useful for getting a list of all dead queers who are main characters (for example).
* `class-related-posts-by-tag.php` - Related Posts by Tags.
* `class-tax-two.php` - Taxonomy Two Array is used for generating a query of posts that are in two taxonomies
* `class-taxonomy.php` - Taxonomy Array
* `class-wp-meta-query.php` - SQL version of meta query _(not currently used)_

### Rest API

Stored in `/php/rest-api/` - These files generate the REST API output.

* `class-alexa-skills.php` - Builds the basic Alexa API (see also Alexa Skills section below)
* `class-byq.php` -  LezWatch.TV Plugin (formerly Bury Your Queers)
    - Last Death - "It has been X days since the last WLW Death"
    - On This Day - "On this day, X died"
    - When Died - "X died on date Y"
* `class-export-json.php` - Export content in JSON format. Mostly used for WikiData and Universities.
* `class-fresh-json.php` - Generates 'whats new' content
* `class-imdb-json.php` - API to communicate with IMDb and generate information (used by Alexa)
* `class-list-json.php` - Generates lists
* `class-otd-json.php` - X Of The Day API service. Every 24 hours, a new character and show of the day are spawned
* `class-shows-like-json.php` - Similar shows.
* `class-stats-json.php` - JSON API version of the stats (mostly)
* `class-this-year-json.php` - Outputs simplified lists of what happened in a given year.
* `class-what-happened-json.php` - Outputs data based on what happened in a given year, year-month, or specific day.
* `class-whats-on-json.php` - What's on TV tonight (or tomorrow).

_Alexa Skills (`/php/rest-api/alexa/`)_

* `class-common.php` - Code used by multiple Alexa skills
* `class-validate.php` - Validates the requests as coming from Amazon
* `class-byq.php` - Old BYQ code
* `class-flash-brief.php` - Since the flash brief has trouble with media in post content, we've made our own special version.
* `class-newest.php` - Generate the newest shows or characters (or deaths)
* `class-shows.php` - Skills for interactions with shows (similar shows, recommended shows, etc.)
* `class-this-year.php` - Gives you an idea how this year is going...
* `class-whats-on.php` - Generates what's on TV stuff.
* `class-who-are-you.php` - Runs all code that discusses actors, characters, and shows.

_Templates (`/php/rest-api/templates/`)_

* `export-json.php` - uses var query data to determine what to show.

### Statistics

Stored in `/php/statistics/` - These files generate everything for stats, from graphs to the rest API stuff.

* `class-matcher.php` - Data Matcher
    - `const BUILD_CLASS_MATCHER` - Array of data types to classes
    - `const FORMAT_CLASS_MATCHER` - Array of format types to classes
    - `const META_PARAMS` - Array of custom params for meta data searches
* `class-gutenberg-ssr.php` - Gutenberg Server side rendering to show stats
* `class-query_vars.php` - Query Variables customization (to make virtual pages) and Yoast meta
* `class-the-array.php` - Builds the array via `make()`
* `class-the-output.php` - Builds the output `make()`

_Build (`/php/statistics/build/`)_

Each file has a `make()` function which build an array that will be passed to the formatter code and output.

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

_Formats (`/php/statistics/formats`)_

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

_Templates (`/php/statistics/templates/`)_

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

### Theme

Stored in `/php/theme/` - Code used to generate data for the theme in weird ways. Each class file has a `make()` function that generates the output. Some have sub-sets.

* `class-actor-birthday.php` - Boolean return if an actor is having a birthday.
* `class-content-warning.php` - If a show has a content warning, we display it/
* `class-data-actor.php` - Generate custom data for actors
    - `function age()` - Generate and return actor age
    - `function characters()` - Generate and return characters played by actor
    - `function dead()` - Generate and return all DEAD characters played by actor
    - `function terms()`  - Generate and return term data related to actor (gender, pronouns, etc)
* `class-data-author.php` - Generate and return data for Authors (Mika, Tracy, Etc.)
    - `function social()` - Social Media for authors
    - `function favorite_shows()` - Favorite Showes
* `class-data-character.php` - Generate character Data
    - `function actors()` - Generate data for Actors
    - `function cliches()` - Generate data for Clichés
    - `function dead()` - Generate data for Dead characters
    - `function oneactor()` - Generate data for PRIMARY Actor
    - `function oneshow()` - Generate data for PRIMARY show
    - `function shows()` - Generate data for all shows a character is on
    - `function terms()`  - Generate and return term data related to character (gender, pronouns, etc)
* `class-list-characters.php` - Generate and return list of Characters from a show
* `class-show-stars.php` - Make show stars
* `class-stats-symbolicon.php` - Makes the icon/title for symbolicons
* `class-taxonomy-archive-title.php` - Customize title of archives with pretty icons
* `class-tvmaze.php` - Calls to TVMaze
    - `episodes()` - output of next ep for TV Maze
* `class-ways-to-watch.php` - Outputs Ways to Watch

### This Year

Stored in `/php/this-year/` - Technically a subset of statistics, This Year shows you just the data for the indicated year.

* `class-display.php` - Controls individual pages
    - `function navigation()` - Builds the nav footer shown in `display()`
* `class-generator.php` - Wrapper to generate content and arrays
* `class-the-array.php` - Builds the array
* `class-the-output.php` - Builds the pretty output

_Build (`/php/this-year/build/`)_

Each file has a `make()` function which build an array that will be passed to the formatter code and output.

* `class-characters-dead.php` - Generates data for dead characters
* `class-characters-list.php` - Generates data for all characters
* `class-overview.php` - Builds the data for the overview page
* `class-shows-list.php` - Generates data for all show pages

_Formats (`/php/this-year/formats`)_

Each file has a `make()` function which formats the arrays build in the BUILD section (above) for proper display.

* `class-chart.php` - Outputs charts (currently only used on the default page)
* `class-dead.php` - Outputs dead content
* `class-default.php` - Outputs front page of this-year
* `class-shows.php` - Outputs all show pages dynamically

### WP-CLI

Stored in `/wp-cli/` -- All code for WP-CLI

* `cli-calc.php` - Calculations on content (scores, character count, etc) - `wp lwtv CALC [ID]`
* `cli-check.php` - Data validation checkers - `wp lwtv CHECK [queerchars|wiki] [id]`
* `cli-generate.php` - Generate custom content - `wp lwtv GENERATE [otd|tvmaze]`

### Tests

Stored in `/tests/ ` -- Unit/Functionality Tests

* `bootstrap.php` - Boostrapper
* `test-sample.php` - Example
* `test-ways-to-watch.php` - Ways to Watch

### Node Scripts

Stored in `/node_scripts/` -  Scripts used by NPM (for anything in `node_modules`). _This is removed by the builder script when pushed to production._

* `postinstall.js` - script run at the end of NPM to move files to the correct location.

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
