import { Fragment } from '@wordpress/element';
import { InnerBlocks } from '@wordpress/block-editor';

export default function Edit( props ) {

	const { className } = props;

	return (
		<Fragment>
			<div
				className={ `${ className } wp-block affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items` }
			>
				<InnerBlocks
					template={ [
						[ 'lwtv/affiliate-item' ]
					] }
					allowedBlocks={ [
						[ 'lwtv/affiliate-item' ]
					] }
					defaultBlock={ 'lwtv/affiliate-item' }
				/>
			</div>
		</Fragment>
	);
}
