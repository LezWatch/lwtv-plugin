/**
 * Custom Template for the Actor CPT only
 */

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;

registerBlockType( 'lez-library/actor-cpt', {

	title: 'LezWatchTV Actors',
	icon: 'editor-rtl',
	category: 'lezwatch',
	description: 'A programatically insertable block for actor templates.',
	//inserter: false,
	attributes: {
		lezactors_imdb: {
			type: 'string',
			source: 'meta',
			meta: 'lezactors_imdb'
		},
		lezactors_wikipedia: {
			type: 'string',
			source: 'meta',
			meta: 'lezactors_wikipedia'
		},
	},

	edit: function( props ) {
		const { className, attributes } = props;

		function onChangeImdb( event ) {
			setAttributes( { lezactors_imdb: event.target.value } );
		}

		function onChangeImdb( event ) {
			setAttributes( { lezactors_wikipedia: event.target.value } );
		}

		return (
			<Fragment>
				<table width="90%">
					<tr><th>IMDb ID</th> <th>WikiPedia URL</th></tr>
					<tr>
						<td><input value={ attributes.lezactors_imdb } onChange={ onChangeImdb } type="text" /></td>
						<td><input value={ attributes.lezactors_wikipedia } onChange={ onChangeWiki } type="text" /></td>
					</tr>
				</table>
			</Fragment>
		);
	},

	save: function( props ) {
		return;
	},
} );
