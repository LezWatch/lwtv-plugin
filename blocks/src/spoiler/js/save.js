const { createElement } = wp.element;
const { RichText } = wp.blockEditor;

export default function Save( props ) {
	const content = props.attributes.content;

	const container = createElement(
		'div', { className: 'alert alert-danger' },
		React.createElement( RichText.Content, { value: content })
	);
	return container;
}
