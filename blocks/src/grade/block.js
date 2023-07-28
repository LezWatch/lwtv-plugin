/**
 * Block: GRADE!
 *
 * This is the original version. There's an even cooler one.
 */

//  Import CSS.
import './css/style.scss';
import './css/editor.scss';

// Import defaults
import metadata from './block.json';
import Edit from './js/edit';
import Save from './js/save';

import { registerBlockType } from '@wordpress/blocks';

registerBlockType( metadata.name, {
	icon: (
		<svg
			aria-hidden="true"
			data-prefix="far"
			data-icon="star-exclamation"
			role="img"
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 576 512"
			className="svg-inline--fa fa-star-exclamation fa-w-18 fa-3x"
		>
			<path
				fill="currentColor"
				d="M252.5 184.6c-.4-4.6 3.3-8.6 8-8.6h55.1c4.7 0 8.3 4 8 8.6l-6.8 88c-.3 4.2-3.8 7.4-8 7.4h-41.5c-4.2 0-7.7-3.2-8-7.4l-6.8-88zM288 296c-22.1 0-40 17.9-40 40s17.9 40 40 40 40-17.9 40-40-17.9-40-40-40zm257.9-70L440.1 329l25 145.5c4.5 26.2-23.1 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.6-46.4-33.7l25-145.5L30.1 226c-19-18.5-8.5-50.8 17.7-54.6L194 150.2l65.3-132.4c11.8-23.8 45.7-23.7 57.4 0L382 150.2l146.1 21.2c26.2 3.8 36.7 36.1 17.8 54.6zm-56.8-11.7l-139-20.2-62.1-126L225.8 194l-139 20.2 100.6 98-23.7 138.4L288 385.3l124.3 65.4-23.7-138.4 100.5-98z"
			></path>
		</svg>
	),
	edit: Edit,
	save: Save,
} );
