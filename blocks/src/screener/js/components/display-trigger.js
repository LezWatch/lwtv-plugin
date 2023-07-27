import { Fragment } from '@wordpress/element';

// Figure out Trigger Display
export default function DisplayTrigger( { score } ) {

	let color;

	switch( score ) {
		case 'high':
			color = 'danger';
			break;
		case 'medium':
			color = 'warning';
			break;
		default:
			color = 'info';
	}

	if ( 'none' !== score ) {
		return (
			<Fragment>
				<span data-toggle="tooltip" aria-label="Warning - This show contains triggers" title="Warning - This show contains triggers">
				<button
					type="button"
					className={ `btn btn-${ color }` }
				>
					<span
						role="img"
						className={ `screener screener-warn ${ color }` }
					><span class="symbolicon" role="img">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>warning</title><g id="warning"><path d="M23.51,17.5,15.18,2.85a3.66,3.66,0,0,0-6.36,0L.49,17.5A3.68,3.68,0,0,0,3.67,23H20.33A3.68,3.68,0,0,0,23.51,17.5ZM11,7a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v7a1,1,0,0,1-1,1h0a1,1,0,0,1-1-1Zm1,13a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,12,20Z"></path></g></svg>
					</span></span>
				</button>
				</span>
			</Fragment>
		);
	}
}
