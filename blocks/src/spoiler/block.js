/**
 * BLOCK: Spoilers
 *
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { registerBlockType } = wp.blocks;
const { createElement } = wp.element;
const { RichText, InspectorControls } = wp.blockEditor;
const { SelectControl, ToggleControl } = wp.components;

registerBlockType( 'lez-library/spoilers', {
	title: 'Spoiler Warning',
	icon: <svg aria-hidden="true" data-prefix="far" data-icon="flushed" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" class="svg-inline--fa fa-flushed fa-w-16 fa-3x"><path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 448c-110.3 0-200-89.7-200-200S137.7 56 248 56s200 89.7 200 200-89.7 200-200 200zm96-312c-44.2 0-80 35.8-80 80s35.8 80 80 80 80-35.8 80-80-35.8-80-80-80zm0 128c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm0-72c-13.3 0-24 10.7-24 24s10.7 24 24 24 24-10.7 24-24-10.7-24-24-24zm-112 24c0-44.2-35.8-80-80-80s-80 35.8-80 80 35.8 80 80 80 80-35.8 80-80zm-80 48c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm0-72c-13.3 0-24 10.7-24 24s10.7 24 24 24 24-10.7 24-24-10.7-24-24-24zm160 144H184c-13.2 0-24 10.8-24 24s10.8 24 24 24h128c13.2 0 24-10.8 24-24s-10.8-24-24-24z" class=""></path></svg>,
	category: 'lezwatch',
	customClassName: false,
	className: false,
	attributes: {
		content: {
			source: 'children',
			selector: 'div',
			default: 'Warning: This post contains spoilers!'
		}
	},

	description: 'Uh uh uh, no spoilers, sweetie.',

	save: function( props ) {
		const content = props.attributes.content;
		const container = createElement(
			'div', { className: 'alert alert-danger' },
			React.createElement( RichText.Content, { value: content })
		);
		return container;
	},

	edit: function( props ) {
		const content = props.attributes.content;
		const focus = props.focus;

		function onChangeSpoiler( newContent ) {
			props.setAttributes( { content: newContent } );
		}

		const editSpoiler = createElement(
			RichText,
			{
				tagName: 'div',
				className: props.className,
				onChange: onChangeSpoiler,
				value: content,
				focus: focus,
				onFocus: props.setFocus,
			}
		);

		return createElement(
			'div', { className: 'alert alert-danger' },
			editSpoiler
		);
	},
} );
