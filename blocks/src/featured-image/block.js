/**
 * BLOCK: Featured Image
 *
 */

import metadata from './block.json';

import Edit from './js/edit';
import Save from './js/save';

// Import CSS.
import './css/style.scss';
import './css/editor.scss';

const { registerBlockType } = wp.blocks;

var withImageSize = function() {
	return 'large';
};

wp.hooks.addFilter( 'editor.PostFeaturedImage.imageSize', metadata.name, withImageSize );

registerBlockType( metadata.name,
	{
		edit: Edit,
		save: Save,
	}
);
