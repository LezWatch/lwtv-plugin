// Either create a new empty object, or work with the existing one.
window.LWTV_CMB2 = window.LWTV_CMB2 || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	// Cache specific objects from the DOM so we don't look them up repeatedly.
	app.cache = function() {
		app.$ = {};
		app.$.select = $( document.getElementById( 'lezshows_affiliate' ) );
		app.$.field = $( document.getElementById( 'lezshows_affiliateurl' ) );
		app.$.field_container = app.$.field.closest( '.cmb-row');
	};

	app.init = function() {
		// Store/cache our selectors
		app.cache();
		
		// Show the custom container when the selection is 'show-field'
		app.$.select.on( 'change', function( event ) {
			if ( 'url' === $(this).val() ) {
				app.$.field_container.show();
			} else {
				app.$.field_container.hide();
			}
		} ).trigger( 'change' );
	};

	$( document ).ready( app.init );
})( window, document, jQuery, LWTV_CMB2 );