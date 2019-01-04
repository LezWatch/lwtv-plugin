/**
 * Blocks for Custom Post Type Meta Data
 */

//  Import CSS.
import './editor.scss';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { ServerSideRender, DateTimePicker } = wp.components;

registerBlockType( 'lez-library/cpt-meta', {

	title: 'LezWatch.TV Custom Post Type Meta',
	icon: <svg aria-hidden="true" data-prefix="fas" data-icon="alicorn" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-alicorn fa-w-20 fa-3x"><path fill="currentColor" d="M631.98 32H531.73c5.93-6.14 10.4-13.63 12.18-22.36 1.01-4.96-2.88-9.64-7.94-9.64H416c-70.69 0-128 57.31-128 128h-.08c-63.92 0-104.2-36.78-127.66-90.27-3.22-7.35-13.61-7.76-17.04-.5C133.49 57.82 128 80.8 128 105.1c0 67.04 51.01 133.09 128 147.74v3.17c-76.89 0-133.8-49.67-152.67-108.99-5.58 4.17-10.75 8.86-15.33 14.1V160c-48.53 0-88 39.47-88 88v56c0 8.84 7.16 16 16 16h16c8.84 0 16-7.16 16-16v-56c0-13.22 6.87-24.39 16.78-31.68-.21 2.58-.78 5.05-.78 7.68 0 27.64 11.84 52.36 30.54 69.88l-25.72 68.6a63.945 63.945 0 0 0-2.16 37.99l24.85 99.41A15.982 15.982 0 0 0 107.02 512h65.96c10.41 0 18.05-9.78 15.52-19.88l-26.31-105.26 23.84-63.59 102.04 22.33V496c0 8.84 7.16 16 16 16h64c8.84 0 16-7.16 16-16V318.22c19.74-20.19 32-47.75 32-78.22 0-.22-.07-.42-.08-.64V136.89l16 7.11 18.9 37.7c7.45 14.87 25.05 21.55 40.49 15.37l32.55-13.02a31.997 31.997 0 0 0 20.12-29.74L544 83.3l92.42-36.65c6.59-4.38 3.48-14.65-4.44-14.65zM480 96c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z" class=""></path></svg>,
	category: 'lezwatch',
	description: 'A programatically insertable block for actor, character, and show templates.',
	inserter: false,
	attributes: {
		post_id: {
			type: 'int',
		}
	},

	edit: props => {
		const { ClassName, attributes, setAttributes } = props;
		props.attributes.post_id = jQuery('#post_ID').val();
		return (
			<Fragment>
				<ServerSideRender
					block='lez-library/cpt-meta'
					attributes={ props.attributes }
				/>
			</Fragment>
		);
	},

	save: props => {
		return null;
	},
} );
