//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n;
const { Fragment, Component } = wp.element;
const { createBlock, registerBlockType } = wp.blocks;
const { RichText, PlainText } = wp.editor;
const { PanelBody, ToggleControl, SelectControl, InspectorControls } = wp.components;

registerBlockType( 'lwtv/show-grade', {
	title: __( 'Grade Show' ),
	icon: <svg aria-hidden="true" data-prefix="far" data-icon="star-exclamation" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star-exclamation fa-w-18 fa-3x"><path fill="currentColor" d="M252.5 184.6c-.4-4.6 3.3-8.6 8-8.6h55.1c4.7 0 8.3 4 8 8.6l-6.8 88c-.3 4.2-3.8 7.4-8 7.4h-41.5c-4.2 0-7.7-3.2-8-7.4l-6.8-88zM288 296c-22.1 0-40 17.9-40 40s17.9 40 40 40 40-17.9 40-40-17.9-40-40-40zm257.9-70L440.1 329l25 145.5c4.5 26.2-23.1 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.6-46.4-33.7l25-145.5L30.1 226c-19-18.5-8.5-50.8 17.7-54.6L194 150.2l65.3-132.4c11.8-23.8 45.7-23.7 57.4 0L382 150.2l146.1 21.2c26.2 3.8 36.7 36.1 17.8 54.6zm-56.8-11.7l-139-20.2-62.1-126L225.8 194l-139 20.2 100.6 98-23.7 138.4L288 385.3l124.3 65.4-23.7-138.4 100.5-98z" class=""></path></svg>,
	category: 'lezwatch',
	keywords: [
		__( 'grade' ),
		__( 'show' ),
	],
	customClassName: false,
	className: false,
	attributes: {
		summary: {
			type: 'string',
		},
		title: {
			type: 'string',
		},
		grade: {
			type: 'string',
			default: 'C',
		},
		show: {
			type: 'number',
			default: 0,
		},
	},

	edit: props => {
		const { attributes: { placeholder },
			 className, setAttributes,  } = props;
		const { title, summary, grade, show } = props.attributes;

		// Queer Score:
		function onChangeGrade( event ) {
			const selected = event.target.querySelector( 'option:checked' );
			setAttributes( { grde: selected.value } );
			event.preventDefault();
		}

		return(
			<Fragment>
			<div className={ `${ className } bd-callout show-grade` }>
				<div class="show-grade-grade">
					<h5>Grade:</h5>
					<span>
						<form onSubmit={ onChangeGrade }>
							<select value={ grade } onChange={ onChangeGrade }>
								<option value="A+">A+</option>
								<option value="A">A</option>
								<option value="A-">A-</option>
								<option value="B+">B+</option>
								<option value="B">B</option>
								<option value="B-">B+</option>
								<option value="C+">C+</option>
								<option value="C">C</option>
								<option value="C-">C+</option>
								<option value="D">D</option>
								<option value="F">F</option>
							</select>
						</form>
					</span>
				</div>
				<div class="show-grade-body">
					<h5>
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
						placeholder={ 'Grade Summary (could be better...)' }
						onChange={ ( summary ) => setAttributes( { summary } ) }
					/>
				</div>
			</div>
			</Fragment>
		);
	},

	save: props => {
		const { attributes: { className }, setAttributes } = props;
		const { title, summary, grade, show } = props.attributes;

		return (
			<Fragment>
			<div className={ `${ className } bd-callout show-grade` }>
				<h5>
					<div class="grade">{ grade }</div>
					<RichText.Content
						tagName='em'
						value={ title }
					/>
				</h5>
				<RichText.Content
					tagName='p'
					value={ summary }
				/>
			</div>
			</Fragment>
		);
	},
} );
