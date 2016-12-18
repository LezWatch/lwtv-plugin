<?php
/*
Plugin Name: Google Analytics
Plugin URI:  http://lezwatch.com
Description: Google Anlytics without a plugin
Version: 1.0
Author: Mika Epstein
*/

// Google Analytics

add_action('wp_footer', 'add_lez_googleanalytics');
function add_lez_googleanalytics() {
	?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-3187964-11', 'auto');
  ga('send', 'pageview');

</script>

	<?php
}

// Google Analytics
add_filter( 'amp_post_template_analytics', 'add_amp_lez_googleanalytics' );
function add_amp_lez_googleanalytics( $analytics ) {
    if ( ! is_array( $analytics ) ) {
        $analytics = array();
    }

    // https://developers.google.com/analytics/devguides/collection/amp-analytics/
    $analytics['helf-googleanalytics'] = array(
        'type' => 'googleanalytics',
        'attributes' => array(
            // 'data-credentials' => 'include',
        ),
        'config_data' => array(
            'vars' => array(
                'account' => "UA-3187964-11"
            ),
            'triggers' => array(
                'trackPageview' => array(
                    'on' => 'visible',
                    'request' => 'pageview',
                ),
            ),
        ),
    );

    return $analytics;
}