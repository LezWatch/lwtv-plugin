import { registerBlockType } from '@wordpress/blocks';

import classnames from 'classnames';
import memoize from 'memize';
import times from 'lodash/times';

const { Fragment } = wp.element;
const { InnerBlocks, InspectorControls } = wp.blockEditor;
const { PanelBody, Button } = wp.components;
const { dispatch } = wp.data;

const getItemsTemplate = memoize( ( items ) => {
	return times( items, () => [ 'lwtv/affiliate-item' ] );
} );

import './style.scss';

registerBlockType( 'lwtv/affiliate-grid', {
	title: 'Affiliate Grid',
	icon: <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="grip-vertical" class="svg-inline--fa fa-grip-vertical fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M96 32H32C14.33 32 0 46.33 0 64v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zM288 32h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32z"></path></svg>,
	category: 'lezwatch',
	keywords: [ 'affiliates' ],
	className: false,
	description: 'A block for showing a grid of all affiliates.',
    attributes: {
		items: {
			type: 'number',
			default: 2,
		},
	},

	edit: props => {

		const { attributes: { placeholder },
			className, setAttributes, clientId  } = props;
		const { items } = props.attributes;

		/**
		 * Add Item
		 */
		const onAddItem = () => {
			setAttributes( { items: parseInt(`${ items }`)+1 } )
			const block = createBlock( 'lwtv/affiliate-item' )
			dispatch( 'core/block-editor' ).insertBlock( block, items, clientId )
		}

		return (
			<Fragment>
				<div
					className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items-${ items }` }
				>
					<InnerBlocks
						template={ getItemsTemplate( items ) }
						allowedBlocks={ [
							[ 'lwtv/affiliate-item' ]
						] }
						defaultBlock={ 'lwtv/affiliate-item' }
					/>
				</div>
			</Fragment>
		);
	},

	save: props => {
		const { attributes: { className } } = props;
		const { items } = props.attributes;

		return (
			<div
				className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items-${ items }` }
			>
				<InnerBlocks.Content />
			</div>
		);
	},

} );
