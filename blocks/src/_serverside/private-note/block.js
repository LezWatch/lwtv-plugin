/**
 * BLOCK: Private Note
 *
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

import Icon from '../../_common/svg/private-note';

import { registerBlockType } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';
import { InnerBlocks } from '@wordpress/block-editor';

registerBlockType( 'lez-library/private-note', {
	title: 'Private Note',
	icon: Icon,
	category: 'lezwatch',
	description:
		'Private notes, only seen by logged in users. It will be stripped from all published pages.',

	edit: ( props ) => {
		const { className } = props;
		return (
			<Fragment>
				<div className={ `${ className } alert alert-warning` }>
					<InnerBlocks
						template={ [
							[
								'core/paragraph',
								{
									content:
										'All content in this block will be invisible to non-logged-in visitors (delete this and replace it).',
								},
							],
						] }
						templateLock={ false }
					/>
				</div>
			</Fragment>
		);
	},

	save: ( props ) => {
		const {
			attributes: { className },
		} = props;
		return (
			<div className={ `${ className } alert alert-warning` }>
				<InnerBlocks.Content />
			</div>
		);
	},
} );
