import { registerBlockType } from '@wordpress/blocks';

import classnames from 'classnames';

const { Fragment } = wp.element;
const { InnerBlocks, InspectorControls } = wp.blockEditor;
const { PanelBody, Button } = wp.components;
const { dispatch } = wp.data;

import './style.scss';
import './editor.scss';

registerBlockType( 'lwtv/affiliate-grid', {
    title: 'Affiliate Grid',
    icon: <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="grip-vertical" class="svg-inline--fa fa-grip-vertical fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M96 32H32C14.33 32 0 46.33 0 64v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zM288 32h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32z"></path></svg>,
    category: 'lezwatch',
    keywords: [ 'affiliates' ],
    className: true,
    description: 'A block for showing a grid of all affiliates.',

    edit: props => {

        const { attributes: { placeholder },
            className, setAttributes, clientId  } = props;

        return (
            <Fragment>
                <div
                    className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items` }
                >
                    <InnerBlocks
                        template={ [
                            [ 'lwtv/affiliate-item' ]
                        ] }
                        allowedBlocks={ [
                            [ 'lwtv/affiliate-item' ]
                        ] }
                        defaultBlock={ 'lwtv/affiliate-item' }
                    />
                </div>
            </Fragment>
        );
    },

    save: props => {
        const { attributes: { className } } = props;

        return (
            <div
                className={ `${ className } affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items` }
            >
                <InnerBlocks.Content />
            </div>
        );
    },

} );
