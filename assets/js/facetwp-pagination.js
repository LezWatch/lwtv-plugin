// FacetWP - Pagination Scrolling
//
// Source: https://facetwp.com/how-to-add-pagination-scrolling/
//
// FacetWP is ajax-based, meaning pagination happens without a full page reload. 
// The trade-off is that the browser won’t automatically scroll to the top of 
// the page upon pagination. This can be achieved with a bit of custom js.

(function($) {
    $(document).on('facetwp-loaded', function() {
        $('html, body').animate({
            scrollTop: $('.facetwp-template').offset().top
        }, 500);
    });
})(jQuery);