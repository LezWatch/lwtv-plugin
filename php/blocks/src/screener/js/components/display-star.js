import Star from './svg/star';
import { Fragment } from '@wordpress/element';

// Figure out Trigger Display
export default function DisplayStar({ score }) {
	let color;

	switch (score) {
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

	if ('none' !== score) {
		return (
			<Fragment>
				<span
					data-bs-toggle="tooltip"
					aria-label={`${score} Star Show`}
					title=""
					data-original-title={`${score} Star Show`}
				>
					<button type="button" className="btn btn-info">
						<span
							role="img"
							className={`screener screener-star ${color}`}
						>
							<span className="symbolicon" role="img">
								{Star}
							</span>
						</span>
					</button>
				</span>
			</Fragment>
		);
	}
}
