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

const PrePublishCheckList = () => {
	// Manage the messaging in state.
	const [featuredImageMessage, setFeaturedImageMessage] = useState('');
	const [metaExcerptMessage, setMetaExcerptMessage] = useState('');
	
	// The useSelect hook is better for retrieving data from the store.
	const { featuredImageID, metaExcerpt } = useSelect((select) => {
		return {
			featuredImageID:
				select('core/editor').getEditedPostAttribute('featured_media'),
			metaExcerpt:
				select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ),
		};
	});

	// The useDispatch hook is better for dispatching actions.
	const { lockPostSaving, unlockPostSaving } = useDispatch('core/editor');

	// Put all the logic in the useEffect hook.
	useEffect(() => {
		let lockPost = false;

		// Check if the excerpt exists.
		if (metaExcerpt === 0) {
			lockPost = true;
			setMetaExcerptMessage('Not Set');
		} else {
			setMetaExcerptMessage(' Set');
		}

		// Get the featured image
		if (featuredImageID === 0) {
			lockPost = true;
			setFeaturedImageMessage('Not Set');
		} else {
			setFeaturedImageMessage(' Set');
		}

		if (lockPost === true) {
			lockPostSaving();
		} else {
			unlockPostSaving();
		}
	}, [metaExcerpt, featuredImageID]);

	return (
		<PluginPrePublishPanel title={'Publish Checklist'}>
			<p><b>Excerpt:</b> {metaExcerptMessage}</p>
			<p><b>Featured Image:</b> {featuredImageMessage}</p>
		</PluginPrePublishPanel>
	);
};

registerPlugin('pre-publish-checklist', { render: PrePublishCheckList });