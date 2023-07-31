/**
 * BLOCK: Glossary
 */

//  Import CSS.
import './editor.scss';
import Icon from '../../_common/svg/glossary';

import { registerBlockType } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/server-side-render';

registerBlockType( 'lez-library/glossary', {
	title: 'Glossary',
	icon: Icon,
	category: 'lezwatch',
	className: false,
	attributes: {
		taxonomy: {
			type: 'string',
		},
	},
	edit: ( props ) => {
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
								{
									label: 'Choose a taxonomy...',
									value: null,
								},
								{
									label: 'Clichés',
									value: 'lez_cliches',
								},
								{
									label: 'Tropes',
									value: 'lez_tropes',
								},
								{
									label: 'Formats',
									value: 'lez_formats',
								},
								{
									label: 'Genres',
									value: 'lez_genres',
								},
								{
									label: 'Intersections',
									value: 'lez_intersections',
								},
							] }
							onChange={ ( value ) =>
								setAttributes( { taxonomy: value } )
							}
						/>
					</PanelBody>
				</InspectorControls>
				<ServerSideRender
					block="lez-library/glossary"
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
