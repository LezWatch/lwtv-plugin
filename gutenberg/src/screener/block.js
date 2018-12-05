//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { createBlock, registerBlockType } = wp.blocks;
const { RichText, PlainText } = wp.editor;
const { PanelBody, ToggleControl, RangeControl, SelectControl } = wp.components;

// Display queer
function DisplayQueer( { score } ) {

	return (
		<Fragment>
			<span data-toggle="tooltip" aria-label="How good is this show for queers?" title="" data-original-title="How good is this show for queers?">
				<button type="button" class="btn btn-dark">Queer Score: { `${ score }` }</button>
			</span>
		</Fragment>
	);
}

// Figure out Worth Display
function DisplayWorth( { score } ) {

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

// Figure out Trigger Display
function DisplayTrigger( { score } ) {

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

// Figure out Trigger Display
function DisplayStar( { score } ) {

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

registerBlockType( 'lwtv/screener', {
	title: __( 'Screener Reviews' ),
	icon: 'video-alt',
	category: 'lezwatch',
	keywords: [
		__( 'screener' ),
		__( 'review' ),
	],
	customClassName: false,
	className: false,
	attributes: {
		title: {
			type: 'string',
		},
		summary: {
			type: 'string',
		},
		queer: {
			type: 'string',
			default: '3',
		},
		worthit: {
			type: 'string',
			default: 'meh',
		},
		star: {
			type: 'string',
			default: 'none',
		},
		trigger: {
			type: 'string',
			default: 'none',
		},
	},

	edit: props => {
		const { attributes: { placeholder },
			 className, setAttributes,  } = props;
		const { title, summary, queer, worthit, star, trigger } = props.attributes;

		// Queer Score:
		function onChangeQueer( event ) {
			const selected = event.target.querySelector( 'option:checked' );
			setAttributes( { queer: selected.value } );
			event.preventDefault();
		}

		// Worth It:
		function onChangeWorth( event ) {
			const selected = event.target.querySelector( 'option:checked' );
			setAttributes( { worthit: selected.value } );
			event.preventDefault();
		}

		// Star:
		function onChangeStar( event ) {
			const selected = event.target.querySelector( 'option:checked' );
			setAttributes( { star: selected.value } );
			event.preventDefault();
		}

		// Trigger:
		function onChangeTrigger( event ) {
			const selected = event.target.querySelector( 'option:checked' );
			setAttributes( { trigger: selected.value } );
			event.preventDefault();
		}

		return(
			<Fragment>
			<div className={ `${ className } bd-callout screener-shortcode` }>
				<h5>Screener Review On&nbsp;
					<PlainText
						tagName='em'
						value={ title }
						placeholder={ 'Show Title' }
						onChange={ ( title ) => setAttributes( { title } ) }
					/>
				</h5>
				<RichText
					tagName='p'
					value={ summary }
					placeholder={ 'Content of Review' }
					onChange={ ( summary ) => setAttributes( { summary } ) }
				/>
				<p>
					<span><button type="button" class="btn btn-dark">Queer:
						<form onSubmit={ onChangeQueer }>
							<select value={ queer } onChange={ onChangeQueer }>
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
							</select>
						</form>
					</button></span>

					<span><button type="button" className={ `btn btn-${ worthit }` }>Worth:
						<form onSubmit={ onChangeWorth }>
							<select value={ worthit } onChange={ onChangeWorth }>
								<option value="yes">Yes</option>
								<option value="meh">Meh</option>
								<option value="no">No</option>
								<option value="tbd">TBD</option>
							</select>
						</form>
					</button></span>

					<span><button type="button" className={ `btn btn-${ trigger }` }>Trigger:
						<form onSubmit={ onChangeTrigger }>
							<select value={ trigger } onChange={ onChangeTrigger }>
								<option value="none">None</option>
								<option value="low">Low</option>
								<option value="medium">Medium</option>
								<option value="high">High</option>
							</select>
						</form>
					</button></span>

					<span><button type="button" className={ `btn btn-${ star }` }>Star:
						<form onSubmit={ onChangeStar }>
							<select value={ star } onChange={ onChangeStar }>
								<option value="none">None</option>
								<option value="gold">Gold</option>
								<option value="silver">Silver</option>
								<option value="bronze">Bronze</option>
								<option value="anti">Anti</option>
							</select>
						</form>
					</button></span>
				</p>
			</div>
			</Fragment>
		);
	},

	save: props => {
		const { attributes: { className }, setAttributes } = props;
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
	},
} );
