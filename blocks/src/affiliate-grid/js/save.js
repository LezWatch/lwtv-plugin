const { InnerBlocks } = wp.blockEditor;

export default function Save( props ) {
	const { attributes: { className } } = props;

	if ( className === undefined ) {
		className = 'wp-block';
	}

	return (
		<div
			className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items` }
		>
			<InnerBlocks.Content />
		</div>
	);
}
