/**
 * BLOCK: Screener
 */

// Import defaults
import metadata from './block.json';
import Edit from './js/edit';
import Save from './js/save';
import Icon from '../_common/svg/video';

//  Import CSS.
import './css/style.scss';
import './css/editor.scss';

import { registerBlockType } from '@wordpress/blocks';

registerBlockType( metadata.name, {
	icon: Icon,
	edit: Edit,
	save: Save,
} );
