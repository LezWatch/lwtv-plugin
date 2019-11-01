window.CMB2_Ajaxified_Dropdown = (function ( window, document, $, undefined ) {
    'use strict';

    var app = {};

    app.cache = function(){
        // The form
        app.$metabox = $( '#show_details_metabox' );
        app.$genres = app.$metabox.find( '#lezshows_tvgenre_primary' );
    };

    app.init = function () {
        app.cache();
        $( 'body' ).on( 'change', '#lezshows_tvgenre', app.change_primary_list )
    };

    app.change_primary_list = function( evt ){
        var that = $( this ),
            new_val = that.val();

        // ajaxurl is already defined in wp-admin, so no special stuff needed.
        $.post( ajaxurl, {
            action: 'get_genres',
            value: new_val
        }, app.handle_response, 'json' );
    };

    app.handle_response = function( resp ){
        console.log( resp );
        if( ! resp.success || ! resp.data ){
            return false;
        }

        // Clear out the <option> tags
        app.$genres.empty();
        app.$genres.append( resp.data );

    };

    $( document ).ready( app.init );

    return app;

})( window, document, jQuery );
