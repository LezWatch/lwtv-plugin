import { RichText } from '@wordpress/block-editor';
import { createElement } from '@wordpress/element';

export default function Save( props ) {
	const content = props.attributes.content;

	const container = createElement(
		'div', { className: 'wp-block-lez-library-spoilers alert alert-danger' },
		React.createElement( RichText.Content, { value: content })
	);
	return container;
}
