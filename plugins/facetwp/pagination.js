// Pagination Scrolling
//
// Source: https://facetwp.com/how-to-add-pagination-scrolling/
//
// FacetWP is ajax-based, meaning pagination happens without a full page reload. 
// The trade-off is that the browser won’t automatically scroll to the top of 
// the page upon pagination. This can be achieved with a bit of custom js.

(function($) {
    $(document).on('facetwp-loaded', function() {
        $('html, body').animate({
            scrollTop: $('.facetwp-page-title')
        }, 500);
    });
})(jQuery);

// Refresh Warning
//
// Since FacetWP is Ajax based, having this there will make it more obvious
// a page is loading...

(function($) {
	$(document).on('facetwp-refresh', function() {
		$('.facetwp-count').html('<i class="fa fa-spinner fa-pulse fa-fw"></i><span class="sr-only">Loading...</span>');
	});
    
    $(document).on('facetwp-loaded', function() {
	   $('.facetwp-count').html('');
	});
    
})(jQuery);