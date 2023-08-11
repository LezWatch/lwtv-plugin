/**
 * BLOCK: Author Box
 */

// Import CSS.
import './editor.scss';

import { registerBlockType } from '@wordpress/blocks';

// Edit as it's own file
import edit from './edit';
import Icon from '../../_common/svg/team-member';

// Register block
registerBlockType('lwtv/author-box', {
	title: 'Team Member',
	icon: Icon,
	category: 'lezwatch',
	className: false,
	attributes: {
		users: {
			type: 'string',
		},
		format: {
			type: 'string',
			default: 'large',
		},
	},
	edit,
	save() {
		// Rendering in PHP
		return null;
	},
});
