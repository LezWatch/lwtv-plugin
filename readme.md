## LezWatch Commercials Plugin

Custom plugin for LezWatch.TV

## Description

The LezWatch.TV plugin contains custom code for use with the site. Instead of having everything hardcoded into the theme, or shoved in a shared MU Plugin, it was moved to it's own plugin. This allows it to be updated outside of the theme and deploy new features as needed.

The code was written by Tracy Levesque and Mika Epstein, with assitance from [Yikes Inc.](YikesInc.com)

## Features

The main file `functions.php` calls all other needed files.

Defines `FIRST_LWTV_YEAR` - The first year of known queers on TV (if not otherwise defined). Currently 1961.

### Amazon Affiliates

Auto Adds in Amazon Affiliate Code. This calls the ApaiIO library (from (LezWatch Library)[https://github.com/LezWatch/lezwatch-library]) to generate ads related to content on page.

### Assets

Stored in `/assets/`

_Images (`images`)_

* toaster - A toaster icon (used when no featured image is found)
* unicorn - Unicorn (used if a toaster cannot be loaded)

_Javascript (`js`)_

* Chart.bundle.min.js - Stat charts

### Custom Post Types

Stored in `/cpts/`:

* Actors: `actors.php` and `/actors/` - Actor Post Type and related Taxonomies
* Characters: `characters.php` - Character Post Type and related Taxonomies
* All: `all-cpts.php` - Rebranding featured images for CPTs
* Shows: `shows.php`, `shows.css`, and `/shows/` - Show Post Type and related Taxonomies

Actors and Shows have custom code to generate statistics and flush related Varnish pages when Characters are saved.

### Cron

In file: `cron.php`

Custom cron jobs to load high-traffic pages and ensure Varnish is cached.

### Custom Loops

In file: `custom-loops.php`

Custom arrays and WP_Query calls that are repeated in multiple places.

* Determine if actor is queer
* Taxonomy Array
* Taxonomy Two Array - used for generating a query of posts that are in two taxonomies
* Post Meta Array
* WP Meta Query
* Post Type Array - Generate an array of all posts in a specific post type.
* Post Meta AND Taxonomy Query - Generate an array of posts that have a specific post meta AND a specific taxonomy value. Useful for getting a list of all dead queers who are main characters (for example).
* Related Posts by Tags.

### Plugin Addons

Stored in `/plugins/`

* CMB2: `/cmb2.php` 
    - calls other files 
    - generates a CB2 formatted list of terms
* CMB2 Folder - `/cmb2/`
    - Select2: `/cmb-field-select2/` - CMB2 Field Type: Select2
    - Grid: `/CMB2-grid/` - A grid display system
    - Custom CSS: `/cmb2.css` 
    - Year Range: `/year-range.php` - 'date_year_range' custom field type
    - LWTV: `/lwtv.php` - Favorite shows for author profiles, Symbolicon support
* FacetWP - `/facet.php`
    - calls other files
    - Only show pagination if there's more than one page
    - Reset Shortcode
* FacetWP Folder - `/facet/` 
    - CMB2: `/cmb2.php` - FacetWP Integration with CMB2
    - LWTV: `/lwtv.php` 
        - filter Data before it's saved to rename values (capitalization)
        - split actors and shows into separate entries, and add additional orderby params
* Yoast's WordPress SEO - `/yoast-seo.php`
    - Remove stopword removal from shows and characters CPTs

### Query Variables

Custom Query Variables that let us have the following special pages

* role
* statistics
* this-year

### Rest API

Stored in `/rest-api/` - These files generate the REST API output.

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
* Of The Day - `/of-the-day.php`
    - The code that runs the X Of the Day API service. Every 24 hours, a new character and show of the day are spawned
* Statistics - `/stats.php`
    - JSON API version of the stats (mostly)
* What Happened - `/what-happened.php`
    - Outputs data based on what happened in a given year.

### Search

Extra Search Functions:

* Join posts and postmeta tables
* Modify Search Location to include custom fields
* Force search to be distinct and prevent duplicates
* Pretty Permalinks for Search

### SEO

Adds in a custom OpenGraph image for taxonomies, as they don't accept SVGs for some reason.

### Shortcodes

Various shortcodes used on LezWatch.TV

* Display The first year we had queers: `[firstyear]`
* Display This Month recap: `[thismonth]` or `[thismonth date="2017-01"]`
* Display screener information: `[screener title="Some Like It Hot" summary="A quirky black and white romcom" queer="2" worth="yes" trigger="low"]`

### Sort Stop Words

Filter post order by for shows to NOT include the/an/a when sorting.

### Statistics

Stored in `/statistics/` - These files generate the REST API output.

The basic defines for all stats pages.

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
    - Show scores
    - Actors/Characters
    - Roles on Shows
    - Queerness
* Display: `class LWTV_Stats_Display` - `display.php`
    - Determine icon for each stats page
    - Determine Title for each stats page
    - Determine archive intro for each stats page
    - Determine display content for each stats page
* Output: `class LWTV_Stats_Output` - `output.php`
    - Lists
    - Percentages
    - Averages
    - Linear regression function
    - Barcharts
    - Stacked Barcharts
    - Piecharts
    - Trendlines

## Deployment

Pushes are automatically deployed via Codeship to:

* Master: lezwatchtv.com
* Development: lwtv.dream.press