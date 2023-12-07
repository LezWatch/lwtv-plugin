import { Fragment } from '@wordpress/element';
import Warning from './svg/warning';

// Figure out Trigger Display
export default function DisplayTrigger({ score }) {
	let color;

	switch (score) {
		case 'high':
			color = 'danger';
			break;
		case 'medium':
			color = 'warning';
			break;
		default:
			color = 'info';
	}

	if ('none' !== score) {
		return (
			<Fragment>
				<span
					data-bs-toggle="tooltip"
					aria-label="Warning - This show contains triggers"
					title="Warning - This show contains triggers"
				>
					<button type="button" className={`btn btn-${color}`}>
						<span
							role="img"
							className={`screener screener-warn ${color}`}
						>
							<span className="symbolicon" role="img">
								{Warning}
							</span>
						</span>
					</button>
				</span>
			</Fragment>
		);
	}
}
