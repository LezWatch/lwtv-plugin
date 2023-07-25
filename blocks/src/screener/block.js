/**
 * BLOCK: Screener
 */

// Import defaults
import metadata from './block.json';
import Edit from './js/edit';
import Save from './js/save';

//  Import CSS.
import './css/style.scss';
import './css/editor.scss';

const { registerBlockType } = wp.blocks;

registerBlockType( metadata.name,
	{
		edit: Edit,
		save: Save,
	}
);
