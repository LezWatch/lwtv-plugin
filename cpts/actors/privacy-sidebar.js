( function( wp ) {
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginSidebar = wp.editPost.PluginSidebar;
    var el = wp.element.createElement;

    registerPlugin( 'privacy-sidebar', {
        render: function() {
            return el( PluginSidebar,
                {
                    name: 'privacy-sidebar',
                    icon: 'admin-post',
                    title: 'Right to Disappear',
                },
                'Meta field'
            );
        },
    } );
} )( window.wp );

// https://developer.wordpress.org/block-editor/tutorials/plugin-sidebar-0/plugin-sidebar-1-up-and-running/
