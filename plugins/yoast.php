<?php

/* Enable Yoast SEO sitemap caching
 * Last Tested: Mar 27 2019 using Yoast SEO 10.0.1 on WordPress 5.1.1
 * Credit: Yoast development team
 *********
 * Please note that changes will be applied upon next sitemap update.
 * To manually create the cached sitemap, please disable and enable the sitemaps then load each sitemap.
 * Once cached, Yoast SEO will update sitemap cache as needed.
 */

add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_true' );
