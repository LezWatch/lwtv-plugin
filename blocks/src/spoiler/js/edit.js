import { RichText } from '@wordpress/block-editor';
import { createElement } from '@wordpress/element';

export default function Edit(props) {
	const { attributes: className, focus, setFocus } = props;
	const { content } = props.attributes;

	function onChangeSpoiler(newContent) {
		props.setAttributes({ content: newContent });
	}

	const editSpoiler = createElement(RichText, {
		tagName: 'div',
		className,
		onChange: onChangeSpoiler,
		value: content,
		focus,
		onFocus: setFocus,
	});

	return createElement(
		'div',
		{ className: 'wp-block-lez-library-spoilers alert alert-danger' },
		editSpoiler
	);
}
