/**
 * BLOCK: TV Show Calendar
 */

//  Import CSS.
import './editor.scss';
import Icon from '../../_common/svg/calendar-week';

import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';

// Register block
registerBlockType('lwtv/tvshow-calendar', {
	title: 'TV Shows Calendar',
	icon: Icon,
	category: 'lezwatch',
	keywords: ['calendar', 'tv shows'],
	className: false,
	edit() {
		return <ServerSideRender block="lwtv/tvshow-calendar" />;
	},
	save() {
		// This is a display only block. No saving.
		return null;
	},
});
