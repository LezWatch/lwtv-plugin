const { RichText } = wp.blockEditor;
const { createElement } = wp.element;

export default function Edit( props ) {
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
		'div', { className: 'wp-block lwtv-spoilers alert alert-danger' },
		editSpoiler
	);
}
