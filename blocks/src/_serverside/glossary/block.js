/**
 * BLOCK: Glossary
 */

//  Import CSS.
import './editor.scss';

const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InspectorControls } = wp.editor;
const { PanelBody, SelectControl } = wp.components;
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'lez-library/glossary', {
	title: 'Glossary',
	icon: <svg aria-hidden="true" data-prefix="fas" data-icon="boxes" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-boxes fa-w-18 fa-2x"><path fill="currentColor" d="M560 288h-80v96l-32-21.3-32 21.3v-96h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16zm-384-64h224c8.8 0 16-7.2 16-16V16c0-8.8-7.2-16-16-16h-80v96l-32-21.3L256 96V0h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16zm64 64h-80v96l-32-21.3L96 384v-96H16c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16z" class=""></path></svg>,
	category: 'lezwatch',
	className: false,
	attributes: {
		taxonomy: {
			type: 'string',
		}
	},

	edit: props => {

		const { attributes, setAttributes } = this.props;
		const { taxonomy } = attributes;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ 'Glossary Block Settings' }>
						<SelectControl
							label={ 'Taxonomy' }
							value={ taxonomy }
							options={ [
								{ label: 'Choose a taxonomy...', value: null },
								{ label: 'Clichés', value: 'lez_cliches' },
								{ label: 'Tropes', value: 'lez_tropes' },
								{ label: 'Formats', value: 'lez_formats' },
								{ label: 'Genres', value: 'lez_genres' },
								{ label: 'Intersections', value: 'lez_intersections' },
							] }
							onChange={ ( value ) => setAttributes( { taxonomy: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<ServerSideRender
					block='lez-library/glossary'
					attributes={ props.attributes }
				/>
			</Fragment>
		);
	},

	save() {
		// Rendering in PHP
		return null;
	},

} );
