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