## LezWatch Commercials Plugin

Custom plugin for LezWatch TV

## Description

The LezWatch TV plugin contains custom code for use with the site. Instead of having everything hardcoded into the theme, or shoved in a shared MU Plugin, it was moved to it's own plugin. This allows it to be updated outside of the theme and deploy new features as needed.

## Features

The main file `functions.php` calls all other needed files.

### Assets

* /js/                   - Javascript Files
* Chart.bundle.min.js    - Stat charts


### Custom Loops

Custom arrays and WP_Query calls that are repeated in multiple places.

* Taxonomy Array
* Taxonomy Two Array - used for generating a query of posts that are in two taxonomies
* Post Meta Array
* WP Meta Query
* Post Type Array
* Post Meta AND Taxonomy Query
* Related Posts by Tags.

### Custom Post Types

Stored in `/cpts/`:

* Character Post Type and related Taxonomies
* Rebranding featured images for CPTs
* Show Post Type and related Taxonomies

### Plugin Addons

Stored in `/plugins/`

* CMB2: Custom CSS and Symbolicons
* FacetWP: Filter Data before it's saved to rename values (capitalization), split actors and shows into separate entries, and add additional orderby params
* Yoast's WordPress SEO: Remove stopword removal from shows and characters CPTs

### Query Variables

Custom Query Variables that let us have the following special pages

* newest
* role
* statistics
* this-year

### Rest API

Stored in `/rest-api/` - These files generate the REST API output.

* Alexa Skills
* Bury Your Queers
* Statistics

### Search

Extra Search Functions:

* Join posts and postmeta tables
* Modify Search Location to include custom fields
* Force search to be distinct and prevent duplicates
* Pretty Permalinks for Search

### SEO

Adds in a custom OpenGraph image for taxonomies, as they don't accept SVGs for some reason.

### Sort Stop Words

_Not currently in use_. This screws up non-asc/desc sorts.

Filter post order by for shows to NOT include the/an/a when sorting.

### Statistics

The basic defines for all stats pages.

In `class LWTV_Stats`

* Generate: Statistics Base Code
* Statistics Taxonomy Array
* Statistics Taxonomy Array for DEAD
* Statistics Array for DEAD by ROLE
* Statistics Meta and Taxonomy Array
* Statistics Meta Array
* Statistics Death By Year
* Statistics Death on Shows
* Statistics Roles on Shows
* Statistics Display Lists
* Statistics Display Percentages
* Statistics Display Average
* linear regression function
* Statistics Display Barcharts
* Statistics Display Piecharts
* Statistics Display Trendlines

In `class LWTV_Stats_Display`

* Determine icon for each stats page
* Determine Title for each stats page
* Determine archive intro for each stats page
* Determine display content for each stats page


## Deployment

Pushes to the `master` branch are automatically deployed via Codeship to:

* lezwatchtv.com
* lwtv.dream.press