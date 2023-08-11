/**
 * BLOCK: Pre Publish Checks
 *
 * Checks for:
 * - Featured Image
 * - Custom Excerpt
 *
 * Original Source: https://wordpress.stackexchange.com/questions/339138/add-pre-publish-conditions-to-the-block-editor
 */

import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel } from '@wordpress/edit-post';

import { useState, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

import metadata from './block.json';
import './editor.scss';

// Icons
const iconFail = (
	<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
		<path
			fill="#c0392b"
			d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c-9.4 9.4-9.4 24.6 0 33.9l47 47-47 47c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l47-47 47 47c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-47-47 47-47c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-47 47-47-47c-9.4-9.4-24.6-9.4-33.9 0z"
		/>
	</svg>
);
const iconSuccess = (
	<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
		<path
			fill="#5cb85c"
			d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"
		/>
	</svg>
);

// Build it!
const PrePublishCheckList = () => {
	// Featured Image
	const [featuredImageMessage, setFeaturedImageMessage] = useState('');
	// Post Meta
	const [postExcerptMessage, setPostExcerptMessage] = useState('');

	// Get the current settings
	const { featuredImage, postExcerpt, postContent } = useSelect((select) => {
		return {
			featuredImage:
				select('core/editor').getEditedPostAttribute('featured_media'),
			postExcerpt:
				select('core/editor').getEditedPostAttribute('excerpt'),
			postContent:
				select('core/editor').getEditedPostAttribute('content'),
		};
	});

	// Get the post lock status and save status.
	const { lockPostSaving, unlockPostSaving } = useDispatch('core/editor');

	let styleClass = 'pre-publish-success';

	// Put all the logic in the useEffect hook.
	useEffect(() => {
		let lockPost = false;
		setPostExcerptMessage(iconSuccess);
		setFeaturedImageMessage(iconSuccess);

		// Check the excerpt. Does it exist and is it customized?
		const truncatedPostExcerpt = postExcerpt.slice(0, 50);
		const truncatedPostContent = postContent.slice(0, 50);
		if (
			postExcerpt === 0 ||
			truncatedPostContent.indexOf(truncatedPostExcerpt) === 0
		) {
			lockPost = true;
			setPostExcerptMessage(iconFail);
		}

		// Check the featured image
		if (featuredImage === 0) {
			lockPost = true;
			setFeaturedImageMessage(iconFail);
		}

		if (lockPost === true) {
			styleClass = 'pre-publish-fail';
			lockPostSaving();
		} else {
			unlockPostSaving();
		}
	}, [postExcerpt, featuredImage, postContent]);

	return (
		<PluginPrePublishPanel
			title={'LWTV Publish Checklist'}
			className={styleClass}
			initialOpen={true}
			icon={'info-outline'}
		>
			<p>
				<b>Custom Excerpt:</b> {postExcerptMessage}
			</p>
			<p>
				<b>Featured Image:</b> {featuredImageMessage}
			</p>
		</PluginPrePublishPanel>
	);
};

registerPlugin(metadata.name, { render: PrePublishCheckList });
