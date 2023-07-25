const { Fragment } = wp.element;
const { InnerBlocks } = wp.blockEditor;

export default function Edit( props ) {

	const { className } = props;

	if ( className === undefined ) {
		className = 'wp-block';
	}

	return (
		<Fragment>
			<div
				className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items` }
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
