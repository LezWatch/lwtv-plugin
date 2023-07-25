const { Fragment } = wp.blockEditor;
const { RichText } = wp.blockEditor;

// Internal Components
import DisplayQueer from './components/display-queer';
import DisplayWorth from './components/display-worth';
import DisplayTrigger from './components/display-trigger';
import DisplayStar from './components/display-star';

export default function Save( props ) {
	const { attributes: { className } } = props;
	const { title, summary, queer, worthit, star, trigger } = props.attributes;


	return (
		<Fragment>
		<div className={ `${ className } bd-callout screener-shortcode` }>
			<h5>Screener Review On&nbsp;
				<RichText.Content
					tagName='em'
					value={ title }
				/>
			</h5>
			<RichText.Content
				tagName='p'
				value={ summary }
			/>

			<p>
				<DisplayQueer score={ queer } />
				&nbsp;
				<DisplayWorth score={ worthit } />
				&nbsp;
				<DisplayTrigger score={ trigger } />
				&nbsp;
				<DisplayStar score={ star } />
			</p>

		</div>
		</Fragment>
	);
}
