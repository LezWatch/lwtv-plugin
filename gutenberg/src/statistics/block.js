/**
 * BLOCK: Statistics Box
 */

//  Import CSS.
import './editor.scss';

const { registerBlockType } = wp.blocks;
const { createElement, Fragment } = wp.element;
const { InspectorControls } = wp.editor;
const { ServerSideRender, TextControl, PanelBody, SelectControl } = wp.components;

// Register block
registerBlockType( 'lwtv/statistics', {
	title: 'Statistics',
	icon: <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="chart-bar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-chart-bar fa-w-16 fa-3x"><path fill="currentColor" d="M396.8 352h22.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-192 0h22.4c6.4 0 12.8-6.4 12.8-12.8V140.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h22.4c6.4 0 12.8-6.4 12.8-12.8V204.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zM496 400H48V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16zm-387.2-48h22.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8z" class=""></path></svg>,
	category: 'lezwatch',
	className: false,
	attributes: {
		page: {
			type: 'string',
		},
	},

	edit: props => {

		const { attributes: { placeholder }, setAttributes } = props;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ 'Statistics Block Settings' }>
						<SelectControl
							label={ 'Type' }
							value={ props.attributes.page }
							options={ [
								{ label: 'Choose a Statistic type...', value: null },
								{ label: 'Main Page', value: 'main' },
								{ label: 'Actors', value: 'actors' },
								{ label: 'Charatcers', value: 'characters' },
								{ label: 'Death', value: 'death' },
								{ label: 'Formats', value: 'formats' },
								{ label: 'Nations', value: 'nations' },
								{ label: 'Shows', value: 'shows' },
								{ label: 'Stations', value: 'stations' },
							] }
							onChange={ ( value ) => props.setAttributes( { page: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<ServerSideRender
					block='lwtv/statistics'
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
