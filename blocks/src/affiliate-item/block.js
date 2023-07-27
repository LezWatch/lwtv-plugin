/**
 * BLOCK: Affiliate Grid Item
 *
 */

// Import defaults
import metadata from './block.json';
import Edit from './js/edit';
import Save from './js/save';

import { registerBlockType } from '@wordpress/blocks';

registerBlockType( metadata.name,
	{
		edit: Edit,
		save: Save,
	}
);
