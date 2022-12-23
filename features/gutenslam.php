<?php
/*
 * GutenSlam is from Marius (Clorith) to make Gutenberg/Block Editor stop being such
 * a dillhole and forget preferences.
 *
 * https://gist.githubusercontent.com/Clorith/3def2df9ddf47e0e7452d28cf76fb134/raw/958d1ce12fe811474a1d5827de3eae2b53c6f09b/disable-fullscreen-snippet.php
 *
 * @version 1.0
 * @package library
*/

class LezWatch_Gutenslam {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_print_footer_scripts', array( $this, 'js_head_print' ), 50 );
	}

	public function js_head_print() {
		$screen = get_current_screen();

		// Only add script in editor views.
		if ( 'edit' !== $screen->parent_base ) {
			return;
		}

		?>

		<script>
			document.addEventListener( 'DOMContentLoaded', () => {

				/**
				* Feature statuses to enforce.
				*
				* $feature => $desiredState
				*
				*/
				let featureDeclaration = {
						'fullscreenMode' : false,
						'welcomeGuide'   : false
					},
					previousState = {};

				// Fetch any user-defined preferences.
				const userPreference = localStorage.getItem( 'featureStatusOverrides' );
				if ( null !== userPreference ) {
					featureDeclaration = JSON.parse( userPreference );
				}

				// Loop over the available features that are defines as overrideable.
				for ( const [ key, value ] of Object.entries( featureDeclaration ) ) {
					previousState[ key ] = value;

					// If the current editor state for the feature does not match what we expect, toggle the feature state.
					if ( value !== wp.data.select( 'core/edit-post' ).isFeatureActive( key ) ) {
						wp.data.dispatch( 'core/edit-post' ).toggleFeature( key );
					}
				}

				// Subscribe to feature changes in the editor.
				wp.data.subscribe( () => {
					let currentState;

					// Loop over our controlled featurestot see if any of them have been set by the user.
					for ( const [ key, value ] of Object.entries( featureDeclaration ) ) {
						currentState = wp.data.select( 'core/edit-post' ).isFeatureActive( key );

						// If the preference has been changed for a setting, update the personal preferences.
						if ( previousState[ key ] !== currentState ) {
							previousState[ key ] = currentState;
						}
					}

					// Store the custom editor preferences for this user in localStorage for next time.
					localStorage.setItem( 'featureStatusOverrides', JSON.stringify( previousState ) );
				} );
			} );
		</script>

		<?php
	}
}

new LezWatch_Gutenslam();
