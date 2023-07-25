
// Figure out Trigger Display
export default function DisplayStar( { score } ) {

	let color;

	switch( score ) {
		case 'anti':
			color = 'danger';
			break;
		case 'bronze':
			color = 'danger';
			break;
		case 'silver':
			color = 'warning';
			break;
		default:
			color = 'gold';
	}

	if ( 'none' !== score ) {
		return (
			<Fragment>
				<span
					data-toggle="tooltip"
					aria-label={ `${ score } Star Show` }
					title=""
					data-original-title={ `${ score } Star Show` }
				>
				<button type="button" class="btn btn-info">
					<span
						role="img"
						className={ `screener screener-star ${ color }` }
					><span class="symbolicon" role="img">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>star</title><g id="star"><path d="M24,9.69A1,1,0,0,0,23,9H15.39L13,1.68a1,1,0,0,0-1.9,0L8.61,9H1a1,1,0,0,0-.95.69,1,1,0,0,0,.36,1.12l6.12,4.45L4.05,22.68a1,1,0,0,0,.36,1.12,1,1,0,0,0,1.17,0L12,19.23l6.42,4.58A1,1,0,0,0,19,24a1,1,0,0,0,.59-.2A1,1,0,0,0,20,22.68l-2.48-7.42,6.12-4.45A1,1,0,0,0,24,9.69Z"></path></g></svg>
					</span></span>
				</button>
				</span>
			</Fragment>
		);
	}
}
