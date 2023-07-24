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
		icon: <svg aria-hidden="true" data-prefix="far" data-icon="camera-retro" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-camera-retro fa-w-16 fa-3x"><path fill="currentColor" d="M154 80H38c-3.3 0-6-2.7-6-6V38c0-3.3 2.7-6 6-6h116c3.3 0 6 2.7 6 6v36c0 3.3-2.7 6-6 6zm358 0v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48h136l33.6-44.8C226.7 39.1 240.9 32 256 32h208c26.5 0 48 21.5 48 48zm-48 64H48v288h416V144zm0-64H256l-12 16h220V80zm-88 208c0-66.2-53.8-120-120-120s-120 53.8-120 120 53.8 120 120 120 120-53.8 120-120zm-48 0c0 39.7-32.3 72-72 72s-72-32.3-72-72 32.3-72 72-72 72 32.3 72 72zm-96 0c0-13.2 10.8-24 24-24 8.8 0 16-7.2 16-16s-7.2-16-16-16c-30.9 0-56 25.1-56 56 0 8.8 7.2 16 16 16s16-7.2 16-16z" class=""></path></svg>,
		edit: Edit,
		save: Save,
	}
);
