import { Fragment } from '@wordpress/element';
import { RichText, PlainText } from '@wordpress/block-editor';

export default function Edit( props ) {
	const {
		attributes: { title, summary, queer, worthit, star, trigger },
		setAttributes,
	} = props;

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

	// Title:
	function onTitleChange( value ) {
		setAttributes( { title: value } );
	}

	// Summary:
	function onSummaryChange( value ) {
		setAttributes( { summary: value } );
	}

	return (
		<Fragment>
			<div
				className={
					'wp-block lwtv-screener bd-callout screener-shortcode'
				}
			>
				<h5>
					Screener Review On ...&nbsp;
					<PlainText
						tagName="em"
						value={ title }
						placeholder={ 'Show Title' }
						onChange={ onTitleChange }
					/>
				</h5>
				<RichText
					tagName="p"
					value={ summary }
					placeholder={ 'Content of Review' }
					onChange={ onSummaryChange }
				/>
				<p>
					<span>
						<button type="button" className="btn btn-dark">
							Queer:
							<form onSubmit={ onChangeQueer }>
								<select
									value={ queer }
									onChange={ onChangeQueer }
								>
									<option value="0">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</form>
						</button>
					</span>

					<span>
						<button
							type="button"
							className={ `btn btn-${ worthit }` }
						>
							Worth:
							<form onSubmit={ onChangeWorth }>
								<select
									value={ worthit }
									onChange={ onChangeWorth }
								>
									<option value="yes">Yes</option>
									<option value="meh">Meh</option>
									<option value="no">No</option>
									<option value="tbd">TBD</option>
								</select>
							</form>
						</button>
					</span>

					<span>
						<button
							type="button"
							className={ `btn btn-${ trigger }` }
						>
							Trigger:
							<form onSubmit={ onChangeTrigger }>
								<select
									value={ trigger }
									onChange={ onChangeTrigger }
								>
									<option value="none">None</option>
									<option value="low">Low</option>
									<option value="medium">Medium</option>
									<option value="high">High</option>
								</select>
							</form>
						</button>
					</span>

					<span>
						<button type="button" className={ `btn btn-${ star }` }>
							Star:
							<form onSubmit={ onChangeStar }>
								<select
									value={ star }
									onChange={ onChangeStar }
								>
									<option value="none">None</option>
									<option value="gold">Gold</option>
									<option value="silver">Silver</option>
									<option value="bronze">Bronze</option>
									<option value="anti">Anti</option>
								</select>
							</form>
						</button>
					</span>
				</p>
			</div>
		</Fragment>
	);
}
