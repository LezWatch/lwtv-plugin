
const { PostFeaturedImage } = wp.editor;

export default function Edit() {
	return (
		<div className={ `wp-block` }>
			<PostFeaturedImage />
		</div>
	);
}
