/**
 * External dependencies
 */

const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;
const { SelectControl, PanelBody } = wp.components;
const { InspectorControls } = wp.blockEditor;
const { withSelect } = wp.data;

import ServerSideRender from '@wordpress/server-side-render';

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

		const authorList = this.getAuthorsForSelect();

		// Customize List
		authorList.push( { label: '- Select User -', value: 0 } );
		authorList.sort( function( a, b ) {
			return a.value - b.value;
		} );

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ 'Author Profile Settings' }>
					<SelectControl
						label={ 'Author ID' }
						type="number"
						value={ users }
						options={ authorList }
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
