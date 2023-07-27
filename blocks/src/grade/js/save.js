import { Fragment } from '@wordpress/element';
import { RichText } from '@wordpress/block-editor';

export default function Save( props ) {
	const { attributes: { className }, setAttributes } = props;
	const { summary, grade } = props.attributes;

	return (
		<Fragment>
		<div className={ `${ className } bd-callout show-grade` }>
			<div class="grade alert alert-info">{ grade }</div>
			<div class="show-grade body">
				<RichText.Content
					tagName='p'
					value={ summary }
				/>
			</div>
		</div>
		</Fragment>
	);
}
