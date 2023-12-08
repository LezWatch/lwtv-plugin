import { Fragment } from '@wordpress/element';

// Display queer
export default function DisplayQueer({ score }) {
	return (
		<Fragment>
			<span
				data-bs-toggle="tooltip"
				aria-label="How good is this show for queers?"
				title=""
				data-original-title="How good is this show for queers?"
			>
				<button type="button" className="btn btn-dark">
					Queer Score: {`${score}`}
				</button>
			</span>
		</Fragment>
	);
}
