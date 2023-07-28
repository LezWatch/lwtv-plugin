import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { RichText, InspectorControls } from '@wordpress/block-editor';

export default function Edit( props ) {
	const {
		attributes: { className },
		setAttributes,
	} = props;
	const { summary, grade } = props.attributes;

	// Summary:
	function onSummaryChange( value ) {
		setAttributes( { summary: value } );
	}

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ 'Grade Block Settings' }>
					<SelectControl
						label={ 'Grade' }
						value={ grade }
						options={ [
							{ label: 'Pick a grade...', value: null },
							{ label: 'A+', value: 'A+' },
							{ label: 'A', value: 'A' },
							{ label: 'A-', value: 'A-' },
							{ label: 'B+', value: 'B+' },
							{ label: 'B', value: 'B' },
							{ label: 'B-', value: 'B-' },
							{ label: 'C+', value: 'C+' },
							{ label: 'C', value: 'C' },
							{ label: 'C-', value: 'C-' },
							{ label: 'D', value: 'D' },
							{ label: 'F', value: 'F' },
						] }
						onChange={ ( value ) =>
							props.setAttributes( { grade: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div
				className={ `${ className } wp-block bd-callout show-grade wp-block` }
			>
				<div className="show-grade grade">{ grade }</div>
				<div className="show-grade body">
					<RichText
						tagName="p"
						value={ summary }
						placeholder={ 'Summary (could have been better...)' }
						onChange={ onSummaryChange }
					/>
				</div>
			</div>
		</Fragment>
	);
}
