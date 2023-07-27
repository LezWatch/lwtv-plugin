import { Fragment } from '@wordpress/element';

// Figure out Worth Display
export default function DisplayWorth( { score } ) {

	let color = 'info';
	let icon;
	let svgID = 'meh';
	let svgTitle;

	switch( score ) {
		case 'yes':
			color = 'success';
			svgID = 'thumbs-up';
			icon = 'M3,9a1,1,0,0,0-1,1V21a1,1,0,0,0,2,0V10A1,1,0,0,0,3,9ZM20,9H12.37l1.48-3.89A2.35,2.35,0,0,0,13,2.38,2.06,2.06,0,0,0,10.11,3L6,9H6v9a4,4,0,0,0,4,4h6.7a2,2,0,0,0,1.83-1.19l3.3-7.42a2.06,2.06,0,0,0,.17-.81V11A2,2,0,0,0,20,9Z';
			break;
		case 'no':
			color = 'danger';
			svgID = 'thumbs-down';
			icon = 'M14,2H7.3A2,2,0,0,0,5.47,3.19l-3.3,7.42a2.06,2.06,0,0,0-.17.81V13a2,2,0,0,0,2,2h7.63l-1.48,3.89A2.35,2.35,0,0,0,11,21.62a2.06,2.06,0,0,0,2.93-.57L18,15h0V6A4,4,0,0,0,14,2Zm7,0a1,1,0,0,0-1,1V14a1,1,0,0,0,2,0V3A1,1,0,0,0,21,2Z';
			break;
		case 'tbd':
			color = 'info';
			svgID = 'clock-icon';
			icon = 'M12,5a1,1,0,0,0-1,1V8H9a1,1,0,0,0,0,2h4V6A1,1,0,0,0,12,5ZM23,22H1a1,1,0,1,0,0,2H23a1,1,0,0,0,0-2ZM21,9a9,9,0,1,0-18,0L3,20H21Zm-9,7a7,7,0,1,1,7-7A7,7,0,0,1,12,16Z';
			break;
		default:
			icon = 'M12,0A12,12,0,1,0,24,12,12,12,0,0,0,12,0ZM7.5,8A1.5,1.5,0,1,1,6,9.5,1.5,1.5,0,0,1,7.5,8ZM17,17H7a1,1,0,0,1,0-2H17a1,1,0,0,1,0,2Zm-.5-6A1.5,1.5,0,1,1,18,9.5,1.5,1.5,0,0,1,16.5,11Z';
	}

	svgTitle = <title>{svgID}</title>;

	return (
		<Fragment>
			<span
				data-toggle="tooltip"
				aria-label={ `Is this show worth watching? ${ score }` }
				title=""
				data-original-title={ `Is this show worth watching? ${ score }` }
			>
			<button
				type="button"
				className={ `btn btn-${ color }` }
			>
				Worth It?&nbsp;
				<span
					role="img"
					className={ `screener screener-worthit ${ score }` }
				><span class="symbolicon" role="img">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
					id={svgID}>
					{svgTitle}
					<path
						d={icon}/>
					</svg>
				</span></span>
			</button>
			</span>
		</Fragment>
	);
}
