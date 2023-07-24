
const { PostFeaturedImage } = wp.editor;

export default function Edit( props ) {
	const { className } = props;
	return (
		<div className={ `${ className } wp-block` }>
			<PostFeaturedImage />
		</div>
	);
}
