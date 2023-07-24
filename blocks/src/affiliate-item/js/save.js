export default function Save( { attributes, className } ) {
	const { name, url, descr, imgUrl  } = attributes;

	let returnImage = <img src={ imgUrl } class="card-img-top" alt={ name } />;
	let button = '';
	if ( url ) {
		returnImage = <a href={ url } target="_new" rel="noopener"><img src={ imgUrl } class="card-img-top" alt={ name } /></a>;
		button = <a href={ url } target="_new" class="btn btn-primary" rel="noopener">Shop { name }</a>;
	}

	return (
		<div className={ `${ className } col mb-4` } >
			<div class="card">
				{ returnImage }
				<div class="card-body">
					<h5 class="card-title">{ name }</h5>
					<p class="card-text">{ descr }</p>
					{ button }
				</div>
			</div>
		</div>
	);
}
