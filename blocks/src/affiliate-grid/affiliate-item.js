import { registerBlockType } from '@wordpress/blocks';

const { Fragment } = wp.element;
const { InspectorControls, MediaUpload, MediaUploadCheck, RichText } = wp.blockEditor;
const { PanelBody, Button, ResponsiveWrapper, TextControl } = wp.components;
const { withSelect } = wp.data;

import './style.scss';

registerBlockType( 'lwtv/affiliate-item', {
	title: 'Affiliate Grid Item',

	category: 'lezwatch',
    parent: [ 'lwtv/affiliate-grid' ],
	icon: 'editor-rtl',
	category: 'layout',
	className: false,
	attributes: {
		name: {
			type: 'string',
			default: 'Example Affiliate',
		},
		url: {
			type: 'string',
		},
		descr: {
			type: 'string',
			default: 'We are cool! Shop here!',
		},
        imgUrl: {
            type: 'string',
            default: 'https://via.placeholder.com/600x320.png?text=LezWatchTV+Placeholder+Affiliate+Image',
        }
	},
    description: 'An individual affiliate.',

    edit( { attributes, setAttributes, isSelected, className } ) {
		const { name, url, descr, imgUrl } = attributes;

        function selectImage(value) {
            console.log(value);
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
                <div className="col-sm-6">
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
	},

    save( { attributes, className } ) {
		const { name, url, descr, imgUrl  } = attributes;

        let returnImage = <img src={ imgUrl } class="card-img-top" alt={ name } />;
        let button = '';
        if ( url ) {
            returnImage = <a href={ url } target="_new"><img src={ imgUrl } class="card-img-top" alt={ name } /></a>;
            button = <a href={ url } target="_new" class="btn btn-primary">Shop { name }</a>;
        }

		return (
            <div className={ `${ className } col mb-4` } >
                <div class="card">
                  { returnImage }
                  <div class="card-body">
                  <h5 class="card-title">{ name }</h5>
                  <p class="card-text">{ descr }</p>
                  { button }
                  </div>
                </div>
            </div>
		);
	},
} );
