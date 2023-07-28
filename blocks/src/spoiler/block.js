/**
 * BLOCK: Spoilers
 *
 */

// Import defaults
import metadata from './block.json';
import Edit from './js/edit';
import Save from './js/save';
import SVG from './js/svg';

//  Import CSS.
import './css/style.scss';
import './css/editor.scss';

import { registerBlockType } from '@wordpress/blocks';

registerBlockType( metadata.name, {
	icon: SVG,
	edit: Edit,
	save: Save,
} );
