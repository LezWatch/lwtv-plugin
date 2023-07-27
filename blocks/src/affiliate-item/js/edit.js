import { Fragment } from '@wordpress/element';
import { InspectorControls, MediaUpload, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes, className } ) {

	const { name, url, descr, imgUrl } = attributes;

	function selectImage(value) {
		setAttributes({
			imgUrl: value.sizes.full.url,
		})
	}

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ 'Affiliate Item Settings' }>
				<TextControl
					label={ 'Affiliate Link' }
					help={ 'Link to affiliate network (with any variables needed)' }
					onChange={ ( url ) => setAttributes( { url } ) }
					value={ url }
			/>
				</PanelBody>
			</InspectorControls>
			<div className={ `${ className } col mb-4` } >
				<div class="card">
					<div class="card-body">
						<MediaUpload
						onSelect={selectImage}
						render={ ({open}) => {
							return <img
								src={ imgUrl }
								onClick={open}
							/>;
						}}
						/>
						<RichText
							tagName='h5'
							className='card-title'
							value={ name }
							onChange={ ( name ) => setAttributes( { name } ) }
						/>
						<RichText
							tagName='p'
							className='card-text'
							value={ descr }
							onChange={ ( descr ) => setAttributes( { descr } ) }
						/>
					</div>
				</div>
			</div>
		</Fragment>
	);
}
