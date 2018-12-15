/**
 * External dependencies
 */

const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;
const { SelectControl, PanelBody, ServerSideRender } = wp.components;
const { InspectorControls } = wp.editor;
const { withSelect } = wp.data;

class AuthorProfileBlock extends Component {

	getAuthorsForSelect() {
		const { authors } = this.props;
		return authors.map( ( author ) => {

			return {
				label: author.name,
				value: author.id,
			};

		} );
	}

	render() {

		const { attributes, setAttributes, authors } = this.props;
		const { users, format } = attributes;
		const obj = this.getAuthorsForSelect();
		obj.push( { label: '- Select User -', value: 0 } );

		obj.sort( function( a, b ) {
			return a.value - b.value;
		} );

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ 'Author Profile Settings' }>
					<SelectControl
						label={ 'Author ID' }
						type="number"
						value={ users }
						options={ obj }
						onChange={ ( value ) => setAttributes( { users: value } ) }
					/>
					<SelectControl
						label={ 'Format' }
						type="string"
						value={ format }
						options={ [
							{ label: 'Large', value: 'large' },
							{ label: 'Compact', value: 'compact' },
							{ label: 'Thumbnail', value: 'thumbnail' },
						] }
						onChange={ ( value ) => setAttributes( { format: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
		);

		function isAuthor( author ) {
			return parseInt( author.id ) === parseInt( users );
		}

		const newAuthor = authors.find( isAuthor );

		return (
			<Fragment>
				{ inspectorControls }
				<ServerSideRender
					block='lwtv/author-box'
					attributes={ attributes }
				/>
			</Fragment>
		);
	}
}

export default withSelect( ( select ) => {
	return {
		authors: select( 'core' ).getAuthors(),
	};
} )( AuthorProfileBlock );
