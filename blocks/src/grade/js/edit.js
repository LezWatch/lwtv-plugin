
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { RichText, InspectorControls } from '@wordpress/block-editor';

export default function Edit( props ) {
	const { attributes: { className }, setAttributes,  } = props;
		const { summary, grade } = props.attributes;

		return(
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
							onChange={ ( value ) => props.setAttributes( { grade: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div className={ `${ className } wp-block bd-callout show-grade wp-block` }>
					<div class="show-grade grade">{ grade }</div>
					<div class="show-grade body">
						<RichText
							tagName='p'
							value={ summary }
							placeholder={ 'Summary (could have been better...)' }
							onChange={ ( summary ) => setAttributes( { summary } ) }
						/>
					</div>
				</div>
			</Fragment>
		);
}
